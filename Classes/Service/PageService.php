<?php
namespace Mindshape\MindshapeSeo\Service;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2021 Daniel Dorndorf <dorndorf@mindshape.de>, mindshape GmbH
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
use Mindshape\MindshapeSeo\Utility\ObjectUtility;
use Mindshape\MindshapeSeo\Utility\TypoScriptFrontendUtility;
use PDO;
use TYPO3\CMS\Backend\Tree\View\PageTreeView;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;
use TYPO3\CMS\Core\Database\QueryGenerator;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\RootlineUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\Page\PageRepository;

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
    protected $uriBuilder;

    /**
     * @var \TYPO3\CMS\Frontend\Page\PageRepository
     */
    protected $pageRepository;

    /**
     * @var int
     */
    protected static $pageTreeRoot = 0;

    /**
     * @var int
     */
    protected static $pageTreeDepth = 0;

    /**
     * @var \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     */
    protected $typoScriptFrontendController;

    /**
     * @throws \Mindshape\MindshapeSeo\Service\Exception
     * @throws \TYPO3\CMS\Extbase\Object\Exception
     */
    public function __construct()
    {
        /** @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManager $configurationManager */
        $configurationManager = ObjectUtility::makeInstance(ConfigurationManager::class);
        $this->pageRepository = ObjectUtility::makeInstance(PageRepository::class);

        if ('BE' === TYPO3_MODE) {
            try {
                TypoScriptFrontendUtility::bootTypoScriptFrontendController();
                $this->typoScriptFrontendController = $GLOBALS['TSFE'];
            } catch (TypoScriptFrontendControllerBootException $exception) {
                $this->typoScriptFrontendController = null;
            }
        } elseif ('FE' === TYPO3_MODE) {
            $this->typoScriptFrontendController = $GLOBALS['TSFE'];
        } else {
            throw new Exception('Illegal TYPO3_MODE');
        }

        $configurationManager->setContentObject(
            ObjectUtility::makeInstance(ContentObjectRenderer::class)
        );

        $this->uriBuilder = ObjectUtility::makeInstance(UriBuilder::class);
        $this->uriBuilder->injectConfigurationManager($configurationManager);
    }

    /**
     * @return bool
     */
    public function hasFrontendController()
    {
        return $this->typoScriptFrontendController instanceof TypoScriptFrontendController;
    }

    /**
     * @return int
     */
    public function getCurrentSysLanguageUid()
    {
        /** @var \TYPO3\CMS\Core\Context\LanguageAspect $languageAspect */
        $languageAspect = GeneralUtility::makeInstance(Context::class)->getAspect('language');
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
    public function getPageLink($pageId, $absolute = false, int $sysLanguageUid = 0, $linkAccessRestrictedPages = true)
    {
        $this->uriBuilder
            ->reset()
            ->setTargetPageUid($pageId)
            ->setCreateAbsoluteUri($absolute)
            ->setLinkAccessRestrictedPages($linkAccessRestrictedPages);

        /** @var Typo3Version $typo3Version */
        $typo3Version = GeneralUtility::makeInstance(Typo3Version::class);

        if (true === version_compare('10.4', $typo3Version->getVersion(), '<=')) {
            $this->uriBuilder->setLanguage($sysLanguageUid);
        } else {
            $this->uriBuilder->setArguments(['L' => $sysLanguageUid]);
        }

        return $this->uriBuilder->buildFrontendUri();
    }

    /**
     * @param int $pageUid
     * @param int $sysLanguageUid
     * @return array|false
     */
    public function getPage($pageUid, $sysLanguageUid = 0)
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
            ->execute();
        if (0 === $result->rowCount()) {
            $queryBuilder = DatabaseUtility::queryBuilder();

            $queryBuilder
                ->select('p.*')
                ->from('pages', 'p');

            if (isset($GLOBALS['TCA']['pages']['ctrl']['transOrigPointerField']) && !empty($GLOBALS['TCA']['pages']['ctrl']['transOrigPointerField'])) {
                $queryBuilder->where(
                    $queryBuilder->expr()->eq(
                        'p.' . $GLOBALS['TCA']['pages']['ctrl']['transOrigPointerField'],
                        $queryBuilder->createNamedParameter($pageUid, PDO::PARAM_INT)),
                    $queryBuilder->expr()->eq('p.sys_language_uid', $queryBuilder->createNamedParameter(
                        $sysLanguageUid,
                        PDO::PARAM_INT)
                    )
                );
            } else {
                $queryBuilder->where(
                    $queryBuilder->expr()->eq('p.sys_language_uid', $queryBuilder->createNamedParameter(
                        $sysLanguageUid,
                        PDO::PARAM_INT)
                    )
                );
            }
            $result = $queryBuilder->execute();
        }

        return $result->fetch();
    }

    /**
     * @return array
     */
    public function getCurrentPage()
    {
        $pageId = $this->typoScriptFrontendController->id;
        $languageId = GeneralUtility::makeInstance(Context::class)->getPropertyFromAspect('language', 'id');

        if (is_null($pageId)) {
            $pageId = BackendUtility::getCurrentPageTreeSelectedPage();
        }

        return $this->getPage((int) $pageId, $languageId ?? 0);
    }

    /**
     * @param int $pageUid
     * @param int $sysLanguageUid
     * @param string $customUrl
     * @param $useGoogleBreadcrumb
     * @return array|null
     */
    public function getPageMetaData($pageUid, $sysLanguageUid = 0, $customUrl = '', $useGoogleBreadcrumb = false)
    {
        $page = $this->getPage($pageUid, $sysLanguageUid);

        if (false === $page) {
            return null;
        }

        $pageUrl = $this->getPageLink($pageUid, true, $sysLanguageUid);

        $previewUrl = $this->getSerpPreviewUrl($pageUid, $sysLanguageUid, $customUrl);

        $title = false === empty($page['seo_title'])
            ? $page['seo_title']
            : $page['title'];

        return [
            'uid' => $pageUid,
            'title' => $title,
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

    public function getSerpPreviewUrl($pageUid, $sysLanguageUid, $customUrl = "") {
        $baseUri = '' !== $customUrl ? $customUrl : GeneralUtility::getIndpEnv('TYPO3_REQUEST_HOST');
        $baseUri = str_replace('https://', "", rtrim($baseUri, '/'));
        $pageUrlNonAbsolute = parse_url($this->getPageLink($pageUid, false, $sysLanguageUid), PHP_URL_PATH);
        $uri = $baseUri . $pageUrlNonAbsolute;

        if ($pageUrlNonAbsolute == "/") return $baseUri;

        if ($this->uriIsTooLong($uri)) {
            if ($this->uriPathTooLong($uri)) {
                $parts = explode("/", $pageUrlNonAbsolute);
                $uri = $baseUri . "/.../" . $parts[count($parts) -1];
                if ($this->uriIsTooLong($uri)) {
                    $uri = substr($uri, 0, 60) . "...";
                }
            } else {
                $uri = substr($uri, 0, 60) . "...";
            }
        }

        return $this->formatUriForPreview($uri);
    }

    public function formatUriForPreview($uri) {
        return str_replace("/", " › ", rtrim($uri, '/'));
    }

    public function uriIsTooLong($uri) {
        return (strlen($uri) >= 57);
    }

    public function uriPathTooLong($uri) {
       $parts = explode("/", $uri);
       foreach ($parts as $part) {
           if (strlen($part) > 28) {
               return true;
           }
       }
       return false;
    }

    /**
     * @param int $pageUid
     * @param int $sysLanguageUid
     * @param string $customUrl
     * @return array
     */
    public function getSubpagesMetaData($pageUid, $sysLanguageUid = 0, $customUrl = '')
    {
        $metadata = [];

        foreach ($this->getSubPagesFromPageUid($pageUid) as $subPage) {
            if (1 !== (int) $subPage['doktype'] && 4 !== (int) $subPage['doktype']) {
                continue;
            }

            if ((int) $subPage['uid'] !== $pageUid) {
                $metadata[] = $this->getPageMetaData($subPage['uid'], $sysLanguageUid, $customUrl);
            }
        }

        return $metadata;
    }

    /**
     * @return array
     */
    public function getRootpage()
    {
        $rootline = $this->getRootlineReverse();

        return $rootline[0];
    }

    /**
     * @param int $pageUid
     * @param int $sysLanguageUid
     * @return array
     * @throws \TYPO3\CMS\Extbase\Object\Exception
     */
    public function getRootline($pageUid = null, $sysLanguageUid = 0)
    {
        $pages = [];

        $currentPageUid = MindshapeBackendUtility::getCurrentPageTreeSelectedPage();

        if (null === $pageUid) {
            if (0 < (int) $this->typoScriptFrontendController->id) {
                $pageUid = (int) $this->typoScriptFrontendController->id;
            } elseif (0 < $currentPageUid) {
                $pageUid = $currentPageUid;
            }
        }

        foreach (ObjectUtility::makeInstance(RootlineUtility::class, $pageUid)->get() as $page) {
            $pages[] = $this->getPage($page['uid'], $sysLanguageUid);
        }

        return $pages;
    }

    /**
     * @param int $pageUid
     * @param bool $withCurrentPage
     * @param bool $withRootPage
     * @param int $sysLanguageUid
     * @return array
     * @throws \TYPO3\CMS\Extbase\Object\Exception
     */
    public function getRootlineReverse($pageUid = null, $withCurrentPage = false, $withRootPage = true, $sysLanguageUid = 0)
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
     * @return array
     */
    public function getSubPageUidsFromPageUid($pageUid)
    {
        /** @var \TYPO3\CMS\Core\Database\QueryGenerator $queryGenerator */
        $queryGenerator = GeneralUtility::makeInstance(QueryGenerator::class);

        return GeneralUtility::intExplode(
            ',',
            $queryGenerator->getTreeList($pageUid, 9999999, 0, 1)
        );
    }

    /**
     * @param int $pageUid
     * @return array
     */
    public function getSubPagesFromPageUid($pageUid)
    {
        $pages = [];

        foreach ($this->getSubPageUidsFromPageUid($pageUid) as $uid) {
            $pages[] = $this->pageRepository->getPage($uid);
        }

        return $pages;
    }

    /**
     * @param int $pageUid
     * @param int $depth
     * @param int $sysLanguageUid
     * @param string $customUrl
     * @param bool $useGoogleBreadcrumb
     * @param int[] $allowedDoktypes
     * @return array
     */
    public function getPageMetadataTree(
        int $pageUid,
        int $depth = self::TREE_DEPTH_DEFAULT,
        int $sysLanguageUid = 0,
        string $customUrl = '',
        bool $useGoogleBreadcrumb = false,
        array $allowedDoktypes = [1,4]
    )
    {
        $page = $this->getPage($pageUid, $sysLanguageUid);

        if (false === is_array($page)) {
            return null;
        }

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

        $html = $iconFactory->getIconForRecord(
            'pages',
            $page,
            Icon::SIZE_SMALL
        );

        $tree->tree[] = [
            'row' => $page,
            'HTML' => $html,
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
                $treeItem['hasSub'] &&
                self::$pageTreeDepth - $treeItem['invertedDepth'] === self::$pageTreeDepth - 1
            ) {
                $tree->tree[$key]['hasSub'] = false;
            }

            $metadata = $this->getPageMetaData(
                $treeItem['row']['uid'],
                $sysLanguageUid,
                $customUrl,
                $useGoogleBreadcrumb
            );

            if (false === is_array($metadata)) {
                unset($tree->tree[$key]);
                continue;
            }

            $tree->tree[$key]['metadata'] = $metadata;
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
     */
    protected function pageInheritedProperty($pageUid, $property)
    {
        $inherited = false;
        $inheritedPageUid = false;

        foreach ($this->getRootlineReverse($pageUid) as $page) {
            if ($pageUid !== (int) $page['uid']) {
                $inherited = (bool) $page[$property] ? !$inherited : $inherited;
                $inheritedPageUid = $inherited ? (int) $page['uid'] : false;
            }
        }

        return $inheritedPageUid;
    }
}
