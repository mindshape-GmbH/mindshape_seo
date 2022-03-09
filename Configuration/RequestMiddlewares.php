<?php

if (
    false === \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('redirects') ||
    false === (bool) \Mindshape\MindshapeSeo\Utility\SettingsUtility::extensionConfigurationValue('enableGoneRedirects')
) {
    return [
        'frontend' => [
            'mindshape/mindshape-seo/inject-analytics-tags' => [
                'target' => \Mindshape\MindshapeSeo\Http\Middleware\InjectAnalyticsTagsMiddleware::class,
                'before' => [
                    'typo3/cms-frontend/shortcut-and-mountpoint-redirect',
                ],
                'after' => [
                    'typo3/cms-frontend/prepare-tsfe-rendering',
                ],
            ],
        ],
    ];
}

return [
    'frontend' => [
        'mindshape/mindshape-seo/redirecthandler' => [
            'target' => \Mindshape\MindshapeSeo\Http\Middleware\RedirectHandler::class,
            'before' => [
                'typo3/cms-redirects/redirecthandler',
            ],
            'after' => [
                'typo3/cms-frontend/authentication',
            ],
        ],
        'mindshape/mindshape-seo/inject-analytics-tags' => [
            'target' => \Mindshape\MindshapeSeo\Http\Middleware\InjectAnalyticsTagsMiddleware::class,
            'before' => [
                'typo3/cms-frontend/shortcut-and-mountpoint-redirect',
            ],
            'after' => [
                'typo3/cms-frontend/prepare-tsfe-rendering',
            ],
        ],
    ],
];
