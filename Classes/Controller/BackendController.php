<?php

namespace Mindshape\MindshapeSeo\Controller;

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

use Mindshape\MindshapeSeo\Domain\Model\Configuration;
use Mindshape\MindshapeSeo\Domain\Repository\ConfigurationRepository;
use Mindshape\MindshapeSeo\Service\DomainService;
use Mindshape\MindshapeSeo\Service\LanguageService;
use Mindshape\MindshapeSeo\Service\SessionService;
use Mindshape\MindshapeSeo\Service\TranslationService;
use Mindshape\MindshapeSeo\Service\PageService;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;
use TYPO3\CMS\Backend\Routing\UriBuilder as BackendUriBuilder;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Page\JavaScriptModuleInstruction;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Pagination\ArrayPaginator;
use TYPO3\CMS\Core\Pagination\SimplePagination;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * @package mindshape_seo
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class BackendController extends ActionController
{
    /**
     * @var \TYPO3\CMS\Backend\Template\ModuleTemplate
     */
    protected ModuleTemplate $moduleTemplate;

    /**
     * @var \TYPO3\CMS\Backend\Template\Components\ButtonBar
     */
    protected ButtonBar $buttonBar;

    /**
     * @param \TYPO3\CMS\Backend\Template\ModuleTemplateFactory $moduleTemplateFactory
     * @param \Mindshape\MindshapeSeo\Domain\Repository\ConfigurationRepository $configurationRepository
     * @param \Mindshape\MindshapeSeo\Service\DomainService $domainService
     * @param \Mindshape\MindshapeSeo\Service\PageService $pageService
     * @param \Mindshape\MindshapeSeo\Service\LanguageService $languageService
     * @param \Mindshape\MindshapeSeo\Service\SessionService $sessionService
     * @param \Mindshape\MindshapeSeo\Service\TranslationService $translationService
     * @param \TYPO3\CMS\Core\Imaging\IconFactory $iconFactory
     * @param \TYPO3\CMS\Core\Page\PageRenderer $pageRenderer
     */
    public function __construct(
        protected ModuleTemplateFactory $moduleTemplateFactory,
        protected ConfigurationRepository $configurationRepository,
        protected DomainService $domainService,
        protected PageService $pageService,
        protected LanguageService $languageService,
        protected SessionService $sessionService,
        protected TranslationService $translationService,
        protected IconFactory $iconFactory,
        protected PageRenderer $pageRenderer
    ) {
    }

    protected function initializeAction(): void
    {
        $this->moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $this->settings = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS, 'mindshapeseo');

        $this->buttonBar = $this->moduleTemplate->getDocHeaderComponent()->getButtonBar();
        $this->moduleTemplate->getDocHeaderComponent()->setMetaInformation([]);

        if (Environment::getContext()->isProduction()) {
            $this->pageRenderer->addCssFile('EXT:mindshape_seo/Resources/Public/StyleSheets/backend.css');
        } else {
            $this->pageRenderer->addCssFile(
                'EXT:mindshape_seo/Resources/Public/StyleSheets/backend.css',
                'stylesheet',
                'all',
                '',
                false,
                false,
                '',
                true
            );
        }
    }

    /**
     * @param array $domains
     */
    protected function buildDomainMenu(array $domains): void
    {
        /** @var \TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder $uriBuilder */
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        $uriBuilder->setRequest($this->request);

        $menu = $this->moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->makeMenu();
        $menu->setIdentifier('mindshape_seo-DomainMenu');;

        $arguments = $this->request->getArguments();

        if (array_key_exists('domain', $arguments)) {
            $currentDomain = $arguments['domain'];
        } else {
            $currentDomain = $this->sessionService->hasKey('domain') ?
                $this->sessionService->getKey('domain') :
                Configuration::DEFAULT_DOMAIN;
        }

        if (array_key_exists('sysLanguageUid', $arguments)) {
            $sysLanguageUid = (int) $arguments['sysLanguageUid'];
        } else {
            $sysLanguageUid = $this->sessionService->hasKey('sysLanguageUid')
                ? $this->sessionService->getKey('sysLanguageUid')
                : 0;
        }

        foreach ($domains as $domain) {
            $menu->addMenuItem(
                $menu->makeMenuItem()
                    ->setTitle(
                        Configuration::DEFAULT_DOMAIN === $domain
                            ? LocalizationUtility::translate('tx_mindshapeseo_domain_model_configuration.domain.default', 'mindshape_seo')
                            : $domain
                    )
                    ->setHref($uriBuilder->reset()->uriFor('settings', [
                        'domain' => $domain,
                        'sysLanguageUid' => $sysLanguageUid,
                    ], 'Backend'))
                    ->setActive($currentDomain === $domain)
            );
        }

        /** @var \Mindshape\MindshapeSeo\Domain\Model\Configuration $configuration */
        foreach ($this->configurationRepository->findAll() as $configuration) {
            if (false === in_array($configuration->getDomain(), $domains, true)) {
                $menu->addMenuItem(
                    $menu->makeMenuItem()
                        ->setTitle($configuration->getDomain())
                        ->setHref($uriBuilder->reset()->uriFor('settings', ['domain' => $configuration->getDomain()], 'Backend'))
                        ->setActive($currentDomain === $configuration->getDomain())
                );
            }
        }

        $this->moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->addMenu($menu);
    }

    /**
     * @param array $languages
     * @param string|null $domain
     */
    protected function buildLanguageMenu(array $languages, string $domain = null): void
    {
        /** @var \TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder $uriBuilder */
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        $uriBuilder->setRequest($this->request);

        $menu = $this->moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->makeMenu();
        $menu->setIdentifier('mindshape_seo-languageMenu');

        $arguments = $this->request->getArguments();
        $currentAction = $this->request->getControllerActionName();

        if (array_key_exists('sysLanguageUid', $arguments)) {
            $sysLanguageUid = (int) $arguments['sysLanguageUid'];
        } else {
            $sysLanguageUid = $this->sessionService->hasKey('sysLanguageUid')
                ? $this->sessionService->getKey('sysLanguageUid')
                : 0;
        }

        $defaultMenuItemParameters = ['sysLanguageUid' => 0];

        if (true === is_string($domain)) {
            $defaultMenuItemParameters['domain'] = $domain;
        }

        $defaultMenuItem = $menu->makeMenuItem()
            ->setTitle(LocalizationUtility::translate('tx_mindshapeseo_label.default_language', 'mindshape_seo'))
            ->setHref($uriBuilder->reset()->uriFor($currentAction, $defaultMenuItemParameters, 'Backend'))
            ->setActive(0 === $sysLanguageUid);

        $menu->addMenuItem($defaultMenuItem);

        foreach ($languages as $language) {
            $menuItemParameters = ['sysLanguageUid' => $language['uid']];

            $menu->addMenuItem(
                $menu->makeMenuItem()
                    ->setTitle($language['title'])
                    ->setHref($uriBuilder->reset()->uriFor($currentAction, $menuItemParameters, 'Backend'))
                    ->setActive($sysLanguageUid === (int) $language['uid'])
            );
        }

        $this->moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->addMenu($menu);
    }

    protected function buildButtons(): void
    {
        $saveButton = $this->buttonBar->makeLinkButton()
            ->setClasses('mindshape-seo-savebutton')
            ->setHref('#')
            ->setTitle(LocalizationUtility::translate('tx_mindshapeseo_label.save', 'mindshape_seo'))
            ->setIcon($this->iconFactory->getIcon('actions-document-save', Icon::SIZE_SMALL));

        $this->buttonBar->addButton($saveButton);
    }

    /**
     * @param string|null $domain
     * @param int|null $sysLanguageUid
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Doctrine\DBAL\Exception
     * @throws \TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     */
    public function settingsAction(string $domain = null, int $sysLanguageUid = null): ResponseInterface
    {
        $this->pageRenderer->loadJavaScriptModule('@mindshape/mindshape-seo/SettingsModule.js');

        $domains = $this->domainService->getAvailableDomains();

        if (2 <= count($domains)) {
            $this->buildDomainMenu($domains);
        }

        $languages = $this->languageService->getLanguagesAvailable();

        if (0 < count($languages)) {
            $this->buildLanguageMenu(
                $languages,
                $this->getCurrentDomain(
                    $this->arguments->getArgument('domain')->getValue()
                )
            );
        } else {
            $this->arguments->addNewArgument('sysLanguageUid', 'int', false, 0);
        }

        $this->buildButtons();

        $domain = $this->getCurrentDomain($domain);

        if (null === $sysLanguageUid) {
            $sysLanguageUid = $this->sessionService->hasKey('sysLanguageUid')
                ? $this->sessionService->getKey('sysLanguageUid')
                : 0;
        } else {
            $this->sessionService->setKey('sysLanguageUid', $sysLanguageUid);
        }

        $domains = $this->domainService->getAvailableDomains();

        if (0 === count($domains)) {
            $domain = '*';
        }

        $defaultConfiguration = null;
        $configuration = $this->configurationRepository->findByDomain($domain, false, $sysLanguageUid);

        if (!$configuration instanceof Configuration) {
            $configuration = new Configuration();
            $configuration->setDomain($domain);
            $configuration->setTitleAttachmentSeperator(Configuration::DEFAULT_TITLE_ATTACHMENT_SEPERATOR);
            $configuration->setTitleAttachmentPosition(Configuration::TITLE_ATTACHMENT_POSITION_SUFFIX);
        } elseif (0 === count($domains)) {
            $configuration->setDomain(Configuration::DEFAULT_DOMAIN);
        }

        if (Configuration::DEFAULT_DOMAIN !== $configuration->getDomain()) {
            $defaultConfiguration = $this->configurationRepository->findByDomain(
                Configuration::DEFAULT_DOMAIN,
                false,
                $sysLanguageUid
            );
        }

        if (false === $configuration->_isNew()) {
            /** @var \TYPO3\CMS\Backend\Routing\UriBuilder $uriBuilder */
            $uriBuilder = GeneralUtility::makeInstance(BackendUriBuilder::class);

            try {
                $redirectUrl = (string) $uriBuilder->buildUriFromRoute('mindshapeseo_settings');
            } catch (RouteNotFoundException) {
                $redirectUrl = (string) $uriBuilder->buildUriFromRoutePath('/module/MindshapeSeoMindshapeseo/MindshapeSeoSettings');
            }

            $deleteButton = $this->buttonBar->makeLinkButton()
                ->setClasses('mindshape-seo-deletebutton')
                ->setHref('#')
                ->setTitle(LocalizationUtility::translate('tx_mindshapeseo_label.delete', 'mindshape_seo'))
                ->setIcon($this->iconFactory->getIcon('actions-edit-delete', Icon::SIZE_SMALL))
                ->setDataAttributes([
                    'uid' => $configuration->getUid(),
                    'message' => LocalizationUtility::translate('tx_mindshapeseo_label.delete_configuration', 'mindshape_seo'),
                    'label-abort' => LocalizationUtility::translate('tx_mindshapeseo_label.abort', 'mindshape_seo'),
                    'label-delete' => LocalizationUtility::translate('tx_mindshapeseo_label.delete', 'mindshape_seo'),
                    'redirect-url' => $redirectUrl,
                ]);

            $this->buttonBar->addButton($deleteButton);
        }

        $robotsTxtNotExists = true;
        $robotsContent = false;
        $currentDomain = $domain === Configuration::DEFAULT_DOMAIN ? GeneralUtility::getIndpEnv('HTTP_HOST') : $domain;

        if (file_exists(Environment::getPublicPath() . '/robots.txt')) {
            $robotsTxtNotExists = false;
        }

        if ($robotsTxtNotExists === true) {
            /** @var \TYPO3\CMS\Core\Site\SiteFinder $siteFinder */
            $siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
            /** @var \TYPO3\CMS\Core\Site\Entity\Site $allSites */
            $allSites = $siteFinder->getAllSites();

            /** @var \TYPO3\CMS\Core\Site\Entity\Site $site */
            foreach ($allSites as $site) {
                if ($site->getBase()->getHost() === $currentDomain) {
                    $siteConf = $site->getConfiguration();
                    $routes = $siteConf['routes'] ?? null;

                    if (true === is_array($routes)) {
                        foreach ($routes as $route) {
                            if ($route['route'] === 'robots.txt') {
                                $robotsTxtNotExists = false;
                                $robotsContent = $route['content'] ?? '';
                                break;
                            }
                        }
                    }

                    break;
                }
            }
        }

        $this->moduleTemplate->assignMultiple([
            'typo3Version' => GeneralUtility::makeInstance(Typo3Version::class)->getMajorVersion(),
            'domains' => $domains,
            'domainsSelectOptions' => $this->domainService->getConfigurationDomainSelectOptions($domain),
            'currentDomain' => $currentDomain,
            'defaultConfiguration' => $defaultConfiguration,
            'configuration' => $configuration,
            'languageUid' => $sysLanguageUid,
            'titleAttachmentPositionOptions' => [
                Configuration::TITLE_ATTACHMENT_POSITION_SUFFIX => LocalizationUtility::translate('tx_mindshapeseo_domain_model_configuration.title_attachment_position.suffix', 'mindshape_seo'),
                Configuration::TITLE_ATTACHMENT_POSITION_PREFIX => LocalizationUtility::translate('tx_mindshapeseo_domain_model_configuration.title_attachment_position.prefix', 'mindshape_seo'),
            ],
            'jsonldTypeOptions' => [
                Configuration::JSONLD_TYPE_ORGANIZATION => LocalizationUtility::translate('tx_mindshapeseo_domain_model_configuration.jsonld.type.organization', 'mindshape_seo'),
                Configuration::JSONLD_TYPE_PERSON => LocalizationUtility::translate('tx_mindshapeseo_domain_model_configuration.jsonld.type.person', 'mindshape_seo'),
            ],
            'domainUrl' => (GeneralUtility::getIndpEnv('TYPO3_SSL') ? 'https' : 'http') . '://' . ($domain !== Configuration::DEFAULT_DOMAIN ? $domain : GeneralUtility::getIndpEnv('HTTP_HOST')),
            'robotsTxtNotExists' => $robotsTxtNotExists,
            'robotsTxtContent' => $robotsContent,
            'cookieExtensionIsActive' => ExtensionManagementUtility::isLoaded('mindshape_cookie_consent'),
        ]);

        $this->moduleTemplate->setTitle(LocalizationUtility::translate('LLL:EXT:mindshape_seo/Resources/Private/Language/locallang_backend_settings.xlf:mlang_tabs_tab'));

        return $this->moduleTemplate->renderResponse('Backend/Settings');
    }

    /**
     * @param \Mindshape\MindshapeSeo\Domain\Model\Configuration $configuration
     * @param int $languageUid
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Doctrine\DBAL\Exception
     * @throws \Mindshape\MindshapeSeo\Service\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     */
    public function saveConfigurationAction(Configuration $configuration, int $languageUid): ResponseInterface
    {
        if (0 < $languageUid && true === $configuration->_isNew()) {
            $this->translationService->translate($configuration, $languageUid);
        }

        $this->configurationRepository->save($configuration);
        $this->sessionService->setKey('domain', $configuration->getDomain());

        return $this->redirect(
            'settings',
            'Backend',
            null,
            [
                'domain' => $configuration->getDomain(),
            ]
        );
    }

    /**
     * @param int $currentPaginationPage
     * @param int|null $depth
     * @param int|null $sysLanguageUid
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Doctrine\DBAL\Exception
     * @throws \TYPO3\CMS\Core\Context\Exception\AspectNotFoundException
     */
    public function previewAction(int $currentPaginationPage = 1, ?int $depth = null, ?int $sysLanguageUid = null): ResponseInterface
    {
        $currentPageUid = (int)($this->request->getQueryParams()['id'] ?? 0);
        $this->pageRenderer->loadJavaScriptModule('@mindshape/mindshape-seo/PreviewModule.js');

        if ($currentPageUid === 0) {
            return $this->moduleTemplate->renderResponse('Backend/NoPageSelected');
        }

        $languages = $this->languageService->getPageLanguagesAvailable($currentPageUid);

        if (0 < count($languages)) {
            $this->buildLanguageMenu($languages);
        } else {
            $this->arguments->addNewArgument('sysLanguageUid', 'int', false, 0);
        }

        $currentPage = $this->pageService->getCurrentPage();
        $showHiddenPages = (bool) $this->settings['googlePreview']['showHiddenPages'];
        $respectDoktypes = GeneralUtility::intExplode(',', $this->settings['googlePreview']['respectDoktypes']);

        if (
            0 === $currentPageUid ||
            !in_array($currentPage['doktype'], $respectDoktypes) ||
            ($showHiddenPages === false && (bool) $currentPage['hidden'] === true)
        ) {
            if ($showHiddenPages === false && (bool) $currentPage['hidden'] === true) {
                $this->moduleTemplate->assign('pageHidden', true);
            } elseif (!in_array($currentPage['doktype'], $respectDoktypes)) {
                $this->moduleTemplate->assign('unsupportedDoktype', true);
            } else {
                $this->moduleTemplate->assign('noPageSelected', true);
            }
        } else {
            if (null === $depth) {
                $depth = $this->sessionService->hasKey('depth') ?
                    $this->sessionService->getKey('depth') :
                    PageService::TREE_DEPTH_DEFAULT;
            } else {
                $this->sessionService->setKey('depth', $depth);
            }

            if (null === $sysLanguageUid) {
                $sysLanguageUid = $this->sessionService->hasKey('sysLanguageUid') ?
                    $this->sessionService->getKey('sysLanguageUid') :
                    0;
            } else {
                $this->sessionService->setKey('sysLanguageUid', $sysLanguageUid);
            }

            $configuration = $this->domainService->getPageDomainConfiguration(null, $sysLanguageUid);

            if ($configuration instanceof Configuration) {
                $pageTree = $this->pageService->getPageMetadataTree(
                    $currentPageUid,
                    $depth,
                    $sysLanguageUid,
                    $configuration->getJsonldCustomUrl(),
                    $respectDoktypes
                );

                $this->moduleTemplate->assignMultiple([
                    'pageTree' => $pageTree,
                    'titleAttachment' => $configuration->getTitleAttachment(),
                    'titleAttachmentSeperator' => $configuration->getTitleAttachmentSeperator(),
                    'titleAttachmentPosition' => $configuration->getTitleAttachmentPosition(),
                ]);
            } else {
                $pageTree = $this->pageService->getPageMetadataTree(
                    $currentPageUid,
                    $depth,
                    $sysLanguageUid,
                    '',
                    $respectDoktypes
                );

                $this->moduleTemplate->assign('pageTree', $pageTree);
            }

            if (true === (bool) $this->settings['pageTree']['usePagination']) {
                $pageTreePaginator = new ArrayPaginator($pageTree, $currentPaginationPage, 10);
                $pageTreePagination = new SimplePagination($pageTreePaginator);

                $this->moduleTemplate->assignMultiple([
                    'pageTreePaginator' => $pageTreePaginator,
                    'pageTreePagination' => $pageTreePagination,
                ]);
            }

            $this->moduleTemplate->assignMultiple([
                'typo3Version' => GeneralUtility::makeInstance(Typo3Version::class)->getMajorVersion(),
                'sysLanguageUid' => $sysLanguageUid,
                'depth' => $depth,
                'levelOptions' => [
                    PageService::TREE_DEPTH_INFINITY => LocalizationUtility::translate('tx_mindshapeseo_label.preview.levels.infinity', 'mindshape_seo'),
                    ...range(0, 10)
                ],
            ]);
        }

        $this->moduleTemplate->setTitle(LocalizationUtility::translate('LLL:EXT:mindshape_seo/Resources/Private/Language/locallang_backend_preview.xlf:mlang_tabs_tab'));

        return $this->moduleTemplate->renderResponse('Backend/Preview');
    }

    /**
     * @param string|null $domain
     * @return string
     */
    protected function getCurrentDomain(string $domain = null): string
    {
        if (null === $domain) {
            $domain = $this->sessionService->hasKey('domain') ?
                $this->sessionService->getKey('domain') :
                Configuration::DEFAULT_DOMAIN;
        } else {
            $this->sessionService->setKey('domain', $domain);
        }

        return $domain;
    }
}
