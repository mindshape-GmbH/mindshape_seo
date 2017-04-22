<?php
namespace Mindshape\MindshapeSeo\Service;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017 Daniel Dorndorf <dorndorf@mindshape.de>, mindshape GmbH
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use Mindshape\MindshapeSeo\Domain\Model\Configuration;
use Mindshape\MindshapeSeo\Domain\Repository\ConfigurationRepository;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Service\ImageService;
use TYPO3\CMS\Lang\LanguageService as CoreLangugeService;

/**
 * @package mindshape_seo
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class HeaderDataService implements SingletonInterface
{
    /**
     * @var \TYPO3\CMS\Core\Page\PageRenderer
     */
    protected $pageRenderer;

    /**
     * @var \Mindshape\MindshapeSeo\Service\PageService
     */
    protected $pageService;

    /**
     * @var \Mindshape\MindshapeSeo\Service\StandaloneTemplateRendererService
     */
    protected $standaloneTemplateRendererService;

    /**
     * @var \Mindshape\MindshapeSeo\Domain\Repository\ConfigurationRepository
     */
    protected $configurationRepository;

    /**
     * @var \Mindshape\MindshapeSeo\Domain\Model\Configuration
     */
    protected $domainConfiguration;

    /**
     * @var array
     */
    protected $params = array();

    /**
     * @var array
     */
    protected $jsonLd = array();

    /**
     * @var array
     */
    protected $currentPageMetaData;

    /**
     * @var string
     */
    protected $currentDomainUrl;

    /**
     * @var string
     */
    protected $currentSitename;

    /**
     * @return \Mindshape\MindshapeSeo\Service\HeaderDataService
     */
    public function __construct()
    {
        /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->pageService = $objectManager->get(PageService::class);
        $this->standaloneTemplateRendererService = $objectManager->get(StandaloneTemplateRendererService::class);
        $this->configurationRepository = $objectManager->get(ConfigurationRepository::class);
        $this->pageRenderer = $objectManager->get(PageRenderer::class);

        $page = $this->pageService->getCurrentPage();

        $this->currentPageMetaData = $this->pageService->getPageMetaData(
            $page['uid'],
            $this->pageService->getCurrentSysLanguageUid()
        );

        $currentDomain = GeneralUtility::getIndpEnv('HTTP_HOST');

        $this->domainConfiguration = $this->configurationRepository->findByDomain($currentDomain, true);

        $this->currentDomainUrl = $this->pageService->getPageLink(
            $GLOBALS['TSFE']->rootLine[0]['uid'],
            true,
            $this->pageService->getCurrentSysLanguageUid()
        );

        if ($this->domainConfiguration instanceof Configuration) {
            $this->addJsonLd();
        }

        if (
            $this->domainConfiguration instanceof Configuration &&
            false === empty($this->domainConfiguration->getSitename())
        ) {
            $this->currentSitename = $this->domainConfiguration->getSitename();
        } else {
            $this->currentSitename = $GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename'];
        }

        if (0 < (int) $page['mindshapeseo_ogimage']) {
            /** @var \TYPO3\CMS\Core\Resource\FileRepository $fileRepository */
            $fileRepository = $objectManager->get(FileRepository::class);
            /** @var \TYPO3\CMS\Extbase\Service\ImageService $imageService */
            $imageService = $objectManager->get(ImageService::class);
            $files = $fileRepository->findByRelation('pages', 'ogimage', $page['uid']);

            if (0 < count($files)) {
                /** @var \TYPO3\CMS\Core\Resource\FileReference $file */
                $file = $files[0];
                /** @var \TYPO3\CMS\Core\Resource\ProcessedFile $processedFile */
                $processedFile = $imageService->applyProcessingInstructions(
                    $file,
                    array(
                        'crop' => $file->getReferenceProperties()['crop'],
                    )
                );

                $this->currentPageMetaData['facebook']['image'] = GeneralUtility::getIndpEnv('TYPO3_REQUEST_HOST') . '/' . $processedFile->getPublicUrl();
            }
        } elseif ($this->domainConfiguration instanceof Configuration) {
            if (null !== $this->domainConfiguration->getFacebookDefaultImage()) {
                $this->currentPageMetaData['facebook']['image'] = $this->domainConfiguration->getFacebookDefaultImage()->getOriginalResource()->getPublicUrl();
            }
        }
    }

    /**
     * @return void
     */
    public function manipulateHeaderData()
    {
        $this->addBaseUrl();
        $this->addMetaData();
        $this->addFacebookData();

        if (null !== $this->currentPageMetaData['canonicalUrl']) {
            $this->addCanonicalUrl();
        }

        if ($this->domainConfiguration instanceof Configuration) {
            if ($this->domainConfiguration->getAddHreflang()) {
                $this->addHreflang();
            }

            if ($this->domainConfiguration->getAddJsonld()) {
                $this->renderJsonLd();
            }

            if ($this->domainConfiguration->getAddJsonldBreadcrumb()) {
                $this->addJsonLdBreadcrumb();
            }

            if ($this->domainConfiguration->getAddAnalytics()) {
                if ('' !== $this->domainConfiguration->getGoogleAnalytics()) {
                    $this->addGoogleAnalytics();
                }

                if ('' !== $this->domainConfiguration->getGoogleTagmanager()) {
                    $this->addGoogleTagmanager();
                }

                if (
                    '' === $this->domainConfiguration->getGoogleAnalytics() &&
                    '' !== $this->domainConfiguration->getPiwikUrl() &&
                    '' !== $this->domainConfiguration->getPiwikIdsite()
                ) {
                    $this->addPiwik();
                }
            }
        }
    }

    /**
     * @param array $headerData
     * @return void
     */
    public function addTitle(array &$headerData = null)
    {
        $headerDataWithTitle = preg_grep('#<title>(.*)</title>#i', $headerData);

        $title = reset($headerDataWithTitle);

        if (false === $title) {
            $title = $this->currentPageMetaData['title'];
        } else {
            preg_match('#<title>(.*)<\/title>#im', $title, $titleMatches);

            $title = $titleMatches[1];

            $key = reset(array_keys($headerDataWithTitle));

            $headerData[$key] = preg_replace(
                '#(<title>)(.*)(<\/title>)#i',
                '',
                $headerData[$key]
            );
        }

        if (
            $this->domainConfiguration instanceof Configuration &&
            !$this->currentPageMetaData['disableTitleAttachment'] &&
            !empty($this->domainConfiguration->getTitleAttachment())
        ) {
            if ($this->domainConfiguration->getTitleAttachmentPosition() === Configuration::TITLE_ATTACHMENT_POSITION_PREFIX) {
                $title = $this->domainConfiguration->getTitleAttachment() . ' ' . trim($this->domainConfiguration->getTitleAttachmentSeperator()) . ' ' . $title;
            } else {
                $title = $title . ' ' . trim($this->domainConfiguration->getTitleAttachmentSeperator()) . ' ' . $this->domainConfiguration->getTitleAttachment();
            }
        }

        $this->pageRenderer->setTitle($title);
    }

    /**
     * @param string $metaTag
     * @return void
     */
    public function addMetaTag($metaTag)
    {
        if (true === version_compare(VersionNumberUtility::getNumericTypo3Version(), '7.6.15', '<=')) {
            $this->pageRenderer->addHeaderData($metaTag);
        } else {
            $this->pageRenderer->addMetaTag($metaTag);
        }
    }

    /**
     * @return array
     */
    public function getJsonLd()
    {
        return $this->jsonLd;
    }

    /**
     * @param array $jsonLd
     * @return void
     */
    public function setJsonLd(array $jsonLd)
    {
        $this->jsonLd = $jsonLd;
    }

    /**
     * @return void
     */
    protected function addBaseUrl()
    {
        $currentSysLanguageUid = $GLOBALS['TSFE']->sys_language_uid;
        $rootline = $this->pageService->getRootline();

        $rootpage = array_pop($rootline);

        $rootpages = array_filter($rootline, function ($page) {
            return (bool) $page['is_siteroot'];
        });

        if (0 < count($rootpages)) {
            $rootpage = $rootpages[1];
        }

        $this->pageRenderer->setBaseUrl(
            $this->pageService->getPageLink($rootpage['uid'], true, $currentSysLanguageUid)
        );
    }

    /**
     * @return void
     */
    protected function addCanonicalUrl()
    {
        $this->pageRenderer->addHeaderData(
            '<link rel="canonical" href="' . $this->currentPageMetaData['canonicalUrl'] . '"/>'
        );
    }

    /**
     * @return void
     */
    protected function addHreflang()
    {
        /** @var \TYPO3\CMS\Core\Database\DatabaseConnection $databaseConnection */
        $databaseConnection = $GLOBALS['TYPO3_DB'];
        /** @var \TYPO3\CMS\Lang\LanguageService $test */
        $languageService = $GLOBALS['LANG'];

        $result = $databaseConnection->exec_SELECTgetRows(
            'l.*',
            'sys_language l INNER JOIN pages_language_overlay o ON l.uid = o.sys_language_uid',
            'o.pid = ' . $this->currentPageMetaData['uid']
        );

        $this->pageRenderer->addHeaderData(
            $this->renderHreflang(
                $this->pageService->getPageLink($this->currentPageMetaData['uid'], true),
                'x-default'
            )
        );

        if ($languageService instanceof CoreLangugeService) {
            $this->pageRenderer->addHeaderData(
                $this->renderHreflang(
                    $this->pageService->getPageLink($this->currentPageMetaData['uid'], true),
                    $languageService->lang
                )
            );
        }

        foreach ($result as $language) {
            $this->pageRenderer->addHeaderData(
                $this->renderHreflang(
                    $this->pageService->getPageLink($this->currentPageMetaData['uid'], true, $language['uid']),
                    $language['language_isocode']
                )
            );
        }
    }

    /**
     * @param string $url
     * @param string $languageKey
     * @return string
     */
    protected function renderHreflang($url, $languageKey)
    {
        return '<link rel="alternate" href="' . $url . '" hreflang="' . $languageKey . '"/>';
    }

    /**
     * @return void
     */
    protected function addFacebookData()
    {
        $metaData = array(
            'og:site_name' => $this->currentSitename,
            'og:url' => $this->currentPageMetaData['facebook']['url'],
            'og:title' => $this->currentPageMetaData['facebook']['title'],
            'og:description' => $this->currentPageMetaData['facebook']['description'],
        );

        if (array_key_exists('image', $this->currentPageMetaData['facebook'])) {
            $metaData['og:image'] = $this->currentPageMetaData['facebook']['image'];
        }

        $this->addMetaDataArray($metaData, 'property');
    }

    protected function addMetaData()
    {
        $robots = array();

        if (
            (
                $this->currentPageMetaData['meta']['robots']['noindex'] ||
                $this->currentPageMetaData['meta']['robots']['noindexInherited']
            ) &&
            !in_array('noindex', $robots, true)
        ) {
            $robots[] = 'noindex';
        }

        if (
            (
                $this->currentPageMetaData['meta']['robots']['nofollow'] ||
                $this->currentPageMetaData['meta']['robots']['nofollowInherited']
            ) &&
            !in_array('nofollow', $robots, true)
        ) {
            $robots[] = 'nofollow';
        }

        $metaData = array(
            'author' => $this->currentPageMetaData['meta']['author'],
            'contact' => $this->currentPageMetaData['meta']['contact'],
            'description' => $this->currentPageMetaData['meta']['description'],
            'robots' => implode(',', $robots),
        );

        $this->addMetaDataArray($metaData);
    }

    /**
     * @param array $metaData
     * @param string $nameAttribute
     * @return void
     */
    protected function addMetaDataArray(array $metaData, $nameAttribute = 'name')
    {
        foreach ($metaData as $name => $content) {
            if (!empty($content)) {
                $this->addMetaTag(
                    $this->renderMetaTag($name, $content, $nameAttribute)
                );
            }
        }
    }

    /**
     * @param string $name
     * @param string $content
     * @param string $nameAttribute
     * @return string
     */
    protected function renderMetaTag($name, $content, $nameAttribute = 'name')
    {
        return '<meta ' . $nameAttribute . '="' . $name . '" content="' . $content . '">';
    }

    /**
     * @return void
     */
    protected function addGoogleAnalytics()
    {
        $this->pageRenderer->addHeaderData(
            $this->standaloneTemplateRendererService->render('Analytics', 'Google', array(
                'analyticsId' => $this->domainConfiguration->getGoogleAnalytics(),
            ))
        );
    }

    /**
     * @return void
     */
    protected function addGoogleTagmanager()
    {
        $this->pageRenderer->addHeaderData(
            $this->standaloneTemplateRendererService->render('Analytics', 'GoogleTagmanagerHead', array(
                'tagmanagerId' => $this->domainConfiguration->getGoogleTagmanager(),
            ))
        );

        $this->pageRenderer->addFooterData(
            $this->standaloneTemplateRendererService->render('Analytics', 'GoogleTagmanagerBody', array(
                'tagmanagerId' => $this->domainConfiguration->getGoogleTagmanager(),
            ))
        );
    }

    /**
     * @return void
     */
    protected function addPiwik()
    {
        $this->pageRenderer->addHeaderData(
            $this->standaloneTemplateRendererService->render('Analytics', 'Piwik', array(
                'piwikUrl' => $this->domainConfiguration->getPiwikUrl(),
                'piwikIdSite' => $this->domainConfiguration->getPiwikIdsite(),
            ))
        );
    }

    /**
     * @return void
     */
    protected function addJsonLd()
    {
        if ($this->domainConfiguration->getAddJsonld()) {
            $this->jsonLd[] = $this->renderJsonWebsiteName();
            $this->jsonLd[] = $this->renderJsonLdInformation();
        }
    }

    /**
     * @return void
     */
    protected function renderJsonLd()
    {
        if (0 < count($this->jsonLd)) {
            $this->pageRenderer->addHeaderData(
                '<script type="application/ld+json" data-ignore="1">' . json_encode($this->jsonLd) . '</script>'
            );
        }
    }

    /**
     * @return array
     */
    protected function renderJsonWebsiteName()
    {
        return array(
            '@context' => 'http://schema.org',
            '@type' => 'WebSite',
            'url' => '' !== $this->domainConfiguration->getJsonldCustomUrl() ?
                $this->domainConfiguration->getJsonldCustomUrl() :
                GeneralUtility::getIndpEnv('TYPO3_REQUEST_HOST'),
        );
    }

    /**
     * @return array
     */
    protected function renderJsonLdInformation()
    {
        $jsonld = array(
            '@context' => 'http://schema.org',
            '@type' => $this->domainConfiguration->getJsonldType(),
            'url' => $this->currentDomainUrl,
        );

        if (false === empty($this->domainConfiguration->getJsonldName())) {
            $jsonld['name'] = $this->domainConfiguration->getJsonldName();
        }

        if (false === empty($this->domainConfiguration->getJsonldTelephone())) {
            $jsonld['telephone'] = $this->domainConfiguration->getJsonldTelephone();
        }

        if (false === empty($this->domainConfiguration->getJsonldFax())) {
            $jsonld['faxNumber'] = $this->domainConfiguration->getJsonldFax();
        }

        if (false === empty($this->domainConfiguration->getJsonldEmail())) {
            $jsonld['email'] = $this->domainConfiguration->getJsonldEmail();
        }

        if (
            false === empty($this->domainConfiguration->getJsonldAddressLocality()) &&
            false === empty($this->domainConfiguration->getJsonldAddressPostalcode()) &&
            false === empty($this->domainConfiguration->getJsonldAddressStreet())
        ) {
            $jsonld['address'] = array(
                '@type' => 'PostalAddress',
            );

            if (false === empty($this->domainConfiguration->getJsonldAddressLocality())) {
                $jsonld['address']['addressLocality'] = $this->domainConfiguration->getJsonldAddressLocality();
            }


            if (false === empty($this->domainConfiguration->getJsonldAddressPostalcode())) {
                $jsonld['address']['postalcode'] = $this->domainConfiguration->getJsonldAddressPostalcode();
            }


            if (false === empty($this->domainConfiguration->getJsonldAddressStreet())) {
                $jsonld['address']['streetAddress'] = $this->domainConfiguration->getJsonldAddressStreet();
            }
        }

        if (
            null !== $this->domainConfiguration->getJsonldLogo() &&
            Configuration::JSONLD_TYPE_PERSON !== $this->domainConfiguration->getJsonldType()
        ) {
            $jsonld['logo'] = GeneralUtility::getIndpEnv('TYPO3_SITE_URL') . $this->domainConfiguration
                    ->getJsonldLogo()
                    ->getOriginalResource()
                    ->getPublicUrl();
        }

        $socialMediaLinks = array(
            'facebook' => $this->domainConfiguration->getJsonldSameAsFacebook(),
            'twitter' => $this->domainConfiguration->getJsonldSameAsTwitter(),
            'googleplus' => $this->domainConfiguration->getJsonldSameAsGoogleplus(),
            'instagram' => $this->domainConfiguration->getJsonldSameAsInstagram(),
            'youtube' => $this->domainConfiguration->getJsonldSameAsYoutube(),
            'linkedin' => $this->domainConfiguration->getJsonldSameAsLinkedin(),
            'xing' => $this->domainConfiguration->getJsonldSameAsXing(),
            'printerest' => $this->domainConfiguration->getJsonldSameAsPrinterest(),
            'soundcloud' => $this->domainConfiguration->getJsonldSameAsSoundcloud(),
            'tumblr' => $this->domainConfiguration->getJsonldSameAsTumblr(),
        );

        foreach ($socialMediaLinks as $socialMediaLink) {
            if (!empty($socialMediaLink)) {
                if (!is_array($jsonld['sameAs'])) {
                    $jsonld['sameAs'] = array();
                }

                $jsonld['sameAs'][] = $socialMediaLink;
            }
        }

        return $jsonld;
    }

    /**
     * @return void
     */
    protected function addJsonLdBreadcrumb()
    {
        $jsonLdbreadcrumb = $this->renderJsonLdBreadcrum();

        if (0 < count($jsonLdbreadcrumb['itemListElement'])) {
            $this->pageRenderer->addFooterData(
                '<script type="application/ld+json" data-ignore="1">' . json_encode($jsonLdbreadcrumb) . '</script>'
            );
        }
    }

    /**
     * @return array
     */
    protected function renderJsonLdBreadcrum()
    {
        $breadcrumb = array(
            '@context' => 'http://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => array(),
        );

        foreach ($this->pageService->getRootlineReverse(null, true) as $index => $page) {
            if (
                1 !== (int) $page['doktype'] &&
                4 !== (int) $page['doktype']
            ) {
                continue;
            }

            $breadcrumb['itemListElement'][] = array(
                '@type' => 'ListItem',
                'position' => $index + 1,
                'item' => array(
                    '@id' => $this->pageService->getPageLink(
                        $page['uid'],
                        true,
                        $this->pageService->getCurrentSysLanguageUid()
                    ),
                    'name' => $page['title'],
                ),
            );
        }

        return $breadcrumb;
    }
}
