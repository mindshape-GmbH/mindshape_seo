<?php
namespace Mindshape\MindshapeSeo\Generator;

/***************************************************************
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

use Mindshape\MindshapeSeo\Domain\Model\SitemapIndexNode;
use Mindshape\MindshapeSeo\Domain\Model\SitemapNode;
use Mindshape\MindshapeSeo\Service\PageService;
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

    /**
     * @var \TYPO3\CMS\Frontend\Page\PageRepository
     * @inject
     */
    protected $pageRepository;

    /**
     * @var \Mindshape\MindshapeSeo\Service\PageService
     */
    protected $pageService;

    /**
     * @var array
     */
    protected $nodes = array();

    /**
     * @return \Mindshape\MindshapeSeo\Generator\SitemapGenerator
     */
    public function __construct()
    {
        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->pageService = $objectManager->get(PageService::class);
    }

    /**
     * @param int $pageUid
     * @return string
     */
    public function generateSitemap($pageUid)
    {
        $this->getNodes($pageUid);

        $sitemap = $this->getUrlsStartTag() . $this->getRenderedUrls() . $this->getUrlsEndTag();

        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mindshape_seo']['sitemap_postRendering'])) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mindshape_seo']['sitemap_postRendering'] as $userFunc) {
                $params = array('sitemap' => &$sitemap);

                GeneralUtility::callUserFunction($userFunc, $params, $this);
            }
        }

        return $sitemap;
    }

    /**
     * @param int $pageUid
     * @return string
     */
    public function generateSitemapIndexXml($pageUid)
    {
        $this->getSitemaps($pageUid);

        $sitemapIndex = $this->getSitemapIndexStartTag() . $this->getRenderedSitemapNodes() . $this->getSitemapIndexEndTag();

        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mindshape_seo']['sitemapIndex_postRendering'])) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mindshape_seo']['sitemapIndex_postRendering'] as $userFunc) {
                $params = array('sitemap' => &$sitemapIndex);

                GeneralUtility::callUserFunction($userFunc, $params, $this);
            }
        }

        return $sitemapIndex;
    }

    /**
     * Creates end tags for this sitemap.
     *
     * @return string
     */
    protected function getUrlsEndTag()
    {
        return '</urlset>';
    }

    /**
     * Creates start tags for this sitemap.
     *
     * @return string
     */
    protected function getUrlsStartTag()
    {
        return '<?xml version="1.0" encoding="UTF-8"?>' .
        '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';
    }

    /**
     * @return string
     */
    protected function getSitemapIndexEndTag()
    {
        return '</sitemapindex>';
    }

    /**
     * @return string
     */
    protected function getSitemapIndexStartTag()
    {
        return '<?xml version="1.0" encoding="UTF-8"?>' .
        '<sitemapindex xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    }

    /**
     * @param int $pageUid
     * @return string
     */
    protected function getNodes($pageUid)
    {
        $pageUid = (int) $pageUid;

        $pages = $this->pageService->getSubPagesFromPageUid($pageUid);

        $excludePids = array();

        foreach ($pages as $page) {
            $isExcludeSubpagesFromSitemap = (bool) $page['mindshapeseo_exclude_suppages_from_sitemap'];
            $isExcludeFromSitemap = (bool) $page['mindshapeseo_exclude_from_sitemap'];
            $isSubSitemap = (bool) $page['mindshapeseo_sub_sitemap'];
            $isNoIndex = (bool) $page['mindshapeseo_no_index'];
            $isNoIndexRecurive = (bool) $page['mindshapeseo_no_index_recursive'];
            $isPage = 1 === (int) $page['doktype'];
            $isSitemapPage = $pageUid === (int) $page['uid'];

            if (
                $isExcludeSubpagesFromSitemap ||
                $isNoIndexRecurive ||
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

            $parentsProperties = $this->getParentProperties();

            if (
                $isNoIndex ||
                $isExcludeFromSitemap ||
                false === $isPage ||
                0 !== (int) $parentsProperties['fe_group'] ||
                0 !== (int) $page['fe_group'] ||
                true === in_array($page['uid'], $excludePids, false) ||
                (
                    $isSubSitemap &&
                    $isExcludeSubpagesFromSitemap
                ) ||
                (
                    $isSitemapPage &&
                    $isSubSitemap &&
                    $isExcludeSubpagesFromSitemap
                ) ||
                (
                    $isSubSitemap &&
                    false === $isSitemapPage
                )
            ) {
                continue;
            }

            $node = new SitemapNode();

            $node->setUrl($this->pageService->getPageLink($page['uid'], true));

            $lastModification = new \DateTime();
            $lastModification->setTimestamp($page['SYS_LASTCHANGED']);

            $node->setLastModification($lastModification);

            $changeFrequency = $page['mindshapeseo_change_frequency'];
            $priority = (double) $page['mindshapeseo_priority'];

            if (empty($changeFrequency)) {
                $changeFrequency = $parentsProperties['changeFrequenzy'];
            }

            if (empty($priority)) {
                $priority = $parentsProperties['priority'];
            }

            $node->setChangeFrequency($changeFrequency);
            $node->setPriority($priority);

            $this->nodes[] = $node;
        }
    }

    /**
     * @return string
     */
    protected function getRenderedUrls()
    {
        $urls = '';

        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mindshape_seo']['sitemap_preRendering'])) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mindshape_seo']['sitemap_preRendering'] as $userFunc) {
                $params = array('nodes' => &$this->nodes);

                GeneralUtility::callUserFunction($userFunc, $params, $this);
            }
        }

        /** @var \Mindshape\MindshapeSeo\Domain\Model\SitemapNode $node */
        foreach ($this->nodes as $node) {
            if ($node instanceof SitemapNode) {
                $urls .= $this->renderEntry(
                    self::TAG_URL,
                    $node->getUrl(),
                    $node->getLastModification(),
                    $node->getChangeFrequency(),
                    $node->getPriority()
                );
            }
        }

        return $urls;
    }

    /**
     * @param int $pageUid
     * @return void
     */
    protected function getSitemaps($pageUid)
    {
        $sitemaps = '';

        $pages = $this->pageService->getSubPagesFromPageUid($pageUid);

        foreach ($pages as $page) {
            $isExcludeSubpagesFromSitemap = (bool) $page['mindshapeseo_exclude_suppages_from_sitemap'];
            $isSubSitemap = (bool) $page['mindshapeseo_sub_sitemap'];
            $isNoIndex = (bool) $page['mindshapeseo_no_index'];
            $isNoIndexRecurive = (bool) $page['mindshapeseo_no_index_recursive'];

            if (
                false === $isNoIndex &&
                false === $isNoIndexRecurive &&
                false === $isExcludeSubpagesFromSitemap &&
                $isSubSitemap
            ) {
                $indexNode = new SitemapIndexNode();

                $lastModification = new \DateTime();
                $lastModification->setTimestamp($page['SYS_LASTCHANGED']);

                $indexNode->setLastModification($lastModification);

                $pageUrl = $this->pageService->getPageLink($page['uid'], true);
                $pageUrl = preg_replace('#(\.html)$#i', '/', $pageUrl);

                $indexNode->setUrl($pageUrl);

                $this->nodes[] = $indexNode;
            }
        }
    }

    /**
     * @return string
     */
    protected function getRenderedSitemapNodes()
    {
        $sitemapNodes = '';

        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mindshape_seo']['sitemapIndex_preRendering'])) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mindshape_seo']['sitemapIndex_preRendering'] as $userFunc) {
                $params = array('nodes' => &$this->nodes);

                GeneralUtility::callUserFunction($userFunc, $params, $this);
            }
        }

        /** @var \Mindshape\MindshapeSeo\Domain\Model\SitemapIndexNode $node */
        foreach ($this->nodes as $node) {
            if ($node instanceof SitemapIndexNode) {
                $sitemapNodes .= $this->renderEntry(
                    self::TAG_SITEMAP,
                    $node->getUrl(),
                    $node->getLastModification()
                );
            }
        }

        return $sitemapNodes;
    }

    /**
     * @return array
     */
    protected function getParentProperties()
    {
        $properties = array(
            'changeFrequenzy' => '',
            'priority' => self::DEFAULT_PRIORITY,
            'fe_group' => 0,
        );

        foreach ($this->pageService->getRootline() as $page) {
            if (
                !empty($page['mindshapeseo_change_frequency']) &&
                empty($properties['changeFrequenzy'])
            ) {
                $properties['changeFrequenzy'] = $page['mindshapeseo_change_frequency'];
            }

            if (
                !empty($page['fe_group']) &&
                empty($properties['fe_group'])
            ) {
                $properties['fe_group'] = (int) $page['fe_group'];
            }

            if (
                self::DEFAULT_PRIORITY !== (double) $page['mindshapeseo_priority'] &&
                self::DEFAULT_PRIORITY === $properties['priority']
            ) {
                $properties['priority'] = (double) $page['mindshapeseo_priority'];
            }
        }

        return $properties;
    }

    /**
     * Renders a single entry as a normal sitemap entry.
     *
     * @param string $tag
     * @param string $url
     * @param \DateTime $lastModification
     * @param string $changeFrequency
     * @param double $priority
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
