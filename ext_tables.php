<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_mindshapeseo_domain_model_configuration', 'EXT:mindshape_seo/Resources/Private/Language/locallang.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_mindshapeseo_domain_model_configuration');

if (TYPO3_MODE === 'BE') {
    $mainModuleKey = 'mindshapeseo';

    $GLOBALS['TBE_MODULES'][$mainModuleKey] = '';

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'Mindshape.' . $_EXTKEY,
        $mainModuleKey,
        'preview',
        '',
        array(
            'Backend' => 'preview',
        ),
        array(
            'access' => 'user,group',
            'icon' => 'EXT:' . $_EXTKEY . '/Resources/Public/Icons/seo-preview.svg',
            'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_backend_preview.xlf',
            'navigationComponentId' => 'typo3-pagetree',
        )
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'Mindshape.' . $_EXTKEY,
        $mainModuleKey,
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

    $tempModules = array();

    foreach ($GLOBALS['TBE_MODULES'] as $key => $mainModule) {
        switch ($key) {
            case 'web';
                $tempModules['web'] = $mainModule;
                $tempModules[$mainModuleKey] = $GLOBALS['TBE_MODULES'][$mainModuleKey];
                break;
            case $mainModuleKey:
                break;
            case '_configuration':
                $tempModules['_configuration'] = $mainModule;
                $tempModules['_configuration'][$mainModuleKey] = array(
                    'labels' => array(
                        'll_ref' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang_backend_seo_mainmodule.xlf',
                    ),
                    'name' => $mainModuleKey,
                    'iconIdentifier' => 'module-' . $mainModuleKey,
                );
                break;
            default:
                $tempModules[$key] = $mainModule;
        };
    }

    $GLOBALS['TBE_MODULES'] = $tempModules;
}

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
