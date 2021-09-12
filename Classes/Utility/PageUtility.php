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

/**
 * @package mindshape_seo
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class PageUtility
{
    /**
     * @return array
     * @throws \TYPO3\CMS\Core\Context\Exception\AspectNotFoundException
     */
    public static function getCurrentPage()
    {
        $pageService = GeneralUtility::makeInstance(PageService::class);

        return $pageService->getCurrentPage();
    }

    /**
     * @param $pageUid
     * @return array
     */
    public static function getPage($pageUid)
    {
        $pageService = GeneralUtility::makeInstance(PageService::class);

        return $pageService->getPage($pageUid);
    }

    /**
     * @return \TYPO3\CMS\Core\Page\PageRenderer
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

        return GeneralUtility::makeInstance(PageRenderer::class);
    }
}
