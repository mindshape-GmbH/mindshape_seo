<?php
namespace Mindshape\MindshapeSeo\Hook;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2021 Daniel Dorndorf <dorndorf@mindshape.de>
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

use Mindshape\MindshapeSeo\Service\HeaderDataService;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Exception\InvalidExtensionNameException;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * @package mindshape_seo
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class InjectAnalyticsHook
{
    /**
     * @param array $params
     * @param \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController $typoScriptFrontendController
     */
    public function all(array &$params, TypoScriptFrontendController $typoScriptFrontendController): void
    {
        $this->injectAnalyticsTags($typoScriptFrontendController->content);
    }

    /**
     * @param array $params
     * @param \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController $typoScriptFrontendController
     */
    public function cached(array &$params, TypoScriptFrontendController $typoScriptFrontendController): void
    {
        $this->injectAnalyticsTags($typoScriptFrontendController->content);
    }

    /**
     * @param array $params
     * @param \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController $typoScriptFrontendController
     */
    public function output(array &$params, TypoScriptFrontendController $typoScriptFrontendController): void
    {
        $this->injectAnalyticsTags($typoScriptFrontendController->content);
    }

    /**
     * @param array $params
     * @param \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController $typoScriptFrontendController
     * @return void
     */
    public function pageLoadFromCache(array &$params, TypoScriptFrontendController $typoScriptFrontendController) {
        if (isset($params['cache_pages_row']['content']) && strlen($params['cache_pages_row']['content']) > 0) {
            $this->injectAnalyticsTags($params['cache_pages_row']['content']);
        }
    }


    /**
     * @param string $html
     * @return void
     */
    public function injectAnalyticsTags(string &$html)
    {
        if (true === ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isFrontend()) {
            /** @var \Mindshape\MindshapeSeo\Service\HeaderDataService $headerDataService */
            $headerDataService = GeneralUtility::makeInstance(HeaderDataService::class);
            try {
                $analyticsData = $headerDataService->handleAnalytics();
                if (count($analyticsData) > 0) {
                    foreach ($analyticsData as $data) {
                        if ($data !== '' && mb_strpos($html, $data) === false) {
                            $html = str_ireplace("</head>", "$data</head>", $html);
                        }
                    }
                }
            } catch (InvalidExtensionNameException $e) {}
        }
    }
}
