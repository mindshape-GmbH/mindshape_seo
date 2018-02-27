<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Classes/Vendor/autoload.php');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants(
    '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:mindshape_seo/Configuration/TypoScript/constants.txt">'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup(
    '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:mindshape_seo/Configuration/TypoScript/setup.txt">'
);

if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('news')) {
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mindshape_seo']['sitemap_preRendering'][] = \Mindshape\MindshapeSeo\Hook\NewsSitemapHook::class . '->sitemapPreRendering';
}

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_content.php']['typoLink_PostProc']['realurl'] = \Mindshape\MindshapeSeo\Hook\TypoLinkHook::class . '->postProcessEncodedUrl';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-preProcess'][] = \Mindshape\MindshapeSeo\Hook\RenderPreProcessHook::class . '->main';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/realurl/class.tx_realurl_autoconfgen.php']['extensionConfiguration'][] = \Mindshape\MindshapeSeo\Hook\RealurlHook::class . '->addConfiguration';
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerTypeConverter(\Mindshape\MindshapeSeo\Property\TypeConverter\UploadedFileReferenceConverter::class);

if (
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('realurl') &&
    true === array_key_exists('realurl', $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'])
) {
    \Mindshape\MindshapeSeo\Hook\RealurlHook::checkConfiguration();
}

$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController::class] = [
    'className' => \Mindshape\MindshapeSeo\XClass\TypoScriptFrontendController::class
];

call_user_func(function() {
    // call very first TYPO3 hook for redirecting requests
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/index_ts.php']['preprocessRequest'][] = function($parameters, $parent) {
        $preProcess = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\Mindshape\MindshapeSeo\Hook\PreProcess::class);
        $preProcess->redirect($parameters, $parent);
    };
});