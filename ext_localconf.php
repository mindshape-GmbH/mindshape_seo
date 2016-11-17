<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants(
    '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:mindshape_seo/Configuration/TypoScript/constants.txt">'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup(
    '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:mindshape_seo/Configuration/TypoScript/setup.txt">'
);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-preProcess'][] = \Mindshape\MindshapeSeo\Hook\RenderPreProcessHook::class . '->main';
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerTypeConverter(\Mindshape\MindshapeSeo\Property\TypeConverter\UploadedFileReferenceConverter::class);

if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('realurl')) {
    if (!array_key_exists('realurl', $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'])) {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['realurl'] = array();
    }

    if (!array_key_exists('_DEFAULT', $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['realurl'])) {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['realurl']['_DEFAULT'] = array();
    }

    if (!array_key_exists('fileName', $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['realurl']['_DEFAULT'])) {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['realurl']['_DEFAULT']['fileName'] = array();
    }

    if (!array_key_exists('index', $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['realurl']['_DEFAULT']['fileName'])) {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['realurl']['_DEFAULT']['fileName']['index'] = array();
    }

    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['realurl']['_DEFAULT']['fileName']['index'] = array_merge(
        array(
            'sitemap.xml' => array(
                'keyValues' => array(
                    'type' => 19371,
                ),
            ),
            'sitemap-index.xml' => array(
                'keyValues' => array(
                    'type' => 19372,
                ),
            ),
            'sitemap-image.xml' => array(
                'keyValues' => array(
                    'type' => 19373,
                ),
            ),
        ),
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['realurl']['_DEFAULT']['fileName']['index']
    );
}
