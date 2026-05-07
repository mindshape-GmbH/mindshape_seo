<?php

namespace Mindshape\MindshapeSeo\Service;

/***************************************************************
 *
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

use Mindshape\MindshapeSeo\Utility\SettingsUtility;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\View\TemplateView;
use TYPO3Fluid\Fluid\View\ViewInterface;

class StandaloneTemplateRendererService implements SingletonInterface
{
    protected array $viewSettings;
    protected array $settings;

    public function __construct()
    {
        $settingsUtility = new SettingsUtility();
        $extensionTypoScript = $settingsUtility->getExtensionTypoScript();

        $this->viewSettings = $extensionTypoScript['view'] ?? [];
        $this->settings = $extensionTypoScript['settings'] ?? [];
    }

    public function render(
        string $templateFolder,
        string $templateName,
        array $variables,
        string $format = 'html',
        ?RenderingContextInterface $renderingContext = null
    ): string {
        $view = $this->getView($templateFolder, $templateName, $format);

        if (false === array_key_exists('settings', $variables)) {
            $variables['settings'] = $this->settings;
        }

        $view->assignMultiple($variables);

        return $view->render();
    }

    protected function getView(
        string $templateFolder,
        string $templateName,
        string $format = 'html',
        ?RenderingContextInterface $renderingContext = null
    ): ViewInterface {
        /** @var \TYPO3Fluid\Fluid\View\TemplateView $view */
        $view = GeneralUtility::makeInstance(TemplateView::class, $renderingContext);
        $templatePaths = $view->getRenderingContext()->getTemplatePaths();

        // We need the absolute path here for TYPO3Fluid\Fluid\View\TemplatePaths, this works different from TYPO3\CMS\Fluid\View\TemplatePaths
        $templatePaths->setLayoutRootPaths(
            array_map(
                GeneralUtility::class . '::getFileAbsFileName',
                $this->viewSettings['layoutRootPaths'] ?? [0 => 'EXT:mindshape_seo/Resources/Private/Templates/']
            )
        );
        $templatePaths->setTemplateRootPaths(
            array_map(
                GeneralUtility::class . '::getFileAbsFileName',
                $this->viewSettings['templateRootPaths'] ?? [0 => 'EXT:mindshape_seo/Resources/Private/Layouts/']
            )
        );
        $templatePaths->setPartialRootPaths(
            array_map(
                GeneralUtility::class . '::getFileAbsFileName',
                $this->viewSettings['partialRootPaths'] ?? [0 => 'EXT:mindshape_seo/Resources/Private/Partials/']
            )
        );

        $templatePaths->setTemplatePathAndFilename(
            $templatePaths->resolveTemplateFileForControllerAndActionAndFormat(
                $templateFolder,
                $templateName
            )
        );

        return $view;
    }
}
