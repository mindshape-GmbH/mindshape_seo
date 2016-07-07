<?php
namespace Mindshape\MindshapeSeo\Service;

/***************************************************************
 *
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

use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Resource\ProcessedFile;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Service\ImageService;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * @package mindshape_seo
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class HeaderDataService
{
    const DEFAULT_DOMAIN = '*';

    /**
     * @var \TYPO3\CMS\Core\Page\PageRenderer
     */
    protected $pageRenderer;

    /**
     * @var \TYPO3\CMS\Frontend\Page\PageRepository
     */
    protected $pageRepository;

    /**
     * @var \Mindshape\MindshapeSeo\Service\PageService
     */
    protected $pageService;

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
        $this->pageRepository = $objectManager->get(PageRepository::class);
        $this->uriBuilder = $objectManager->get(UriBuilder::class);
        $this->pageService = $objectManager->get(PageService::class);

        $page = $this->pageRepository->getPage($GLOBALS['TSFE']->id);
        $currentDomain = GeneralUtility::getIndpEnv('HTTP_HOST');

        $ogimage = '';

        if (0 < $page['mindshapeseo_ogimage']) {
            /** @var FileRepository $fileRepository */
            $fileRepository = $objectManager->get(FileRepository::class);
            /** @var ImageService $imageService */
            $imageService = $objectManager->get(ImageService::class);
            $files = $fileRepository->findByRelation('pages', 'ogimage', $page['uid']);
            /** @var FileReference $file */
            $file = $files[0];
            /** @var ProcessedFile $processedFile */
            $processedFile = $imageService->applyProcessingInstructions(
                $file,
                array(
                    'crop' => $file->getReferenceProperties()['crop'],
                )
            );

            $ogimage = GeneralUtility::getIndpEnv('TYPO3_REQUEST_HOST') . '/' . $processedFile->getPublicUrl();
        }

        $this->settings = array(
            'domain' => array(),
            'sitename' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename'],
            'page' => array(
                'uid' => $page['uid'],
                'title' => $page['title'],
                'facebook' => array(
                    'title' => $page['mindshapeseo_ogtitle'],
                    'url' => $page['mindshapeseo_ogurl'],
                    'image' => $ogimage,
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
            'tx_mindshapeseo_configuration t',
            't.domain = "' . self::DEFAULT_DOMAIN . '" OR t.domain = "' . $currentDomain . '"'
        );

        if (is_array($result)) {
            $this->settings['domain'] = array(
                'googleAnalytics' => trim($result['google_analytics']),
                'titleAttachment' => trim($result['title_attachment']),
                'addHreflang' => (bool) $result['add_hreflang'],
            );
        }
    }

    /**
     * @return void
     */
    public function manipulateHeaderData()
    {
        $this->attachTitleAttachment();
        $this->addHreflang();
        $this->addFacebookData();
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
        if (!$this->settings['domain']['addHreflang']) {
            return;
        }

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

        foreach ($metaData as $property => $content) {
            if ('' !== $content) {
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
}
