<?php
defined('TYPO3_MODE') || die();

call_user_func(function () {
    if (
        true === \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('redirects') &&
        true === (bool)\Mindshape\MindshapeSeo\Utility\SettingsUtility::extensionConfigurationValue('enableGoneRedirects')
    ) {
        $GLOBALS['TCA']['sys_redirect']['columns']['target_statuscode']['config']['items'][] = ['LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:sys_redirect.target_statuscode.410', 410];
    }
});
