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

use TYPO3\CMS\Core\Database\QueryGenerator;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * @package mindshape_seo
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class PageService implements SingletonInterface
{
    /**
     * @var \TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder
     */
    protected $uriBuilder;

    /**
     * @var \TYPO3\CMS\Frontend\Page\PageRepository
     */
    protected $pageRepository;

    /**
     * @return PageService
     * @throws \Mindshape\MindshapeSeo\Service\Exception
     */
    public function __construct()
    {
        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        /** @var ConfigurationManager $configurationManager */
        $configurationManager = $objectManager->get(ConfigurationManager::class);
        $this->pageRepository = $objectManager->get(PageRepository::class);

        if ('FE' === TYPO3_MODE) {
            /** @var ContentObjectRenderer $contentObjectRenderer */
            $contentObjectRenderer = $objectManager->get(ContentObjectRenderer::class);
        } elseif ('BE' === TYPO3_MODE) {
            /** @var TypoScriptFrontendController $typoScriptFrontendController */
            $typoScriptFrontendController = $objectManager->get(
                TypoScriptFrontendController::class,
                $GLOBALS['TYPO3_CONF_VARS'],
                GeneralUtility::_GET('id'),
                GeneralUtility::_GET('type')
            );

            $typoScriptFrontendController->sys_page = $this->pageRepository;
            $typoScriptFrontendController->initTemplate();

            $contentObjectRenderer = $objectManager->get(ContentObjectRenderer::class, $typoScriptFrontendController);
        } else {
            throw new Exception('Illegal TYPO3_MODE');
        }

        $configurationManager->setContentObject($contentObjectRenderer);
        $this->uriBuilder = $objectManager->get(UriBuilder::class);
        $this->uriBuilder->injectConfigurationManager($configurationManager);

    }

    /**
     * Creates a link to a single page
     *
     * @param int $pageId
     * @param int $sysLanguageUid
     * @return string
     */
    public function getPageLink($pageId, $sysLanguageUid = 0)
    {
        return $this->uriBuilder
            ->reset()
            ->setTargetPageUid($pageId)
            ->setArguments(
                0 < $sysLanguageUid ? array('L' => $sysLanguageUid) : array()
            )
            ->buildFrontendUri();
    }

    /**
     * @param int $pageUid
     * @return array
     */
    public function getPage($pageUid)
    {
        return $this->pageRepository->getPage($pageUid);
    }

    /**
     * @return array
     */
    public function getCurrentPage()
    {
        return $this->getPage($GLOBALS['TSFE']->id);
    }

    public function getRootline()
    {
        $pages = array();

        foreach ($GLOBALS['TSFE']->rootLine as $index => $page) {
            $pages[] = $this->getPage($page['uid']);
        }

        return $pages;
    }

    /**
     * @param int $pageUid
     * @return array
     */
    public function getSubPageUidsFromPageUid($pageUid)
    {
        /** @var QueryGenerator $queryGenerator */
        $queryGenerator = GeneralUtility::makeInstance(QueryGenerator::class);
        return GeneralUtility::trimExplode(
            ',',
            $queryGenerator->getTreeList($pageUid, 9999999, 0, 1)
        );
    }

    /**
     * @param int $pageUid
     * @return array
     */
    public function getSubPagesFromPageUid($pageUid)
    {
        $pages = array();

        foreach ($this->getSubPageUidsFromPageUid($pageUid) as $uid) {
            $pages[] = $this->pageRepository->getPage($uid);
        }

        return $pages;
    }
}
