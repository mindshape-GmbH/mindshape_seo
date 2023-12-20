<?php

namespace Mindshape\MindshapeSeo\Service;

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

use Mindshape\MindshapeSeo\Utility\BackendUtility;
use Mindshape\MindshapeSeo\Utility\BackendUtility as MindshapeBackendUtility;
use Mindshape\MindshapeSeo\Utility\DatabaseUtility;
use Mindshape\MindshapeSeo\Utility\Exception\TypoScriptFrontendControllerBootException;
use Mindshape\MindshapeSeo\Utility\LinkUtility;
use Mindshape\MindshapeSeo\Utility\TypoScriptFrontendUtility;
use PDO;
use TYPO3\CMS\Backend\Tree\View\PageTreeView;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;
use TYPO3\CMS\Core\Database\QueryGenerator;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\RootlineUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * @package mindshape_seo
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class PageService implements SingletonInterface
{
    const TREE_DEPTH_INFINITY = -1;
    const TREE_DEPTH_DEFAULT = 1;

    /**
     * @var \TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder
     */
    protected UriBuilder $uriBuilder;

    /**
     * @var \TYPO3\CMS\Core\Domain\Repository\PageRepository
     */
    protected PageRepository $pageRepository;

    /**
     * @var int
     */
    protected static int $pageTreeDepth = 0;

    /**
     * @var \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController|null
     */
    protected ?TypoScriptFrontendController $typoScriptFrontendController;

    /**
     * @param \TYPO3\CMS\Core\Domain\Repository\PageRepository $pageRepository
     * @param \TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder $uriBuilder
     * @throws \Mindshape\MindshapeSeo\Service\Exception
     * @throws \TYPO3\CMS\Core\Authentication\Mfa\MfaRequiredException
     */
    public function __construct(
        PageRepository $pageRepository,
        UriBuilder $uriBuilder
    ) {
        $this->pageRepository = $pageRepository;
        $this->uriBuilder = $uriBuilder;

        if (true === ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isBackend()) {
            try {
                TypoScriptFrontendUtility::bootTypoScriptFrontendController();
                $this->typoScriptFrontendController = $GLOBALS['TSFE'];
            } catch (TypoScriptFrontendControllerBootException $exception) {
                $this->typoScriptFrontendController = null;
            }
        } elseif (true === ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isFrontend()) {
            $this->typoScriptFrontendController = $GLOBALS['TSFE'];
        } else {
            throw new Exception('Illegal TYPO3 mode');
        }
    }

    /**
     * @return bool
     */
    public function hasFrontendController(): bool
    {
        return $this->typoScriptFrontendController instanceof TypoScriptFrontendController;
    }

    /**
     * @return int
     */
    public function getCurrentSysLanguageUid(): int
    {
        /** @var \TYPO3\CMS\Core\Context\LanguageAspect $languageAspect */
        try {
            $languageAspect = GeneralUtility::makeInstance(Context::class)->getAspect('language');
        } catch (AspectNotFoundException) {
            return 0;
        }

        return $languageAspect->getId();
    }

    /**
     * Creates a link to a single page
     *
     * @param int $pageId
     * @param bool $absolute
     * @param int $sysLanguageUid
     * @param bool $linkAccessRestrictedPages
     * @return string
     */
    public function getPageLink(int $pageId, bool $absolute = false, int $sysLanguageUid = 0, bool $linkAccessRestrictedPages = true): string
    {
        $this->uriBuilder
            ->reset()
            ->setTargetPageUid($pageId)
            ->setCreateAbsoluteUri($absolute)
            ->setLanguage($sysLanguageUid)
            ->setLinkAccessRestrictedPages($linkAccessRestrictedPages);

        return $this->uriBuilder->buildFrontendUri();
    }

    /**
     * @param int $pageUid
     * @param int $sysLanguageUid
     * @return array|null
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
                    $queryBuilder->createNamedParameter($pageUid, PDO::PARAM_INT)
                ),
                $queryBuilder->expr()->eq('p.sys_language_uid', $queryBuilder->createNamedParameter(
                    $sysLanguageUid,
                    PDO::PARAM_INT)
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
                    $queryBuilder->createNamedParameter($pageUid, PDO::PARAM_INT)),
                $queryBuilder->expr()->eq('p.sys_language_uid', $queryBuilder->createNamedParameter(
                    $sysLanguageUid,
                    PDO::PARAM_INT)
                )
            );

            $result = $queryBuilder->executeQuery();
        }

        $page = $result->fetchAssociative();

        return is_array($page) ? $page : null;
    }

    /**
     * @return array|null
     * @throws \Doctrine\DBAL\Exception
     * @throws \TYPO3\CMS\Core\Context\Exception\AspectNotFoundException
     */
    public function getCurrentPage(): ?array
    {
        $pageId = $this->typoScriptFrontendController->id;
        $languageId = GeneralUtility::makeInstance(Context::class)->getPropertyFromAspect('language', 'id');

        if (is_null($pageId)) {
            $pageId = BackendUtility::getCurrentPageTreeSelectedPage();
        }

        return $this->getPage($pageId, $languageId ?? 0);
    }

    /**
     * @param int $pageUid
     * @param int $sysLanguageUid
     * @param string $customUrl
     * @return array|null
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

    /**
     * @param int $pageUid
     * @param int $sysLanguageUid
     * @param string $customUrl
     * @return array|string
     */
    public function getSerpPreviewUrl(int $pageUid, int $sysLanguageUid, string $customUrl = ''): array|string
    {
        $baseUri = '' !== $customUrl ? $customUrl : GeneralUtility::getIndpEnv('TYPO3_REQUEST_HOST');
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

    /**
     * @param string $uri
     * @return string
     */
    public function formatUriForPreview(string $uri): string
    {
        $uri = str_replace("/", " › ", rtrim($uri, '/'));

        return substr($uri, 0, strpos($uri, ' ')) . ' <span class="path">' . trim(substr($uri, strpos($uri, ' '))) . '</span>';
    }

    /**
     * @param string $uri
     * @return bool
     */
    public function uriIsTooLong(string $uri): bool
    {
        return (strlen($uri) >= 57);
    }

    /**
     * @param string $uri
     * @return bool
     */
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
     * @param int|null $pageUid
     * @param int $sysLanguageUid
     * @return array
     * @throws \Doctrine\DBAL\Exception
     */
    public function getRootline(?int $pageUid = null, int $sysLanguageUid = 0): array
    {
        $pages = [];

        $currentPageUid = MindshapeBackendUtility::getCurrentPageTreeSelectedPage();

        if (null === $pageUid) {
            if (0 < $this->typoScriptFrontendController->id) {
                $pageUid = $this->typoScriptFrontendController->id;
            } elseif (0 < $currentPageUid) {
                $pageUid = $currentPageUid;
            }
        }

        foreach (GeneralUtility::makeInstance(RootlineUtility::class, $pageUid)->get() as $page) {
            $pages[] = $this->getPage($page['uid'], $sysLanguageUid);
        }

        return $pages;
    }

    /**
     * @param int|null $pageUid
     * @param bool $withCurrentPage
     * @param bool $withRootPage
     * @param int $sysLanguageUid
     * @return array
     * @throws \Doctrine\DBAL\Exception
     */
    public function getRootlineReverse(?int $pageUid = null, bool $withCurrentPage = false, bool $withRootPage = true, int $sysLanguageUid = 0): array
    {
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
     * @param int $pageUid
     * @param int $depth
     * @param int $sysLanguageUid
     * @param string $customUrl
     * @param int[] $allowedDoktypes
     * @return array|null
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

        /** @var \TYPO3\CMS\Core\Information\Typo3Version $typo3Version */
        $typo3Version = GeneralUtility::makeInstance(Typo3Version::class);

        /** @var \TYPO3\CMS\Backend\Tree\View\PageTreeView $tree */
        $tree = GeneralUtility::makeInstance(PageTreeView::class);
        $tree->init();
        $tree->clause = ' AND pages.deleted = 0 AND pages.sys_language_uid = 0';

        if (0 < count($allowedDoktypes)) {
            $tree->clause .= ' AND (pages.doktype = ' . implode(' OR pages.doktype = ', $allowedDoktypes) . ')';
        }

        $tree->clause .= ' AND ' . $GLOBALS['BE_USER']->getPagePermsClause(1);

        $tree->parentField = 'pages.pid';
        $tree->fieldArray = ['pages.*'];
        $tree->orderByFields = 'pages.sorting';

        /** @var \TYPO3\CMS\Core\Imaging\IconFactory $iconFactory */
        $iconFactory = GeneralUtility::makeInstance(IconFactory::class);

        $tree->tree[] = [
            'row' => $page,
            'HTML' => true === version_compare('11.0', $typo3Version->getVersion(), '<=')
                ? ''
                : $iconFactory->getIconForRecord(
                    'pages',
                    $page,
                    Icon::SIZE_SMALL
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
                Icon::SIZE_SMALL
            );

            if (true === version_compare('11.0', $typo3Version->getVersion(), '<=')) {
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
     * @param int $pageUid
     * @param string $property
     * @return int|bool
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
