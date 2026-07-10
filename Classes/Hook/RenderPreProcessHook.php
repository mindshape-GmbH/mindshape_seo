<?php
namespace Mindshape\MindshapeSeo\Hook;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2026 Daniel Dorndorf <dorndorf@mindshape.de>
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
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class RenderPreProcessHook
{
    /**
     * The render-preProcess hook is still supported in TYPO3 v13/v14 and has no
     * PSR-14 event replacement yet; the title manipulation relies on the page
     * title being already resolved on the PageRenderer at this point.
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function main(array &$params, PageRenderer $pageRenderer): void
    {
        $request = $GLOBALS['TYPO3_REQUEST'] ?? null;

        if (!$request instanceof ServerRequestInterface) {
            return;
        }

        if (ApplicationType::fromRequest($request)->isFrontend()) {
            /** @var \Mindshape\MindshapeSeo\Service\HeaderDataService $headerDataService */
            $headerDataService = GeneralUtility::makeInstance(HeaderDataService::class);
            $headerDataService->manipulateHeaderData();
        }
    }
}
