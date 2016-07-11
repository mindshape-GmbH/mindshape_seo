<?php
namespace Mindshape\MindshapeSeo\Generator;

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

use Mindshape\MindshapeSeo\Service\PageService;
use TYPO3\CMS\Core\Database\QueryGenerator;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * @package mindshape_seo
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class SitemapGenerator implements SingletonInterface
{
    const DEFAULT_PRIORITY = 0.5;

    const TAG_URL = 'url';
    const TAG_SITEMAP = 'sitemap';

    const CHANGE_FREQUENCY_ALWAYS = 'always';
    const CHANGE_FREQUENCY_HOULRY = 'hourly';
    const CHANGE_FREQUENCY_DAILY = 'daily';
    const CHANGE_FREQUENCY_WEEKLY = 'weekly';
    const CHANGE_FREQUENCY_MONTHLY = 'monthly';
    const CHANGE_FREQUENCY_YEARLY = 'yearly';
    const CHANGE_FREQUENCY_NEVER = 'never';

    /**
     * @var \TYPO3\CMS\Frontend\Page\PageRepository
     * @inject
     */
    protected $pageRepository;

    /**
     * @var \Mindshape\MindshapeSeo\Service\PageService
     */
    protected $pageService;

    public function __construct()
    {
        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->pageService = $objectManager->get(PageService::class);
    }

    /**
     * @param int $page
     * @return string
     */
    public function generateSitemapXml($page)
    {
        return $this->getStartTags() . $this->getUrls($page) . $this->getEndTags();
    }

    /**
     * Creates end tags for this sitemap.
     *
     * @return string
     */
    protected function getEndTags()
    {
        return '</urlset>';
    }

    /**
     * Creates start tags for this sitemap.
     *
     * @return string
     */
    protected function getStartTags()
    {
        return '<?xml version="1.0" encoding="UTF-8"?>' .
        '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';
    }

    /**
     * @param int $pageUid
     * @return string
     */
    protected function getUrls($pageUid)
    {
        $urls = '';

        $pages = $this->pageService->getSubPagesFromPageUid($pageUid);

        $excludePids = array();

        foreach ($pages as $page) {
            $isExcludeSubpagesFromSitemap = (bool) $page['mindshapeseo_exclude_suppages_from_sitemap'];
            $isExcludeFromSitemap = (bool) $page['mindshapeseo_exclude_from_sitemap'];
            $isSubSitemap = (bool) $page['mindshapeseo_sub_sitemap'];
            $isNoIndex = (bool) $page['mindshapeseo_no_index'];
            $changeFrequency = $page['mindshapeseo_change_frequency'];
            $priority = (double) $page['mindshapeseo_priority'];
            $isPage = 1 === (int) $page['doktype'];
            $isSitemapPage = $pageUid === (int) $page['uid'];

            if (
                $isExcludeSubpagesFromSitemap ||
                (
                    true === $isSubSitemap &&
                    false === $isSitemapPage
                ) ||
                ($isSubSitemap && $isExcludeSubpagesFromSitemap)
            ) {
                $excludePids = array_merge(
                    $excludePids,
                    array_diff(
                        $this->pageService->getSubPageUidsFromPageUid($page['uid']),
                        array($page['uid'])
                    )
                );
            }

            if (
                $isNoIndex ||
                $isExcludeFromSitemap ||
                false === $isPage ||
                ($isSubSitemap && $isExcludeSubpagesFromSitemap) ||
                (
                    $isSitemapPage &&
                    $isSubSitemap &&
                    $isExcludeSubpagesFromSitemap
                ) ||
                true === in_array($page['uid'], $excludePids, false)
            ) {
                continue;
            }

            if (
                $isSubSitemap &&
                false === $isSitemapPage
            ) {
                $tag = self::TAG_SITEMAP;
                $url = $this->pageService->getPageLink($GLOBALS['TSFE']->rootLine[0]['uid']) . 'sitemap_' . $page['uid'] . '.xml';
            } else {
                $tag = self::TAG_URL;
                $url = $this->pageService->getPageLink($page['uid']);
            }

            $lastmod = new \DateTime();
            $lastmod->setTimestamp($page['SYS_LASTCHANGED']);

            $urls .= $this->renderEntry($tag, $url, $lastmod, $changeFrequency, $priority);
        }

        return $urls;
    }

    /**
     * Renders a single entry as a normal sitemap entry.
     *
     * @param string    $tag
     * @param string    $url
     * @param \DateTime $lastModification
     * @param string    $changeFrequency
     * @param double    $priority
     * @return string
     */
    protected function renderEntry($tag = self::TAG_URL, $url, \DateTime $lastModification, $changeFrequency = '', $priority = self::DEFAULT_PRIORITY)
    {
        $content = '<loc>' . $url . '</loc>';

        if ($lastModification) {
            $content .= '<lastmod>' . $lastModification->format('c') . '</lastmod>';
        }

        if (self::TAG_URL === $tag) {
            $content .= '<priority>' . sprintf('%1.1F', $priority) . '</priority>';

            if ('' !== $changeFrequency) {
                $content .= '<changefreq>' . $changeFrequency . '</changefreq>';
            }
        }

        return '<' . $tag . '>' . $content . '</' . $tag . '>';
    }
}
