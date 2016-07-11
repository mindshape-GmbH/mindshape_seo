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
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Resource\ProcessedFile;
use TYPO3\CMS\Core\Utility\GeneralUtility;
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
     * @var array
     */
    protected $settings;

    /**
     * @param PageRenderer $pageRenderer
     * @return HeaderDataService
     */
    public function __construct(PageRenderer $pageRenderer)
    {
        $this->pageRenderer = $pageRenderer;

        /** @var DatabaseConnection $databaseConnection */
        $databaseConnection = $GLOBALS['TYPO3_DB'];

        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->pageService = $objectManager->get(PageService::class);
        $this->standaloneTemplateRendererService = $objectManager->get(StandaloneTemplateRendererService::class);

        $page = $this->pageService->getPage($GLOBALS['TSFE']->id);
        $currentDomain = GeneralUtility::getIndpEnv('HTTP_HOST');

        $this->settings = array(
            'domain' => array(),
            'sitename' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename'],
            'page' => array(
                'uid' => $page['uid'],
                'title' => $page['title'],
                'meta' => array(
                    'author' => $page['author'],
                    'contact' => $page['author_email'],
                    'description' => $page['description'],
                    'robots' => array(
                        'noindex' => (bool) $page['mindshapeseo_no_index'],
                        'nofollow' => (bool) $page['mindshapeseo_no_follow'],
                    ),
                ),
                'facebook' => array(
                    'title' => $page['mindshapeseo_ogtitle'],
                    'url' => $page['mindshapeseo_ogurl'],
                    'description' => $page['mindshapeseo_ogdescription'],
                ),
                'seo' => array(
                    'noIndex' => (bool) $page['mindshapeseo_no_index'],
                    'noFollow' => (bool) $page['mindshapeseo_no_follow'],
                    'disableTitleAttachment' => (bool) $page['mindshapeseo_disable_title_attachment'],
                ),
            ),
        );

        $result = $databaseConnection->exec_SELECTgetSingleRow(
            '*',
            'tx_mindshapeseo_domain_model_configuration t',
            't.domain = "' . Configuration::DEFAULT_DOMAIN . '" OR t.domain = "' . $currentDomain . '"',
            '',
            'domain DESC'
        );

        if (is_array($result)) {
            $this->settings['domain'] = array(
                'url' => $this->pageService->getPageLink(
                    $GLOBALS['TSFE']->rootLine[0]['uid']
                ),
                'googleAnalytics' => $result['google_analytics'],
                'piwikUrl' => $result['piwik_url'],
                'piwikIdSite' => $result['piwik_idsite'],
                'titleAttachment' => $result['title_attachment'],
                'addHreflang' => (bool) $result['add_hreflang'],
                'facebookDefaultImage' => $result['facebook_default_image'],
                'addJsonLd' => (bool) $result['add_jsonld'],
                'json-ld' => array(
                    'customUrl' => $result['jsonld_custom_url'],
                    'type' => $result['jsonld_type'],
                    'telephone' => $result['jsonld_telephone'],
                    'fax' => $result['jsonld_fax'],
                    'email' => $result['jsonld_email'],
                    'sameAs' => $result['jsonld_same_as'],
                    'logo' => $result['jsonld_logo'],
                    'address' => array(
                        'locality' => $result['jsonld_address_locality'],
                        'postalcode' => $result['jsonld_address_postalcode'],
                        'street' => $result['jsonld_address_street'],
                    ),
                ),
            );
        }

        if (0 === (int) $page['mindshapeseo_ogimage']) {
            $this->settings['page']['facebook']['image'] = $this->settings['domain']['facebookDefaultImage'];
        } else {
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

                $this->settings['page']['facebook']['image'] = GeneralUtility::getIndpEnv('TYPO3_REQUEST_HOST') . '/' . $processedFile->getPublicUrl();
            } else {
                $this->settings['page']['facebook']['image'] = $this->settings['domain']['facebookDefaultImage'];
            }
        }
    }

    /**
     * @return void
     */
    public function manipulateHeaderData()
    {
        $this->attachTitleAttachment();
        $this->addMetaData();
        $this->addFacebookData();

        if ($this->settings['domain']['addHreflang']) {
            $this->addHreflang();
        }

        if ($this->settings['domain']['addJsonLd']) {
            $this->addJsonLd();
        }

        if ('' !== $this->settings['domain']['googleAnalytics']) {
            $this->addGoogleAnalytics();
        }

        if (
            '' === $this->settings['domain']['googleAnalytics'] &&
            '' !== $this->settings['domain']['piwikUrl'] &&
            '' !== $this->settings['domain']['piwikIdSite']
        ) {
            $this->addPiwik();
        }
    }

    /**
     * @return void
     */
    protected function attachTitleAttachment()
    {
        if (
            '' !== $this->settings['domain']['titleAttachment'] &&
            !$this->settings['page']['seo']['disableTitleAttachment']
        ) {
            $this->pageRenderer->setTitle(
                $this->settings['page']['title'] . ' | ' . $this->settings['domain']['titleAttachment']
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
            'o.pid = ' . $this->settings['page']['uid']
        );

        foreach ($result as $language) {
            $this->pageRenderer->addHeaderData(
                $this->renderHreflang(
                    $this->pageService->getPageLink($this->settings['page']['uid'], $language['uid']),
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
            'og:site_name' => $this->settings['sitename'],
            'og:url' => $this->settings['page']['facebook']['url'],
            'og:title' => $this->settings['page']['facebook']['title'],
            'og:description' => $this->settings['page']['facebook']['description'],
            'og:image' => $this->settings['page']['facebook']['image'],
        );

        $this->addMetaDataArray($metaData);
    }

    protected function addMetaData()
    {
        $robots = array();

        if ($this->settings['page']['meta']['robots']['noindex']) {
            $robots[] = 'noindex';
        }

        if ($this->settings['page']['meta']['robots']['nofollow']) {
            $robots[] = 'nofollow';
        }

        $metaData = array(
            'author' => $this->settings['page']['meta']['author'],
            'contact' => $this->settings['page']['meta']['contact'],
            'description' => $this->settings['page']['meta']['description'],
            'robots' => implode(',', $robots),
        );

        $this->addMetaDataArray($metaData);
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
        $view = $this->standaloneTemplateRendererService->getView('Analytics', 'Google');
        $view->assign('analyticsId', $this->settings['domain']['googleAnalytics']);

        $this->pageRenderer->addHeaderData(
            $view->render()
        );
    }

    /**
     * @return void
     */
    protected function addPiwik()
    {
        $view = $this->standaloneTemplateRendererService->getView('Analytics', 'Piwik');
        $view->assignMultiple(array(
            'piwikUrl' => $this->settings['domain']['piwikUrl'],
            'piwikIdSite' => $this->settings['domain']['piwikIdSite'],
        ));

        $this->pageRenderer->addHeaderData(
            $view->render()
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
            'url' => '' !== $this->settings['domain']['json-ld']['customUrl'] ?
                $this->settings['domain']['json-ld']['customUrl'] :
                GeneralUtility::getIndpEnv('HTTP_HOST'),
        );
    }

    /**
     * @return array
     */
    protected function renderJsonLdInformation()
    {
        return array(
            '@context' => 'http://schema.org',
            '@type' => $this->settings['domain']['json-ld']['type'],
            'url' => $this->settings['domain']['url'],
            'telephone' => $this->settings['domain']['json-ld']['telephone'],
            'faxNumber' => $this->settings['domain']['json-ld']['fax'],
            'email' => $this->settings['domain']['json-ld']['email'],
            'logo' => $this->settings['domain']['json-ld']['logo'],
            'address' => array(
                '@type' => 'PostalAddress',
                'addressLocality' => $this->settings['domain']['json-ld']['address']['locality'],
                'postalcode' => $this->settings['domain']['json-ld']['address']['postalcode'],
                'streetAddress' => $this->settings['domain']['json-ld']['address']['street'],
            ),
        );
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

        $rootLineUids = $GLOBALS['TSFE']->rootLine;
        array_pop($rootLineUids);
        $rootLineUids = array_reverse($rootLineUids);

        foreach ($rootLineUids as $index => $page) {
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
                    '@id' => $this->pageService->getPageLink($page['uid']),
                    'name' => $page['title'],
                ),
            );
        }

        return $breadcrumb;
    }
}
