<?php
namespace Mindshape\MindshapeSeo\Service;

/***************************************************************
 *
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

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * @package mindshape_seo
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class StandaloneTemplateRendererService implements SingletonInterface
{
    const TEMPLATES_DEFAULT_FOLDER = 'TemplateRenderer';

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     */
    protected $configurationManager;

    /**
     * @var array
     */
    protected $settings;

    /**
     * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManager $configurationManager
     * @return void
     */
    public function injectConfigurationManager(ConfigurationManager $configurationManager)
    {
        $this->configurationManager = $configurationManager;
    }

    /**
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     * @throws \TYPO3\CMS\Extbase\Object\Exception
     */
    public function __construct()
    {
        /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        /** @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManager $configurationManager */
        $configurationManager = $objectManager->get(ConfigurationManager::class);

        $config = GeneralUtility::removeDotsFromTS(
            $configurationManager->getConfiguration(ConfigurationManager::CONFIGURATION_TYPE_FULL_TYPOSCRIPT)
        );


        $this->settings = $config['plugin']['tx_mindshapeseo'];
    }

    /**
     * @param string $templateFolder
     * @param string $templateName
     * @param array $variables
     * @param string $format
     * @return string
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\InvalidExtensionNameException
     */
    public function render($templateFolder = self::TEMPLATES_DEFAULT_FOLDER, $templateName, array $variables, $format = 'html')
    {
        if ('/' !== $templateFolder[-1]) {
            $templateFolder .= '/';
        }

        $view = $this->getView($templateFolder, $templateName, $format);

        if (0 < count($variables)) {
            $view->assignMultiple($variables);
        }

        return $view->render();
    }

    /**
     * @param string $templateFolder
     * @param string $templateName
     * @param string $format
     * @return \TYPO3\CMS\Fluid\View\StandaloneView
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\InvalidExtensionNameException
     */
    protected function getView($templateFolder = self::TEMPLATES_DEFAULT_FOLDER, $templateName, $format = 'html')
    {
        /** @var \TYPO3\CMS\Fluid\View\StandaloneView $view */
        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setFormat($format);
        $view->getRequest()->setControllerExtensionName('MindshapeSeo');
        $view->setTemplateRootPaths($this->settings['view']['templateRootPaths']);
        $view->setLayoutRootPaths($this->settings['view']['layoutRootPaths']);
        $view->setPartialRootPaths($this->settings['view']['partialRootPaths']);
        $view->setTemplate($templateFolder . $templateName . '.' . $format);

        return $view;
    }
}
