<?php

use Mindshape\MindshapeSeo\Http\Middleware\RedirectHandler;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/** @var \TYPO3\CMS\Core\Configuration\ExtensionConfiguration $extensionConfiguration */
$extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);

try {
    $isEnableGoneRedirects = $extensionConfiguration->get('mindshape_seo', 'enableGoneRedirects');
} catch (ExtensionConfigurationExtensionNotConfiguredException|ExtensionConfigurationPathDoesNotExistException) {
    $isEnableGoneRedirects = false;
}

if ($isEnableGoneRedirects && ExtensionManagementUtility::isLoaded('redirects')) {
    return [
        'frontend' => [
            'mindshape/mindshape-seo/redirecthandler' => [
                'target' => RedirectHandler::class,
                'before' => [
                    'typo3/cms-redirects/redirecthandler',
                ],
                'after' => [
                    'typo3/cms-frontend/authentication',
                ],
            ],
        ],
    ];
}

return [];
