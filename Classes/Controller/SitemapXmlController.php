<?php
namespace Mindshape\MindshapeSeo\Controller;

/***************************************************************
 *
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

use Mindshape\MindshapeSeo\Generator\ImageSitemapGenerator;
use Mindshape\MindshapeSeo\Generator\SitemapGenerator;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * @package mindshape_seo
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class SitemapXmlController implements SingletonInterface
{
    /**
     * @var \Mindshape\MindshapeSeo\Generator\SitemapGenerator
     */
    protected $sitemapGenerator;

    /**
     * @var \Mindshape\MindshapeSeo\Generator\ImageSitemapGenerator
     */
    protected $imageSitemapGenerator;

    /**
     * @return SitemapXmlController
     */
    public function __construct()
    {
        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->sitemapGenerator = $objectManager->get(SitemapGenerator::class);
        $this->imageSitemapGenerator = $objectManager->get(ImageSitemapGenerator::class);
    }

    /**
     * @param string $content
     * @param array $conf
     * @return string
     */
    public function sitemapAction($content, $conf)
    {
        if (1 < (int) GeneralUtility::_GET('pageuid')) {
            $pageUid = (int) GeneralUtility::_GET('pageuid');
        } else {
            $pageUid = $GLOBALS['TSFE']->rootLine[0]['uid'];
        }

        return $this->sitemapGenerator->generateSitemapXml($pageUid);
    }

    /**
     * @param string $content
     * @param array $conf
     * @return string
     */
    public function sitemapIndexAction($content, $conf)
    {
        return $this->sitemapGenerator->generateSitemapIndexXml(
            $pageUid = $GLOBALS['TSFE']->rootLine[0]['uid']
        );
    }

    /**
     * @param string $content
     * @param array $conf
     * @return string
     */
    public function imageSitemapAction($content, $conf)
    {
        return $this->imageSitemapGenerator->generateImageSitemapXml();
    }
}
