<?php

use Mindshape\MindshapeSeo\Controller\BackendController;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') or die();

call_user_func(function () {
    ExtensionManagementUtility::addLLrefForTCAdescr('tx_mindshapeseo_domain_model_configuration', 'EXT:mindshape_seo/Resources/Private/Language/locallang.xlf');

    if ((new Typo3Version())->getMajorVersion() < 12) {
        ExtensionUtility::registerModule(
            'MindshapeSeo',
            'mindshapeseo',
            '',
            'after:web',
            [],
            [
                'access' => 'user,group',
                'iconIdentifier' => 'module-mindshapeseo',
                'labels' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang_backend_seo_mainmodule.xlf',
            ]
        );

        ExtensionUtility::registerModule(
            'MindshapeSeo',
            'mindshapeseo',
            'preview',
            '',
            [BackendController::class => 'preview'],
            [
                'access' => 'user,group',
                'iconIdentifier' => 'module-mindshapeseo-preview',
                'labels' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang_backend_preview.xlf',
                'navigationComponentId' => 'TYPO3/CMS/Backend/PageTree/PageTreeElement',
            ]
        );

        ExtensionUtility::registerModule(
            'MindshapeSeo',
            'mindshapeseo',
            'settings',
            '',
            [BackendController::class => 'settings, saveConfiguration'],
            [
                'access' => 'user,group',
                'iconIdentifier' => 'module-mindshapeseo-settings',
                'labels' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang_backend_settings.xlf',
            ]
        );
    }

    /** @var \TYPO3\CMS\Core\Imaging\IconRegistry $iconRegistry */
    $iconRegistry = GeneralUtility::makeInstance(IconRegistry::class);

    $iconRegistry->registerIcon(
        'module-mindshapeseo-preview',
        SvgIconProvider::class,
        ['source' => 'EXT:mindshape_seo/Resources/Public/Icons/seo-preview.svg']
    );

    $iconRegistry->registerIcon(
        'module-mindshapeseo-settings',
        SvgIconProvider::class,
        ['source' => 'EXT:mindshape_seo/Resources/Public/Icons/seo-settings.svg']
    );

    $iconRegistry->registerIcon(
        'module-mindshapeseo',
        SvgIconProvider::class,
        ['source' => 'EXT:mindshape_seo/Resources/Public/Icons/FontAwesome/pie-chart.svg']
    );

    $iconRegistry->registerIcon(
        'provider-fontawesome-warning',
        SvgIconProvider::class,
        ['source' => 'EXT:mindshape_seo/Resources/Public/Icons/FontAwesome/exclamation-circle.svg']
    );

    $iconRegistry->registerIcon(
        'provider-fontawesome-error',
        SvgIconProvider::class,
        ['source' => 'EXT:mindshape_seo/Resources/Public/Icons/FontAwesome/exclamation-triangle.svg']
    );

    $iconRegistry->registerIcon(
        'provider-fontawesome-success',
        SvgIconProvider::class,
        ['source' => 'EXT:mindshape_seo/Resources/Public/Icons/FontAwesome/check-circle.svg']
    );

    $iconRegistry->registerIcon(
        'provider-fontawesome-caret-up',
        SvgIconProvider::class,
        ['source' => 'EXT:mindshape_seo/Resources/Public/Icons/FontAwesome/caret-up.svg']
    );

    $iconRegistry->registerIcon(
        'provider-fontawesome-caret-down',
        SvgIconProvider::class,
        ['source' => 'EXT:mindshape_seo/Resources/Public/Icons/FontAwesome/caret-down.svg']
    );

    $iconRegistry->registerIcon(
        'provider-fontawesome-angle-up',
        SvgIconProvider::class,
        ['source' => 'EXT:mindshape_seo/Resources/Public/Icons/FontAwesome/angle-up.svg']
    );

    $iconRegistry->registerIcon(
        'provider-fontawesome-angle-down',
        SvgIconProvider::class,
        ['source' => 'EXT:mindshape_seo/Resources/Public/Icons/FontAwesome/angle-down.svg']
    );
});
