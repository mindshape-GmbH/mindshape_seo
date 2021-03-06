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
use Mindshape\MindshapeSeo\Utility\ObjectUtility;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * @package mindshape_seo
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class RenderPreProcessHook
{
    /**
     * @param array $params
     * @param PageRenderer $pageRenderer
     * @throws \TYPO3\CMS\Extbase\Object\Exception
     */
    public function main(array &$params, PageRenderer $pageRenderer)
    {
        if ('FE' === TYPO3_MODE) {
            /** @var \Mindshape\MindshapeSeo\Service\HeaderDataService $headerDataService */
            $headerDataService = ObjectUtility::makeInstance(HeaderDataService::class);
            $headerDataService->manipulateHeaderData();
        }
    }
}
