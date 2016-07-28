<?php
namespace Mindshape\MindshapeSeo\Service;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Daniel Dorndorf <dorndorf@mindshape.de>, mindshape GmbH
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
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Resource\ProcessedFile;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Service\ImageService;

/**
 * @package mindshape_seo
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class HeaderDataService
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
     * @var string
     */
    protected $titleAttachmentSeperator = '|';

    /**
     * @param PageRenderer $pageRenderer
     * @return HeaderDataService
     */
    public function __construct(PageRenderer $pageRenderer)
    {
        $this->pageRenderer = $pageRenderer;

        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->pageService = $objectManager->get(PageService::class);
        $this->standaloneTemplateRendererService = $objectManager->get(StandaloneTemplateRendererService::class);
        $this->configurationRepository = $objectManager->get(ConfigurationRepository::class);
        /** @var ConfigurationManager $configurationManager */
        $configurationManager = $objectManager->get(ConfigurationManager::class);

        $settings = $configurationManager->getConfiguration(ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'mindshape_seo');
        $this->titleAttachmentSeperator = trim($settings['titleAttachmentSeperator']);

        $page = $this->pageService->getCurrentPage();

        $this->currentPageMetaData = $this->pageService->getPageMetaData($page['uid']);

        $this->currentSitename = $GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename'];

        $currentDomain = GeneralUtility::getIndpEnv('HTTP_HOST');

        $this->domainConfiguration = $this->configurationRepository->findByDomain($currentDomain, true);

        $this->currentDomainUrl = $this->pageService->getPageLink(
            $GLOBALS['TSFE']->rootLine[0]['uid'],
            true
        );

        if (0 < (int) $page['mindshapeseo_ogimage']) {
            /** @var FileRepository $fileRepository */
            $fileRepository = $objectManager->get(FileRepository::class);
            /** @var ImageService $imageService */
            $imageService = $objectManager->get(ImageService::class);
            $files = $fileRepository->findByRelation('pages', 'ogimage', $page['uid']);

            if (0 < count($files)) {
                /** @var FileReference $file */
                $file = $files[0];
                /** @var ProcessedFile $processedFile */
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
        $this->addMetaData();
        $this->addFacebookData();

        if (0 < $this->currentPageMetaData['canonicalPageUid']) {
            $this->addCanonicalUrl();
        }

        if ($this->domainConfiguration instanceof Configuration) {
            $this->attachTitleAttachment();

            if ($this->domainConfiguration->getAddHreflang()) {
                $this->addHreflang();
            }

            if ($this->domainConfiguration->getAddJsonld()) {
                $this->addJsonLd();
            }

            if ('' !== $this->domainConfiguration->getGoogleAnalytics()) {
                $this->addGoogleAnalytics();
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

    /**
     * @return void
     */
    protected function addCanonicalUrl()
    {
        $this->pageRenderer->addHeaderData(
            '<link rel="canonical" href="' .
            $this->pageService->getPageLink(
                $this->currentPageMetaData['canonicalPageUid'],
                true,
                $GLOBALS['TSFE']->sys_language_uid
            ) .
            '"/>'
        );
    }

    /**
     * @return void
     */
    protected function attachTitleAttachment()
    {
        if (
            !$this->currentPageMetaData['disableTitleAttachment'] &&
            '' !== $this->domainConfiguration->getTitleAttachment()
        ) {
            $this->pageRenderer->setTitle(
                $this->currentPageMetaData['title'] . ' ' . $this->titleAttachmentSeperator . ' ' . $this->domainConfiguration->getTitleAttachment()
            );
        } else {
            $this->pageRenderer->setTitle(
                $this->currentPageMetaData['title']
            );
        }
    }

    /**
     * @return void
     */
    protected function addHreflang()
    {
        /** @var DatabaseConnection $databaseConnection */
        $databaseConnection = $GLOBALS['TYPO3_DB'];

        $result = $databaseConnection->exec_SELECTgetRows(
            '*',
            'sys_language l INNER JOIN pages_language_overlay o ON l.uid = o.sys_language_uid',
            'o.pid = ' . $this->currentPageMetaData['uid']
        );

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

        $this->addMetaDataArray($metaData);
    }

    protected function addMetaData()
    {
        $robots = array();

        if (
            !$this->currentPageMetaData['meta']['robots']['noindex'] ||
            !$this->currentPageMetaData['meta']['robots']['nofollow']
        ) {
            $robots = $this->getParentRobotsMetaData();
        }

        if (
            $this->currentPageMetaData['meta']['robots']['noindex'] &&
            !in_array('noindex', $robots, true)
        ) {
            $robots[] = 'noindex';
        }

        if (
            $this->currentPageMetaData['meta']['robots']['nofollow'] &&
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
     * @return array
     */
    protected function getParentRobotsMetaData()
    {
        $robots = array();

        $noindex = false;
        $nofollow = false;

        foreach ($this->pageService->getRootline() as $page) {
            if (!$noindex && $page['mindshapeseo_no_index_recursive']) {
                $noindex = true;

                if ($page['mindshapeseo_no_index']) {
                    $robots[] = 'noindex';
                }
            }

            if (!$nofollow && $page['mindshapeseo_no_follow_recursive']) {
                $nofollow = true;

                if ($page['mindshapeseo_no_follow']) {
                    $robots[] = 'nofollow';
                }
            }
        }

        return $robots;
    }

    /**
     * @param array $metaData
     * @return void
     */
    protected function addMetaDataArray(array $metaData)
    {
        foreach ($metaData as $property => $content) {
            if (!empty($content)) {
                $this->pageRenderer->addHeaderData(
                    $this->renderMetaTag($property, $content)
                );
            }
        }
    }

    /**
     * @param string $property
     * @param string $content
     * @return string
     */
    protected function renderMetaTag($property, $content)
    {
        return '<meta property="' . $property . '" content="' . $content . '"/>';
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
        $jsonLdArray = array();

        $jsonLdArray[] = $this->renderJsonWebsiteName();
        $jsonLdArray[] = $this->renderJsonLdInformation();
        $jsonLdbreadcrumb = $this->renderJsonLdBreadcrum();

        if (0 < count($jsonLdbreadcrumb['itemListElement'])) {
            $jsonLdArray[] = $jsonLdbreadcrumb;
        }

        if (0 < count($jsonLdArray)) {
            $this->pageRenderer->addHeaderData(
                '<script type="application/ld+json" data-ignore="1">' . json_encode($jsonLdArray) . '</script>'
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
                GeneralUtility::getIndpEnv('HTTP_HOST'),
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
            'telephone' => $this->domainConfiguration->getJsonldTelephone(),
            'faxNumber' => $this->domainConfiguration->getJsonldFax(),
            'email' => $this->domainConfiguration->getJsonldEmail(),
            'address' => array(
                '@type' => 'PostalAddress',
                'addressLocality' => $this->domainConfiguration->getJsonldAddressLocality(),
                'postalcode' => $this->domainConfiguration->getJsonldAddressPostalcode(),
                'streetAddress' => $this->domainConfiguration->getJsonldAddressStreet(),
            ),
        );

        if (null !== $this->domainConfiguration->getJsonldLogo()) {
            $jsonld['logo'] = $this->domainConfiguration
                ->getJsonldLogo()
                ->getOriginalResource()
                ->getPublicUrl();
        }

        return $jsonld;
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

        foreach ($this->pageService->getRootlineReverse() as $index => $page) {
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
                    '@id' => $this->pageService->getPageLink($page['uid'], true),
                    'name' => $page['title'],
                ),
            );
        }

        return $breadcrumb;
    }
}
