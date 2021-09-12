<?php
declare(strict_types=1);

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

use Mindshape\MindshapeSeo\Utility\Exception;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\TypoScript\TemplateService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * @package Mindshape\MindshapeSeo\Utility
 */
class TypoScriptFrontendUtility
{
    public const DEFAULT_PAGETYPE = 1;

    /**
     * @var \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     */
    protected static $typoScriptFrontendController;

    /**
     * @param int $languageId
     * @throws \Mindshape\MindshapeSeo\Utility\Exception\TypoScriptFrontendControllerBootException
     * @throws \TYPO3\CMS\Extbase\Object\Exception
     */
    public static function bootTypoScriptFrontendController(int $languageId = 0): void
    {
        /** @var Typo3Version $typo3Version */
        $typo3Version = GeneralUtility::makeInstance(Typo3Version::class);

        if (static::$typoScriptFrontendController instanceof TypoScriptFrontendController) {
            return;
        }

        $currentSite = null;

        /** @var \TYPO3\CMS\Core\Site\Entity\Site $site */
        foreach (GeneralUtility::makeInstance(SiteFinder::class)->getAllSites() as $site) {
            if (GeneralUtility::getIndpEnv('TYPO3_HOST_ONLY') === $site->getBase()->getHost()) {
                $currentSite = $site;
            }
        }

        if (null === $currentSite) {
            throw new Exception\TypoScriptFrontendControllerBootException('Can\'t determine the site to use');
        }

        $siteLanguage = $currentSite->getLanguageById($languageId);

        if (
            true === version_compare('10.4', $typo3Version->getBranch(), '==') ||
            true === version_compare('11.4', $typo3Version->getBranch(), '==')
        ) {
            /** @var \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController $typoScriptFrontendController */
            $typoScriptFrontendController = GeneralUtility::makeInstance(
                TypoScriptFrontendController::class,
                GeneralUtility::makeInstance(Context::class),
                $currentSite,
                $siteLanguage
            );
        } else {
            throw new Exception\TypoScriptFrontendControllerBootException(
                'This Utility is not compatible with TYPO3 v' . $typo3Version->getBranch()
            );
        }

        $typoScriptFrontendController->sys_page = GeneralUtility::makeInstance(PageRepository::class);

        $GLOBALS['TSFE'] = $typoScriptFrontendController;

        /** @var \TYPO3\CMS\Core\TypoScript\TemplateService $templateService */
        $templateService = GeneralUtility::makeInstance(TemplateService::class);
        $templateService->start($typoScriptFrontendController->rootLine);

        $GLOBALS['TSFE']->tmpl = $templateService;

        static::$typoScriptFrontendController = $typoScriptFrontendController;
    }
}
