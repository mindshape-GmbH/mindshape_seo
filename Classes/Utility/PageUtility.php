<?php
namespace Mindshape\MindshapeSeo\Utility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2021 Daniel Dorndorf <dorndorf@mindshape.de>, mindshape GmbH
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

use Mindshape\MindshapeSeo\Service\PageService;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * @package mindshape_seo
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class PageUtility
{
    /**
     * @return int
     */
    public static function getCurrentPageUid()
    {
        return 0 < (int) $GLOBALS['TSFE']->id
            ? (int) $GLOBALS['TSFE']->id
            : (int) GeneralUtility::_GET('id');
    }

    /**
     * @return array
     * @throws \TYPO3\CMS\Extbase\Object\Exception
     */
    public static function getCurrentPage()
    {
        /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        /** @var \Mindshape\MindshapeSeo\Service\PageService $pageService */
        $pageService = $objectManager->get(PageService::class);

        return $pageService->getCurrentPage();
    }

    /**
     * @param $pageUid
     * @return array
     * @throws \TYPO3\CMS\Extbase\Object\Exception
     */
    public static function getPage($pageUid)
    {
        /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        /** @var \Mindshape\MindshapeSeo\Service\PageService $pageService */
        $pageService = $objectManager->get(PageService::class);

        return $pageService->getPage($pageUid);
    }

    /**
     * @return \TYPO3\CMS\Core\Page\PageRenderer
     * @throws \TYPO3\CMS\Extbase\Object\Exception
     */
    public static function getPageRenderer()
    {
        /** @var \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController $typoScriptFrontendController */
        $typoScriptFrontendController = $GLOBALS['TSFE'];

        try {
            $typoScriptFrontendControllerReflectionClass = new \ReflectionClass($typoScriptFrontendController);

            $pageRendererPropertyReflection = $typoScriptFrontendControllerReflectionClass->getProperty('pageRenderer');
            $pageRendererPropertyReflection->setAccessible(true);

            /** @var \TYPO3\CMS\Core\Page\PageRenderer $pageRenderer */
            $pageRenderer = $pageRendererPropertyReflection->getValue($typoScriptFrontendController);

            if ($pageRenderer instanceof PageRenderer) {
                return $pageRenderer;
            }
        } catch (\ReflectionException $exception) {}

        /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $pageRenderer = $objectManager->get(PageRenderer::class);

        return $pageRenderer;
    }
}
