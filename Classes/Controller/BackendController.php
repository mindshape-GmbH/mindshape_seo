<?php
namespace Mindshape\MindshapeSeo\Controller;

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
use Mindshape\MindshapeSeo\Domain\Repository\ConfigurationRepository;
use Mindshape\MindshapeSeo\Property\TypeConverter\UploadedFileReferenceConverter;
use Mindshape\MindshapeSeo\Service\DomainService;
use Mindshape\MindshapeSeo\Service\LanguageService;
use Mindshape\MindshapeSeo\Service\SessionService;
use Mindshape\MindshapeSeo\Utility\BackendUtility;
use Mindshape\MindshapeSeo\Service\PageService;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Utility\BackendUtility as CoreBackendUtility;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * @package mindshape_seo
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class BackendController extends ActionController
{
    /**
     * @var \Mindshape\MindshapeSeo\Domain\Repository\ConfigurationRepository
     */
    protected $configurationRepository;

    /**
     * @var \Mindshape\MindshapeSeo\Service\DomainService
     */
    protected $domainService;

    /**
     * @var \Mindshape\MindshapeSeo\Service\PageService
     */
    protected $pageService;

    /**
     * @var \Mindshape\MindshapeSeo\Service\LanguageService
     */
    protected $languageService;

    /**
     * @var \Mindshape\MindshapeSeo\Service\SessionService
     */
    protected $sessionService;

    /**
     * @var \TYPO3\CMS\Core\Imaging\IconFactory
     */
    protected $iconFactory;

    /**
     * @var \TYPO3\CMS\Backend\Template\Components\ButtonBar
     */
    protected $buttonBar;

    /**
     * @var \TYPO3\CMS\Backend\View\BackendTemplateView
     */
    protected $view;

    /**
     * @var \TYPO3\CMS\Backend\View\BackendTemplateView
     */
    protected $defaultViewObjectName = BackendTemplateView::class;

    /**
     * @var int
     */
    protected $currentPageUid;

    /**
     * @param \Mindshape\MindshapeSeo\Domain\Repository\ConfigurationRepository $configurationRepository
     * @return void
     */
    public function injectConfigurationRepository(ConfigurationRepository $configurationRepository)
    {
        $this->configurationRepository = $configurationRepository;
    }

    /**
     * @param \Mindshape\MindshapeSeo\Service\DomainService $domainService
     * @return void
     */
    public function injectDomainService(DomainService $domainService)
    {
        $this->domainService = $domainService;
    }

    /**
     * @param \Mindshape\MindshapeSeo\Service\LanguageService $languageService
     * @return void
     */
    public function injectLanguageService(LanguageService $languageService)
    {
        $this->languageService = $languageService;
    }

    /**
     * @param \Mindshape\MindshapeSeo\Service\PageService $pageService
     * @return void
     */
    public function injectPageService(PageService $pageService)
    {
        $this->pageService = $pageService;
    }

    /**
     * @param \Mindshape\MindshapeSeo\Service\SessionService $sessionService
     * @return void
     */
    public function injectSessionService(SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    /**
     * @param \TYPO3\CMS\Core\Imaging\IconFactory $iconFactory
     * @®return void
     */
    public function injectIconFactory(IconFactory $iconFactory)
    {
        $this->iconFactory = $iconFactory;
    }

    /**
     * @return void
     */
    protected function initializeAction()
    {
        $this->currentPageUid = BackendUtility::getCurrentPageTreeSelectedPage();

        $this->settings = $this->configurationManager->getConfiguration(ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'mindshape_seo');
    }

    /**
     * @param \TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view
     * @return void
     */
    protected function initializeView(ViewInterface $view)
    {
        /** @var \TYPO3\CMS\Backend\View\BackendTemplateView $view */
        parent::initializeView($view);

        $currentAction = $this->request->getControllerActionName();

        if (
            $currentAction === 'settings' ||
            $currentAction === 'preview'
        ) {
            $this->buttonBar = $this->view->getModuleTemplate()->getDocHeaderComponent()->getButtonBar();
            $view->getModuleTemplate()->getDocHeaderComponent()->setMetaInformation([]);

            $pageRenderer = $this->view->getModuleTemplate()->getPageRenderer();
            $pageRenderer->loadRequireJsModule('TYPO3/CMS/Backend/Severity');
            $pageRenderer->loadRequireJsModule('TYPO3/CMS/Backend/Modal');

            if (GeneralUtility::getApplicationContext()->isProduction()) {
                $pageRenderer->addCssFile('/typo3conf/ext/mindshape_seo/Resources/Public/css/backend.min.css');
                $pageRenderer->addJsFile('/typo3conf/ext/mindshape_seo/Resources/Public/js/backend.min.js');
            } else {
                $pageRenderer->addCssFile(
                    '/typo3conf/ext/mindshape_seo/Resources/Public/css/backend.min.css',
                    'stylesheet',
                    'all',
                    '',
                    false,
                    false,
                    '',
                    true
                );
                $pageRenderer->addJsFile(
                    '/typo3conf/ext/mindshape_seo/Resources/Public/js/backend.min.js',
                    'text/javascript',
                    false,
                    false,
                    '',
                    true
                );
            }
        }

        if ($currentAction === 'settings') {
            $domains = $this->domainService->getAvailableDomains();

            if (2 <= count($domains)) {
                $this->buildDomainMenu($domains);
            }

            $this->buildButtons();
        }

        if ($currentAction === 'preview') {
            $languages = $this->languageService->getPageLanguagesAvailable($this->currentPageUid);

            if (0 < count($languages)) {
                $this->buildLanguageMenu($languages);
            } else {
                $this->arguments->addNewArgument('sysLanguageUid', 'int', false, 0);
            }
        }
    }

    /**
     * @param array $domains
     * @return void
     */
    protected function buildDomainMenu(array $domains)
    {
        /** @var \TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder $uriBuilder */
        $uriBuilder = $this->objectManager->get(UriBuilder::class);
        $uriBuilder->setRequest($this->request);

        $menu = $this->view->getModuleTemplate()->getDocHeaderComponent()->getMenuRegistry()->makeMenu();
        $menu->setIdentifier('mindshape_seo');

        $arguments = $this->request->getArguments();

        if (array_key_exists('domain', $arguments)) {
            $currentDomain = $arguments['domain'];
        } else {
            $currentDomain = $this->sessionService->hasKey('domain') ?
                $this->sessionService->getKey('domain') :
                Configuration::DEFAULT_DOMAIN;
        }

        foreach ($domains as $domain) {
            $menu->addMenuItem(
                $menu->makeMenuItem()
                    ->setTitle(
                        Configuration::DEFAULT_DOMAIN === $domain
                            ? LocalizationUtility::translate('tx_mindshapeseo_domain_model_configuration.domain.default', 'mindshape_seo')
                            : $domain
                    )
                    ->setHref($uriBuilder->reset()->uriFor('settings', array('domain' => $domain), 'Backend'))
                    ->setActive($currentDomain === $domain)
            );
        }

        /** @var \Mindshape\MindshapeSeo\Domain\Model\Configuration $configuration */
        foreach ($this->configurationRepository->findAll() as $configuration) {
            if (false === in_array($configuration->getDomain(), $domains, true)) {
                $menu->addMenuItem(
                    $menu->makeMenuItem()
                        ->setTitle($configuration->getDomain())
                        ->setHref($uriBuilder->reset()->uriFor('settings', array('domain' => $configuration->getDomain()), 'Backend'))
                        ->setActive($currentDomain === $configuration->getDomain())
                );
            }
        }

        $this->view->getModuleTemplate()->getDocHeaderComponent()->getMenuRegistry()->addMenu($menu);
    }

    /**
     * @param array $languages
     * @return void
     */
    protected function buildLanguageMenu(array $languages)
    {
        /** @var \TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder $uriBuilder */
        $uriBuilder = $this->objectManager->get(UriBuilder::class);
        $uriBuilder->setRequest($this->request);

        $menu = $this->view->getModuleTemplate()->getDocHeaderComponent()->getMenuRegistry()->makeMenu();
        $menu->setIdentifier('mindshape_seo');

        $arguments = $this->request->getArguments();

        if (array_key_exists('sysLanguageUid', $arguments)) {
            $sysLanguageUid = (int) $arguments['sysLanguageUid'];
        } else {
            $sysLanguageUid = $this->sessionService->hasKey('sysLanguageUid')
                ? $this->sessionService->getKey('sysLanguageUid')
                : 0;
        }

        $defaultMenuItem = $menu->makeMenuItem()
            ->setTitle(LocalizationUtility::translate('tx_mindshapeseo_label.default_language', 'mindshape_seo'))
            ->setHref($uriBuilder->reset()->uriFor('preview', array('sysLanguageUid' => 0), 'Backend'))
            ->setActive(0 === $sysLanguageUid);

        $menu->addMenuItem($defaultMenuItem);

        foreach ($languages as $language) {
            $menu->addMenuItem(
                $menu->makeMenuItem()
                    ->setTitle($language['title'])
                    ->setHref($uriBuilder->reset()->uriFor('preview', array('sysLanguageUid' => $language['uid']), 'Backend'))
                    ->setActive($sysLanguageUid === (int) $language['uid'])
            );
        }

        $this->view->getModuleTemplate()->getDocHeaderComponent()->getMenuRegistry()->addMenu($menu);
    }

    /**
     * @return void
     */
    protected function buildButtons()
    {
        $saveButton = $this->buttonBar->makeLinkButton()
            ->setClasses('mindshape-seo-savebutton')
            ->setHref('#')
            ->setTitle(LocalizationUtility::translate('tx_mindshapeseo_label.save', 'mindshape_seo'))
            ->setIcon($this->iconFactory->getIcon('actions-document-save', Icon::SIZE_SMALL));

        $this->buttonBar->addButton($saveButton, ButtonBar::BUTTON_POSITION_LEFT, 1);
    }

    /**
     * @param string $domain
     * @return void
     */
    public function settingsAction($domain = null)
    {
        if (null === $domain) {
            $domain = $this->sessionService->hasKey('domain') ?
                $this->sessionService->getKey('domain') :
                Configuration::DEFAULT_DOMAIN;
        } else {
            $this->sessionService->setKey('domain', $domain);
        }

        $domains = $this->domainService->getAvailableDomains();

        if (0 === count($domains)) {
            $domain = '*';
        }

        $configuration = $this->configurationRepository->findByDomain($domain);

        if (!$configuration instanceof Configuration) {
            $configuration = new Configuration();
            $configuration->setDomain($domain);
            $configuration->setTitleAttachmentSeperator(Configuration::DEFAULT_TITLE_ATTACHMENT_SEPERATOR);
            $configuration->setTitleAttachmentPosition(Configuration::TITLE_ATTACHMENT_POSITION_SUFFIX);
        } elseif (0 === count($domains)) {
            $configuration->setDomain(Configuration::DEFAULT_DOMAIN);
        }

        if (false === $configuration->_isNew()) {
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
                    'redirect-url' => CoreBackendUtility::getModuleUrl('mindshapeseo_MindshapeSeoSettings'),
                ]);

            $this->buttonBar->addButton($deleteButton, ButtonBar::BUTTON_POSITION_LEFT, 1);
        }

        $this->view->assignMultiple(array(
            'domains' => $domains,
            'domainsSelectOptions' => $this->domainService->getConfigurationDomainSelectOptions($domain),
            'currentDomain' => $domain === Configuration::DEFAULT_DOMAIN ?
                GeneralUtility::getIndpEnv('HTTP_HOST') :
                $domain,
            'configuration' => $configuration,
            'titleAttachmentPositionOptions' => array(
                Configuration::TITLE_ATTACHMENT_POSITION_SUFFIX => LocalizationUtility::translate('tx_mindshapeseo_domain_model_configuration.title_attachment_position.suffix', 'mindshape_seo'),
                Configuration::TITLE_ATTACHMENT_POSITION_PREFIX => LocalizationUtility::translate('tx_mindshapeseo_domain_model_configuration.title_attachment_position.prefix', 'mindshape_seo'),
            ),
            'jsonldTypeOptions' => array(
                Configuration::JSONLD_TYPE_ORGANIZATION => LocalizationUtility::translate('tx_mindshapeseo_domain_model_configuration.jsonld.type.organization', 'mindshape_seo'),
                Configuration::JSONLD_TYPE_PERSON => LocalizationUtility::translate('tx_mindshapeseo_domain_model_configuration.jsonld.type.person', 'mindshape_seo'),
            ),
            'domainUrl' => (GeneralUtility::getIndpEnv('TYPO3_SSL') ? 'https' : 'http') . '://' . ($domain !== Configuration::DEFAULT_DOMAIN ? $domain : GeneralUtility::getIndpEnv('HTTP_HOST')),
            'robotsTxtNotExists' => !file_exists(PATH_site . '/robots.txt'),
        ));
    }

    /**
     * @return void
     */
    public function initializeSaveConfigurationAction()
    {
        $this->setTypeConverterConfigurationForImageUpload('configuration');
    }

    /**
     * @param \Mindshape\MindshapeSeo\Domain\Model\Configuration $configuration
     * @validate $configuration \Mindshape\MindshapeSeo\Validation\Validator\ConfigurationValidator
     * @return void
     */
    public function saveConfigurationAction(Configuration $configuration)
    {
        $this->configurationRepository->save($configuration);
        $this->sessionService->setKey('domain', $configuration->getDomain());

        $this->redirect(
            'settings',
            'Backend',
            null,
            array(
                'domain' => $configuration->getDomain(),
            )
        );
    }

    /**
     * @param int $depth
     * @param int $sysLanguageUid
     * @return void
     */
    public function previewAction($depth = null, $sysLanguageUid = null)
    {
        $currentPage = $this->pageService->getCurrentPage();

        if (
            0 === $this->currentPageUid ||
            (bool) $currentPage['hidden'] ||
            (
                1 !== (int) $currentPage['doktype'] &&
                4 !== (int) $currentPage['doktype']
            )
        ) {
            $this->view->assign('noPageSelected', true);
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

            $configuration = $this->domainService->getPageDomainConfiguration();

            if ($configuration instanceof Configuration) {
                $this->view->assignMultiple(array(
                    'pageTree' => $this->pageService->getPageMetadataTree(
                        $this->currentPageUid,
                        $depth,
                        $sysLanguageUid,
                        $configuration->getJsonldCustomUrl(),
                        $configuration->getAddJsonldBreadcrumb()
                    ),
                    'titleAttachment' => $configuration->getTitleAttachment(),
                    'titleAttachmentSeperator' => $configuration->getTitleAttachmentSeperator(),
                    'titleAttachmentPosition' => $configuration->getTitleAttachmentPosition(),
                ));
            } else {
                $this->view->assign(
                    'pageTree',
                    $this->pageService->getPageMetadataTree(
                        $this->currentPageUid,
                        $depth,
                        $sysLanguageUid
                    )
                );
            }

            $this->view->assignMultiple(array(
                'sysLanguageUid' => $sysLanguageUid,
                'depth' => $depth,
                'levelOptions' => array(
                    PageService::TREE_DEPTH_INFINITY => LocalizationUtility::translate('tx_mindshapeseo_label.preview.levels.infinity', 'mindshape_seo'),
                    0 => '0',
                    1 => '1',
                    2 => '2',
                    3 => '3',
                    4 => '4',
                    5 => '5',
                    6 => '6',
                    7 => '7',
                    8 => '8',
                    9 => '9',
                    10 => '10',
                ),
            ));
        }
    }

    /**
     * @param string $argumentName
     * @return void
     */
    protected function setTypeConverterConfigurationForImageUpload($argumentName)
    {
        $uploadConfiguration = array(
            UploadedFileReferenceConverter::CONFIGURATION_ALLOWED_FILE_EXTENSIONS => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
            UploadedFileReferenceConverter::CONFIGURATION_UPLOAD_FOLDER => '1:/mindshape_seo/',
        );

        /** @var \TYPO3\CMS\Extbase\Property\PropertyMappingConfiguration $newExampleConfiguration */
        $newExampleConfiguration = $this->arguments[$argumentName]->getPropertyMappingConfiguration();
        $newExampleConfiguration
            ->forProperty('facebookDefaultImage')
            ->setTypeConverterOptions(
                UploadedFileReferenceConverter::class,
                $uploadConfiguration
            )
            ->forProperty('jsonldLogo')
            ->setTypeConverterOptions(
                UploadedFileReferenceConverter::class,
                $uploadConfiguration
            );
    }
}
