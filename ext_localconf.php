<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(function () {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants(
        '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:mindshape_seo/Configuration/TypoScript/constants.typoscript">'
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup(
        '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:mindshape_seo/Configuration/TypoScript/setup.typoscript">'
    );

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-all'][] =  \Mindshape\MindshapeSeo\Hook\InjectSeoDataHook::class . '->all';
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-cached'][] =  \Mindshape\MindshapeSeo\Hook\InjectSeoDataHook::class . '->cached';
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-output'][] =  \Mindshape\MindshapeSeo\Hook\InjectSeoDataHook::class . '->output';

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-all'][] = \Mindshape\MindshapeSeo\Hook\ContentPostProcAllHook::class . '->main';

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerTypeConverter(\Mindshape\MindshapeSeo\Property\TypeConverter\UploadedFileReferenceConverter::class);

    // Register a node in ext_localconf.php
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1594739604] = [
        'nodeName' => 'googlePreview',
        'priority' => 40,
        'class' => \Mindshape\MindshapeSeo\Backend\Form\Element\GooglePreviewElement::class,
    ];

});
