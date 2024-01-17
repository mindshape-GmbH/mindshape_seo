<?php
declare(strict_types=1);

namespace Mindshape\MindshapeSeo\Utility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2023 Daniel Dorndorf <dorndorf@mindshape.de>, mindshape GmbH
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
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\TypoScript\TemplateService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * @package Mindshape\MindshapeSeo\Utility
 */
class TypoScriptFrontendUtility
{
    /**
     * @var \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     */
    protected static TypoScriptFrontendController $typoScriptFrontendController;

    /**
     * @param int $languageId
     * @throws \Mindshape\MindshapeSeo\Utility\Exception\TypoScriptFrontendControllerBootException
     * @throws \TYPO3\CMS\Core\Authentication\Mfa\MfaRequiredException
     */
    public static function bootTypoScriptFrontendController(int $languageId = 0): void
    {
        if (static::$typoScriptFrontendController ?? null instanceof TypoScriptFrontendController) {
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

        $frontendAuthentication = GeneralUtility::makeInstance(FrontendUserAuthentication::class);
        $frontendAuthentication->start($GLOBALS['TYPO3_REQUEST']);

        $typoScriptFrontendController = GeneralUtility::makeInstance(
            TypoScriptFrontendController::class,
            GeneralUtility::makeInstance(Context::class),
            $site,
            $site->getDefaultLanguage(),
            GeneralUtility::makeInstance(PageArguments::class, $site->getRootPageId(), '1', []),
            $frontendAuthentication
        );

        $typoScriptFrontendController->sys_page = GeneralUtility::makeInstance(PageRepository::class);

        $GLOBALS['TSFE'] = $typoScriptFrontendController;

        /** @var \TYPO3\CMS\Core\TypoScript\TemplateService $templateService */
        $templateService = GeneralUtility::makeInstance(TemplateService::class);
        $templateService->start($typoScriptFrontendController->rootLine);

        $GLOBALS['TSFE']->tmpl = $templateService;

        static::$typoScriptFrontendController = $typoScriptFrontendController;
    }
}
