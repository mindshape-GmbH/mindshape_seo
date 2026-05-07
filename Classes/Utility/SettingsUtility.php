<?php
declare(strict_types=1);

namespace Mindshape\MindshapeSeo\Utility;

/***
 *
 * This file is part of the "mindshape SEO" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2026 Daniel Dorndorf <dorndorf@mindshape.de>
 *
 ***/

use Psr\Http\Message\ServerRequestInterface;
use Throwable;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\TypoScript\FrontendTypoScript;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\TypoScript\IncludeTree\SysTemplateRepository;
use TYPO3\CMS\Core\TypoScript\IncludeTree\SysTemplateTreeBuilder;
use TYPO3\CMS\Core\TypoScript\IncludeTree\Traverser\IncludeTreeTraverser;
use TYPO3\CMS\Core\TypoScript\IncludeTree\Visitor\IncludeTreeAstBuilderVisitor;
use TYPO3\CMS\Core\TypoScript\Tokenizer\LossyTokenizer;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\RootlineUtility;

class SettingsUtility
{
    protected ?Site $site;
    protected ?int $pageId;
    protected ?ServerRequestInterface $request;
    protected ?array $typoScriptCache = null;

    public function __construct(
        ?Site $site = null,
        ?int $pageId = null,
        ?ServerRequestInterface $request = null
    ) {
        // Fallback to global request if no explicit request is given
        $this->request = $request ?? ($GLOBALS['TYPO3_REQUEST'] ?? null);
        // Fetch site from the resolved request if not explicitly given
        $this->site = $site ?? (
            // Ignore NullSite object in request
            $this->request?->getAttribute('site') instanceof Site
                ? $this->request?->getAttribute('site')
                : null
        );
        // Prefer routed page id, otherwise fall back to the site root page
        $routing = $this->request?->getAttribute('routing');
        $this->pageId = $pageId
            ?? ($routing instanceof PageArguments ? $routing->getPageId() : null)
            ?? $this->site?->getRootPageId();

        if (
            empty($this->pageId) &&
            empty($this->site) &&
            $this->request instanceof ServerRequestInterface &&
            ApplicationType::fromRequest($this->request)->isBackend()
        ) {
            $queryParameters = $this->request->getQueryParams();

            // Page ID from pagetree selection
            if ($pageid = (int) ($queryParameters['id'] ?? 0)) {
                $this->pageId = $pageId;
            // Page ID from edit page record view
            } elseif ($pageId = array_key_first($queryParameters['edit']['pages'] ?? [])) {
                $this->pageId = $pageId;
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function getExtensionTypoScript(): array
    {
        $typoScript = $this->loadTypoScriptSetup();
        $pluginTypoScript = $typoScript['plugin.']['tx_mindshapeseo.'] ?? [];

        if ($pluginTypoScript === []) {
            return [];
        }

        return GeneralUtility::makeInstance(TypoScriptService::class)
            ->convertTypoScriptArrayToPlainArray($pluginTypoScript);
    }

    /**
     * @return array<string, mixed>
     */
    protected function loadTypoScriptSetup(): array
    {
        if ($this->typoScriptCache !== null) {
            return $this->typoScriptCache;
        }

        // Check if current request already has the frontend TypoScript available
        if (
            $this->request instanceof ServerRequestInterface &&
            $this->request->getAttribute('frontend.typoscript') instanceof FrontendTypoScript &&
            $this->request->getAttribute('frontend.typoscript')->hasSetup()
        ) {
            return $this->typoScriptCache = $this->request->getAttribute('frontend.typoscript')->getSetupArray();
        }

        if (empty($this->pageId)) {
            return $this->typoScriptCache = [];
        } elseif (empty($this->site)) {
            /** @var \TYPO3\CMS\Core\Site\SiteFinder */
            $siteFinder = GeneralUtility::makeInstance(SiteFinder::class);

            try {
                $this->site = $siteFinder->getSiteByPageId($this->pageId);
            } catch (SiteNotFoundException) {
                return $this->typoScriptCache = [];
            }
        }

        try {
            $rootLine = GeneralUtility::makeInstance(RootlineUtility::class, $this->pageId)->get();
        } catch (Throwable) {
            return $this->typoScriptCache = [];
        }

        $sysTemplateRepository = GeneralUtility::makeInstance(SysTemplateRepository::class);
        $sysTemplateRows = $sysTemplateRepository->getSysTemplateRowsByRootline($rootLine);

        $treeBuilder = GeneralUtility::makeInstance(SysTemplateTreeBuilder::class);
        $tokenizer = new LossyTokenizer();

        $constantsTree = $treeBuilder->getTreeBySysTemplateRowsAndSite(
            'constants',
            $sysTemplateRows,
            $tokenizer,
            $this->site
        );

        $constantsAstBuilder = GeneralUtility::makeInstance(IncludeTreeAstBuilderVisitor::class);
        (new IncludeTreeTraverser())->traverse($constantsTree, [$constantsAstBuilder]);
        $flatConstants = $constantsAstBuilder->getAst()->flatten();

        $setupTree = $treeBuilder->getTreeBySysTemplateRowsAndSite(
            'setup',
            $sysTemplateRows,
            $tokenizer,
            $this->site
        );

        $setupAstBuilder = GeneralUtility::makeInstance(IncludeTreeAstBuilderVisitor::class);
        $setupAstBuilder->setFlatConstants($flatConstants);
        (new IncludeTreeTraverser())->traverse($setupTree, [$setupAstBuilder]);

        return $this->typoScriptCache = $setupAstBuilder->getAst()->toArray();
    }
}
