<?php

if (
    false === \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('redirects') ||
    false === (bool) \Mindshape\MindshapeSeo\Utility\SettingsUtility::extensionConfigurationValue('enableGoneRedirects')
) {
    return [];
}

return [
    'frontend' => [
        'mindshape/mindshape-customer/redirecthandler' => [
            'target' => \Mindshape\MindshapeSeo\Http\Middleware\RedirectHandler::class,
            'before' => [
                'typo3/cms-redirects/redirecthandler',
            ],
            'after' => [
               'typo3/cms-frontend/authentication',
            ],
        ],
    ],
];
