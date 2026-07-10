<?php

namespace Mindshape\MindshapeSeo\Service;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2026 Daniel Dorndorf <dorndorf@mindshape.de>, mindshape GmbH
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

use Doctrine\DBAL\ParameterType;
use Mindshape\MindshapeSeo\Utility\DatabaseUtility;
use Mindshape\MindshapeSeo\Utility\LinkUtility;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use TYPO3\CMS\Backend\Tree\View\PageTreeView;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Imaging\IconSize;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Routing\RouterInterface;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\RootlineUtility;

class PageService implements SingletonInterface
{
    const TREE_DEPTH_INFINITY = -1;
    const TREE_DEPTH_DEFAULT = 1;


    protected ?Site $currentSite = null;


    protected int $currentPageId = 0;

    protected static int $pageTreeDepth = 0;

    public function __construct(
        protected PageRepository $pageRepository,
        protected SiteFinder $siteFinder,
        protected Context $context
    ) {
        $request = $GLOBALS['TYPO3_REQUEST'] ?? null;

        if (!$request instanceof ServerRequestInterface) {
            // No request context (CLI, scheduler, …); leave defaults intact.
            $this->currentSite = null;

            return;
        }

        if (ApplicationType::fromRequest($request)->isBackend()) {
            $this->currentPageId = (int) (
                $request->getQueryParams()['id']
                ?? array_key_first($request->getQueryParams()['edit']['pages'] ?? [])
                ?? 0
            );
        } else {
            $pageArguments = $request->getAttribute('routing');

            if ($pageArguments instanceof PageArguments) {
                $this->currentPageId = $pageArguments->getPageId();
            }
        }

        $site = $request->getAttribute('site');
        $this->currentSite = $site instanceof Site ? $site : null;

        // The request may carry a NullSite (e.g. in some BE entry paths); in
        // that case resolve the proper Site by the current page id.
        if ($this->currentSite === null && $this->currentPageId > 0) {
            try {
                $this->currentSite = $this->siteFinder->getSiteByPageId($this->currentPageId);
            } catch (SiteNotFoundException) {
                $this->currentSite = null;
            }
        }
    }

    public function getCurrentSysLanguageUid(): int
    {
        try {
            /** @var \TYPO3\CMS\Core\Context\LanguageAspect $languageAspect */
            $languageAspect = $this->context->getAspect('language');
        } catch (AspectNotFoundException) {
            return 0;
        }

        return $languageAspect->getId();
    }

    public function getPageLink(
        int $pageId,
        bool $absolute = true,
        int $sysLanguageUid = 0
    ): string {
        try {
            $site = !$this->currentSite instanceof Site
                ? $this->siteFinder->getSiteByPageId($pageId)
                : $this->currentSite;
        } catch (SiteNotFoundException) {
            $site = null;
        }

        if (!$site instanceof Site) {
            throw new RuntimeException('Can\'t generate URI without a configured site');
        }

        return $site->getRouter()->generateUri(
            $pageId,
            ['_language' => $sysLanguageUid],
            type: $absolute ? RouterInterface::ABSOLUTE_URL : RouterInterface::ABSOLUTE_PATH
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getPage(int $pageUid, int $sysLanguageUid = 0): ?array
    {
        $queryBuilder = DatabaseUtility::queryBuilder();
        $queryBuilder->getRestrictions()->removeByType(HiddenRestriction::class);

        $result = $queryBuilder
            ->select('p.*')
            ->from('pages', 'p')
            ->where(
                $queryBuilder->expr()->eq(
                    'p.uid',
                    $queryBuilder->createNamedParameter($pageUid, ParameterType::INTEGER)
                ),
                $queryBuilder->expr()->eq('p.sys_language_uid', $queryBuilder->createNamedParameter(
                    $sysLanguageUid,
                    ParameterType::INTEGER)
                )
            )
            ->executeQuery();

        if (
            0 === $result->rowCount() &&
            !empty($GLOBALS['TCA']['pages']['ctrl']['transOrigPointerField'])
        ) {
            $queryBuilder = DatabaseUtility::queryBuilder();

            $queryBuilder
                ->select('p.*')
                ->from('pages', 'p');

            $queryBuilder->where(
                $queryBuilder->expr()->eq(
                    'p.' . $GLOBALS['TCA']['pages']['ctrl']['transOrigPointerField'],
                    $queryBuilder->createNamedParameter($pageUid, ParameterType::INTEGER)),
                $queryBuilder->expr()->eq('p.sys_language_uid', $queryBuilder->createNamedParameter(
                    $sysLanguageUid,
                    ParameterType::INTEGER)
                )
            );

            $result = $queryBuilder->executeQuery();
        }

        $page = $result->fetchAssociative();

        return is_array($page) ? $page : null;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws \TYPO3\CMS\Core\Context\Exception\AspectNotFoundException
     */
    public function getCurrentPage(): ?array
    {
        $languageId = $this->context->getPropertyFromAspect('language', 'id');

        return $this->getPage($this->currentPageId, (int) ($languageId ?? 0));
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getPageMetaData(int $pageUid, int $sysLanguageUid = 0, string $customUrl = ''): ?array
    {
        $page = $this->getPage($pageUid, $sysLanguageUid);

        if (!is_array($page)) {
            return null;
        }

        $pageUrl = $this->getPageLink($pageUid, true, $sysLanguageUid);

        $previewUrl = $this->getSerpPreviewUrl($pageUid, $sysLanguageUid, $customUrl);

        return [
            'uid' => $pageUid,
            'title' => $page['title'],
            'seoTitle' => $page['seo_title'],
            'disableTitleAttachment' => (bool) $page['mindshapeseo_disable_title_attachment'],
            'url' => $pageUrl,
            'previewUrl' => $previewUrl,
            'canonicalUrl' => !empty($page['canonical_link'])
                ? LinkUtility::renderTypoLink($page['canonical_link'], true)
                : null,
            'meta' => [
                'description' => $page['description'],
                'focusKeyword' => $page['mindshapeseo_focus_keyword'],
                'robots' => [
                    'noindex' => (bool) $page['no_index'],
                    'nofollow' => (bool) $page['no_follow'],
                    'noindexInherited' => $this->pageInheritedProperty(
                        (int) $page['uid'],
                        'mindshapeseo_no_index_recursive'
                    ),
                    'nofollowInherited' => $this->pageInheritedProperty(
                        (int) $page['uid'],
                        'mindshapeseo_no_follow_recursive'
                    ),
                ],
            ],
        ];
    }

    public function getSerpPreviewUrl(int $pageUid, int $sysLanguageUid, string $customUrl = ''): array|string
    {
        $baseUri = '' !== $customUrl ? $customUrl : $GLOBALS['TYPO3_REQUEST']->getAttribute('normalizedParams')->getRequestHost();
        $baseUri = str_replace('https://', "", rtrim($baseUri, '/'));
        $pageUrlNonAbsolute = parse_url($this->getPageLink($pageUid, false, $sysLanguageUid), PHP_URL_PATH);
        $uri = $baseUri . $pageUrlNonAbsolute;

        if ($pageUrlNonAbsolute == "/") {
            return $baseUri;
        }

        if ($this->uriIsTooLong($uri)) {
            if ($this->uriPathTooLong($uri)) {
                $parts = explode("/", $pageUrlNonAbsolute);
                $uri = $baseUri . "/.../" . $parts[count($parts) - 1];
                if ($this->uriIsTooLong($uri)) {
                    $uri = substr($uri, 0, 60) . "...";
                }
            } else {
                $uri = substr($uri, 0, 60) . "...";
            }
        }

        return $this->formatUriForPreview($uri);
    }

    public function formatUriForPreview(string $uri): string
    {
        $uri = str_replace("/", " › ", rtrim($uri, '/'));

        return substr($uri, 0, strpos($uri, ' ')) . ' <span class="path">' . trim(substr($uri,
                strpos($uri, ' '))) . '</span>';
    }

    public function uriIsTooLong(string $uri): bool
    {
        return (strlen($uri) >= 57);
    }

    public function uriPathTooLong(string $uri): bool
    {
        $parts = explode("/", $uri);
        foreach ($parts as $part) {
            if (strlen($part) > 28) {
                return true;
            }
        }

        return false;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getRootline(int $pageUid, int $sysLanguageUid = 0): array
    {
        $pages = [];

        foreach (GeneralUtility::makeInstance(RootlineUtility::class, $pageUid)->get() as $page) {
            $pages[] = $this->getPage($page['uid'], $sysLanguageUid);
        }

        return $pages;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getRootlineReverse(
        int $pageUid,
        bool $withCurrentPage = false,
        bool $withRootPage = true,
        int $sysLanguageUid = 0
    ): array {
        $rootline = $this->getRootline($pageUid, $sysLanguageUid);

        if (false === $withRootPage) {
            array_pop($rootline);
        }

        $rootline = array_reverse($rootline);

        if (false === $withCurrentPage) {
            array_pop($rootline);
        }

        return $rootline;
    }

    /**
     * @param int[] $allowedDoktypes
     * @throws \Doctrine\DBAL\Exception
     */
    public function getPageMetadataTree(
        int $pageUid,
        int $depth = self::TREE_DEPTH_DEFAULT,
        int $sysLanguageUid = 0,
        string $customUrl = '',
        array $allowedDoktypes = [1, 4]
    ): ?array {
        $page = $this->getPage($pageUid, $sysLanguageUid);

        if (!is_array($page)) {
            return null;
        }

        /** @var \TYPO3\CMS\Backend\Tree\View\PageTreeView $tree */
        $tree = GeneralUtility::makeInstance(PageTreeView::class);
        $clause = ' AND pages.deleted = 0 AND pages.sys_language_uid = 0';

        if (0 < count($allowedDoktypes)) {
            $clause .= ' AND (pages.doktype = ' . implode(' OR pages.doktype = ', $allowedDoktypes) . ')';
        }

        $clause .= ' AND ' . $GLOBALS['BE_USER']->getPagePermsClause(1);

        $tree->init($clause, 'pages.sorting');

        /** @var \TYPO3\CMS\Core\Imaging\IconFactory $iconFactory */
        $iconFactory = GeneralUtility::makeInstance(IconFactory::class);

        $tree->tree[] = [
            'row' => $page,
            'HTML' => $iconFactory->getIconForRecord(
                'pages',
                $page,
                IconSize::SMALL
            ),
        ];

        if (self::TREE_DEPTH_INFINITY === $depth) {
            $depth = 9999;
        }

        if (self::$pageTreeDepth === 0) {
            self::$pageTreeDepth = $depth;
        }

        if (0 < $depth) {
            $tree->getTree($pageUid, $depth);
        }

        foreach ($tree->tree as $key => $treeItem) {
            if (
                $treeItem['hasSub'] ?? null &&
            self::$pageTreeDepth - $treeItem['invertedDepth'] === self::$pageTreeDepth - 1
            ) {
                $tree->tree[$key]['hasSub'] = false;
            }

            $metadata = $this->getPageMetaData(
                $treeItem['row']['uid'],
                $sysLanguageUid,
                $customUrl
            );

            if (false === is_array($metadata)) {
                unset($tree->tree[$key]);
                continue;
            }

            $tree->tree[$key]['metadata'] = $metadata;

            $icon = $iconFactory->getIconForRecord(
                'pages',
                $treeItem['row'],
                IconSize::SMALL
            );

            if ($icon->getIdentifier() !== 'apps-pagetree-page-domain') {
                $tree->tree[$key]['HTML'] .= $icon;
            }
        }

        $tree->tree[0]['hasSub'] = 1 < count($tree->tree);

        return $tree->tree;
    }

    /**
     * Checks if a recursive field was set above the page
     * Returns the pid where property was set or false
     *
     * @throws \Doctrine\DBAL\Exception
     */
    protected function pageInheritedProperty(int $pageUid, string $property): bool|int
    {
        $inherited = false;
        $inheritedPageUid = false;

        foreach ($this->getRootlineReverse($pageUid) as $page) {
            if ($pageUid !== (int) $page['uid']) {
                $inherited = $page[$property] ? !$inherited : $inherited;
                $inheritedPageUid = $inherited ? (int) $page['uid'] : false;
            }
        }

        return $inheritedPageUid;
    }
}
