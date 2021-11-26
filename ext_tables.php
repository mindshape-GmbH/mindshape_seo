<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(function () {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_mindshapeseo_domain_model_configuration', 'EXT:mindshape_seo/Resources/Private/Language/locallang.xlf');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_mindshapeseo_domain_model_configuration');

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
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

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'MindshapeSeo',
        'mindshapeseo',
        'preview',
        '',
        [\Mindshape\MindshapeSeo\Controller\BackendController::class => 'preview'],
        [
            'access' => 'user,group',
            'icon' => 'EXT:mindshape_seo/Resources/Public/Icons/seo-preview.svg',
            'labels' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang_backend_preview.xlf',
            'navigationComponentId' => 'TYPO3/CMS/Backend/PageTree/PageTreeElement',
        ]
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'MindshapeSeo',
        'mindshapeseo',
        'settings',
        '',
        [\Mindshape\MindshapeSeo\Controller\BackendController::class => 'settings, saveConfiguration'],
        [
            'access' => 'user,group',
            'icon' => 'EXT:mindshape_seo/Resources/Public/Icons/seo-settings.svg',
            'labels' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang_backend_settings.xlf',
        ]
    );

    /** @var \TYPO3\CMS\Core\Imaging\IconRegistry $iconRegistry */
    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);

    $iconRegistry->registerIcon(
        'module-mindshapeseo',
        \TYPO3\CMS\Core\Imaging\IconProvider\FontawesomeIconProvider::class,
        ['name' => 'pie-chart']
    );

    $iconRegistry->registerIcon(
        'provider-fontawesome-info',
        \TYPO3\CMS\Core\Imaging\IconProvider\FontawesomeIconProvider::class,
        ['name' => 'info-circle']
    );

    $iconRegistry->registerIcon(
        'provider-fontawesome-warning',
        \TYPO3\CMS\Core\Imaging\IconProvider\FontawesomeIconProvider::class,
        ['name' => 'exclamation-circle']
    );

    $iconRegistry->registerIcon(
        'provider-fontawesome-error',
        \TYPO3\CMS\Core\Imaging\IconProvider\FontawesomeIconProvider::class,
        ['name' => 'exclamation-triangle']
    );

    $iconRegistry->registerIcon(
        'provider-fontawesome-success',
        \TYPO3\CMS\Core\Imaging\IconProvider\FontawesomeIconProvider::class,
        ['name' => 'check-circle']
    );

    $iconRegistry->registerIcon(
        'provider-fontawesome-caret-up',
        \TYPO3\CMS\Core\Imaging\IconProvider\FontawesomeIconProvider::class,
        ['name' => 'caret-up']
    );

    $iconRegistry->registerIcon(
        'provider-fontawesome-caret-down',
        \TYPO3\CMS\Core\Imaging\IconProvider\FontawesomeIconProvider::class,
        ['name' => 'caret-down']
    );

    $iconRegistry->registerIcon(
        'provider-fontawesome-angle-up',
        \TYPO3\CMS\Core\Imaging\IconProvider\FontawesomeIconProvider::class,
        ['name' => 'angle-up']
    );

    $iconRegistry->registerIcon(
        'provider-fontawesome-angle-down',
        \TYPO3\CMS\Core\Imaging\IconProvider\FontawesomeIconProvider::class,
        ['name' => 'angle-down']
    );
});
