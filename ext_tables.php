<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_mindshapeseo_domain_model_configuration', 'EXT:mindshape_seo/Resources/Private/Language/locallang.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_mindshapeseo_domain_model_configuration');

if (TYPO3_MODE === 'BE') {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'Mindshape.' . $_EXTKEY,
        'web',
        'preview',
        '',
        array(
            'Backend' => 'preview',
        ),
        array(
            'access' => 'user,group',
            'icon' => 'EXT:' . $_EXTKEY . '/Resources/Public/Icons/seo-preview.svg',
            'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_backend_preview.xlf',
        )
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'Mindshape.' . $_EXTKEY,
        'tools',
        'settings',
        '',
        array(
            'Backend' => 'settings, saveConfiguration',
        ),
        array(
            'access' => 'user,group',
            'icon' => 'EXT:' . $_EXTKEY . '/Resources/Public/Icons/seo-settings.svg',
            'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_backend_settings.xlf',
        )
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerAjaxHandler(
        'MindshapeSeoAjaxHandler::savePage',
        Mindshape\MindshapeSeo\Handler\AjaxHandler::class . '->savePage'
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerAjaxHandler(
        'MindshapeSeoAjaxHandler::savePageRobots',
        Mindshape\MindshapeSeo\Handler\AjaxHandler::class . '->savePageRobots'
    );
}

/** @var \TYPO3\CMS\Core\Imaging\IconRegistry $iconRegistry */
$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);

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
