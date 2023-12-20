<?php

use Mindshape\MindshapeSeo\Utility\SettingsUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') or die();

call_user_func(function () {
    if (
        true === ExtensionManagementUtility::isLoaded('redirects') &&
        true === (bool) SettingsUtility::extensionConfigurationValue('enableGoneRedirects')
    ) {
        $GLOBALS['TCA']['sys_redirect']['columns']['target_statuscode']['config']['items'][] = [
            'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:sys_redirect.target_statuscode.410',
            410,
        ];
    }
});
