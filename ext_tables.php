<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

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
            'icon' => 'EXT:' . $_EXTKEY . '/ext_icon.png',
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
            'icon' => 'EXT:' . $_EXTKEY . '/ext_icon.png',
            'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_backend_settings.xlf',
        )
    );

}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'mindshape SEO');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_mindshapeseo_domain_model_configuration', 'EXT:mindshape_seo/Resources/Private/Language/locallang.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_mindshapeseo_domain_model_configuration');
