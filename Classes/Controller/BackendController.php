<?php
namespace Mindshape\MindshapeSeo\Controller;

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

use Mindshape\MindshapeSeo\Domain\Model\Configuration;
use Mindshape\MindshapeSeo\Property\TypeConverter\UploadedFileReferenceConverter;
use Mindshape\MindshapeSeo\Service\PageService;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Property\PropertyMappingConfiguration;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * @package mindshape_seo
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class BackendController extends ActionController
{
    /**
     * @var \Mindshape\MindshapeSeo\Domain\Repository\ConfigurationRepository
     * @inject
     */
    protected $configurationRepository;

    /**
     * @var \Mindshape\MindshapeSeo\Service\DomainService
     * @inject
     */
    protected $domainService;

    /**
     * @var \Mindshape\MindshapeSeo\Service\PageService
     * @inject
     */
    protected $pageService;

    /**
     * @var \Mindshape\MindshapeSeo\Service\LanguageService
     * @inject
     */
    protected $languageService;

    /**
     * @var \Mindshape\MindshapeSeo\Service\SessionService
     * @inject
     */
    protected $sessionService;

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
     * @return void
     */
    protected function initializeAction()
    {
        $this->currentPageUid = (int) GeneralUtility::_GET('id');
    }

    /**
     * @param \TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view
     * @return void
     */
    protected function initializeView(ViewInterface $view)
    {
        /** @var BackendTemplateView $view */
        parent::initializeView($view);

        $currentAction = $this->request->getControllerActionName();

        if (
            $currentAction === 'settings' ||
            $currentAction === 'preview'
        ) {
            $view->getModuleTemplate()->getDocHeaderComponent()->setMetaInformation([]);

            $pageRenderer = $this->view->getModuleTemplate()->getPageRenderer();
            $pageRenderer->loadJquery();
            $pageRenderer->addCssFile(ExtensionManagementUtility::extRelPath('mindshape_seo') . 'Resources/Public/css/backend.css');
            $pageRenderer->addJsFile(ExtensionManagementUtility::extRelPath('mindshape_seo') . 'Resources/Public/js/backend.js');
            $pageRenderer->setBackPath('../typo3/');
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

        foreach ($domains as $domain) {
            $menu->addMenuItem(
                $menu->makeMenuItem()
                    ->setTitle($domain)
                    ->setHref($uriBuilder->reset()->uriFor('settings', array('domain' => $domain), 'Backend'))
                    ->setActive($arguments['domain'] === $domain)
            );
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

        $defaultMenuItem = $menu->makeMenuItem()
            ->setTitle(LocalizationUtility::translate('tx_minshapeseo_label.default_language', 'mindshape_seo'))
            ->setHref($uriBuilder->reset()->uriFor('preview', array('sysLanguageUid' => 0), 'Backend'))
            ->setActive(!array_key_exists('sysLanguageUid', $arguments) || $arguments['sysLanguageUid'] === 0);

        $menu->addMenuItem($defaultMenuItem);

        if (array_key_exists('sysLanguageUid', $arguments)) {
            $sysLanguageUid = $arguments['sysLanguageUid'];
        } else {
            $sysLanguageUid = $this->sessionService->hasKey('sysLanguageUid') ?
                $this->sessionService->getKey('sysLanguageUid') :
                0;
        }

        foreach ($languages as $language) {
            $menu->addMenuItem(
                $menu->makeMenuItem()
                    ->setTitle($language['title'])
                    ->setHref($uriBuilder->reset()->uriFor('preview', array('sysLanguageUid' => $language['uid']), 'Backend'))
                    ->setActive($sysLanguageUid === $language['uid'])
            );
        }

        $this->view->getModuleTemplate()->getDocHeaderComponent()->getMenuRegistry()->addMenu($menu);
    }

    /**
     * @return void
     */
    protected function buildButtons()
    {
        /** @var \TYPO3\CMS\Core\Imaging\IconFactory $iconFactory */
        $iconFactory = GeneralUtility::makeInstance(IconFactory::class);

        $buttonBar = $this->view->getModuleTemplate()->getDocHeaderComponent()->getButtonBar();

        /** @var \TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder $uriBuilder */
        $uriBuilder = $this->objectManager->get(UriBuilder::class);
        $uriBuilder->setRequest($this->request);

        $saveButton = $buttonBar->makeLinkButton()
            ->setClasses('mindshape-seo-savebutton')
            ->setHref('#')
            ->setTitle(LocalizationUtility::translate('tx_minshapeseo_label.save', 'mindshape_seo'))
            ->setIcon($iconFactory->getIcon('actions-document-save', Icon::SIZE_SMALL));

        $buttonBar->addButton($saveButton, ButtonBar::BUTTON_POSITION_LEFT, 1);
    }

    /**
     * @param string $domain
     * @return void
     */
    public function settingsAction($domain = Configuration::DEFAULT_DOMAIN)
    {
        $domains = $this->domainService->getAvailableDomains();

        if (1 === count($domains)) {
            $domain = $domains[0];
        }

        $configuration = $this->configurationRepository->findByDomain($domain);

        if (null === $configuration) {
            $configuration = new Configuration();
            $configuration->setDomain($domain);
        }

        $this->view->assignMultiple(array(
            'domains' => $this->domainService->getAvailableDomains(),
            'currentDomain' => $domain,
            'configuration' => $configuration,
            'jsonldTypeOptions' => array(
                Configuration::JSONLD_TYPE_ORGANIZATION => LocalizationUtility::translate('tx_minshapeseo_configuration.jsonld.type.organization', 'mindshape_seo'),
                Configuration::JSONLD_TYPE_PERSON => LocalizationUtility::translate('tx_minshapeseo_configuration.jsonld.type.person', 'mindshape_seo'),
            ),
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
     * @return void
     */
    public function saveConfigurationAction(Configuration $configuration)
    {
        $this->configurationRepository->save($configuration);

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
                $this->view->assign(
                    'pageTree',
                    $this->pageService->getPageMetadataTree(
                        $this->currentPageUid,
                        $depth,
                        $sysLanguageUid,
                        $configuration->getTitleAttachment(),
                        $configuration->getJsonldCustomUrl(),
                        $configuration->getAddJsonld()
                    )
                );
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
                'depth' => $depth,
                'levelOptions' => array(
                    PageService::TREE_DEPTH_INFINITY => LocalizationUtility::translate('tx_minshapeseo_label.preview.levels.infinity', 'mindshape_seo'),
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
     * @param $argumentName
     * @return void
     */
    protected function setTypeConverterConfigurationForImageUpload($argumentName)
    {
        $uploadConfiguration = array(
            UploadedFileReferenceConverter::CONFIGURATION_ALLOWED_FILE_EXTENSIONS => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
            UploadedFileReferenceConverter::CONFIGURATION_UPLOAD_FOLDER => '1:/mindshape_seo/',
        );

        /** @var PropertyMappingConfiguration $newExampleConfiguration */
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
