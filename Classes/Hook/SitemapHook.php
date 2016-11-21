<?php
namespace Mindshape\MindshapeSeo\Hook;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Daniel Dorndorf <dorndorf@mindshape.de>
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

use Mindshape\MindshapeSeo\Generator\SitemapGenerator;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * @package mindshape_seo
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
abstract class SitemapHook
{
    /**
     * @var \TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder
     */
    protected $uriBuilder;

    /**
     * @return \Mindshape\MindshapeSeo\Hook\SitemapHook
     */
    public function __construct()
    {
        /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->uriBuilder = $objectManager->get(UriBuilder::class);
    }

    /**
     * @param $params
     * @param \Mindshape\MindshapeSeo\Generator\SitemapGenerator $sitemapGenerator
     * @return void
     */
    public function sitemapPreRendering(&$params, SitemapGenerator $sitemapGenerator)
    {
    }

    /**
     * @param $params
     * @param \Mindshape\MindshapeSeo\Generator\SitemapGenerator $sitemapGenerator
     * @return void
     */
    public function sitemapPostRendering(&$params, SitemapGenerator $sitemapGenerator)
    {
    }
}
