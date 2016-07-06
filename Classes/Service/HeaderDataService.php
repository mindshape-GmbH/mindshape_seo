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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * @package mindshape_seo
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class HeaderDataService
{
    const DEFAULT_TITLE = '<title>|</title>';

    /**
     * @var \TYPO3\CMS\Core\Page\PageRenderer
     */
    protected $pageRenderer;

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

        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        /** @var PageRepository $pageRepository */
        $pageRepository = $objectManager->get(PageRepository::class);
        /** @var DatabaseConnection $databaseConnection */
        $databaseConnection = $GLOBALS['TYPO3_DB'];

        $page = $pageRepository->getPage($GLOBALS['TSFE']->id);
        $currentDomain = GeneralUtility::getIndpEnv('HTTP_HOST');

        $ogimage = '';

        if (0 < $page['mindshapeseo_ogimage']) {
            $result = $databaseConnection->exec_SELECTgetSingleRow(
                'identifier',
                'sys_file s INNER JOIN sys_file_reference f ON f.uid_local = s.uid',
                'f.tablenames = "pages" AND f.fieldname = "ogimage"'
            );

            if (is_array($result)) {
                $ogimage = $result['identifier'];
            }
        }

        $this->settings = array(
            'domainSettings' => array(),
            'pageSettings' => array(
                'originalTitle' => $page['title'],
                'facebook' => array(
                    'title' => $page['mindshapeseo_ogtitle'],
                    'url' => $page['mindshapeseo_ogurl'],
                    'image' => GeneralUtility::getIndpEnv('TYPO3_REQUEST_HOST') . $ogimage,
                    'description' => $page['mindshapeseo_ogdescription'],
                ),
                'seo' => array(
                    'noIndex' => (bool) $page['mindshapeseo_no_index'],
                    'noFollow' => (bool) $page['mindshapeseo_no_follow'],
                    'disableTitleAttachment' => (bool) $page['mindshapeseo_disable_title_attachment'],
                ),
            ),
        );

        $domains = $databaseConnection->exec_SELECTgetRows(
            '*',
            'tx_mindshapeseo_configuration t',
            't.domain = "*" OR t.domain = "' . $currentDomain . '"'
        );

        foreach ($domains as $domain) {
            $this->settings['domainSettings'] = array(
                'googleAnalytics' => $domain['google_analytics'],
                'titleAttachment' => $domain['title_attachment'],
                'addHreflang' => (bool) $domain['add_hreflang'],
            );
        }
    }

    /**
     * @return void
     */
    public function manipulateHeaderData()
    {
        $this->attachTitleAttachment();
    }

    /**
     * @return void
     */
    protected function attachTitleAttachment()
    {
        if (!$this->settings['pageSettings']['seo']['disableTitleAttachment']) {
            $this->pageRenderer->setTitle(
                $this->settings['pageSettings']['originalTitle'] . ' | ' . $this->settings['domainSettings']['titleAttachment']
            );
        }
    }
}
