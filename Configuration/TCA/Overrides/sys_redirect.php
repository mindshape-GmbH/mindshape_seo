<?php

use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

defined('TYPO3') or die();

call_user_func(function () {
    /** @var \TYPO3\CMS\Core\Configuration\ExtensionConfiguration $extensionConfiguration */
    $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);

    try {
        $isEnableGoneRedirects = $extensionConfiguration->get('mindshape_seo', 'enableGoneRedirects');
    } catch (ExtensionConfigurationExtensionNotConfiguredException|ExtensionConfigurationPathDoesNotExistException) {
        $isEnableGoneRedirects = false;
    }

    if ($isEnableGoneRedirects && ExtensionManagementUtility::isLoaded('redirects')) {
        $GLOBALS['TCA']['sys_redirect']['columns']['target_statuscode']['config']['items'][] = [
            'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:sys_redirect.target_statuscode.410',
            410,
        ];
        $GLOBALS['TCA']['sys_redirect']['columns']['target_statuscode']['onChange'] = 'reload';
        $GLOBALS['TCA']['sys_redirect']['columns']['target']['displayCond'] = 'FIELD:target_statuscode:!=:410';
    }
});
