<?php
namespace Mindshape\MindshapeSeo\Userfuncs\Tca;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017 Daniel Dorndorf <dorndorf@mindshape.de>, mindshape GmbH
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

use Mindshape\MindshapeSeo\Domain\Model\Configuration;
use Mindshape\MindshapeSeo\Service\DomainService;
use Mindshape\MindshapeSeo\Service\PageService;
use Mindshape\MindshapeSeo\Service\StandaloneTemplateRendererService;
use TYPO3\CMS\Backend\Form\Element\UserElement;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * @package mindshape_seo
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class FbScrapeButton
{
    /**
     * @param
     * @return string
     */
    public function render()
    {
        $button  = '<a href="#" id="fbScrapeButton" class="btn btn-default">';
        $button .= 'Scrape Facebook';
        $button .= '</a>';
        $message = '<div class="fbScrapeMessage" style="margin-bottom:0;"></div>';

        $result = '<div class="row" style="display:flex; align-items:center;">';
        $result.= '<div class="col-sm-2">' . $button;
        $result.= '</div><div class="col-sm-10">' . $message ;
        $result.= '</div></div>';
        return $result;
    }
}
