<?php

use Mindshape\MindshapeSeo\Http\Middleware\InjectAnalyticsTagsMiddleware;
use Mindshape\MindshapeSeo\Http\Middleware\RedirectHandler;
use Mindshape\MindshapeSeo\Utility\SettingsUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

if (
    ExtensionManagementUtility::isLoaded('redirects') &&
    SettingsUtility::extensionConfigurationValue('enableGoneRedirects')
) {
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
            'mindshape/mindshape-seo/inject-analytics-tags' => [
                'target' => InjectAnalyticsTagsMiddleware::class,
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
        'mindshape/mindshape-seo/inject-analytics-tags' => [
            'target' => InjectAnalyticsTagsMiddleware::class,
            'before' => [
                'typo3/cms-frontend/shortcut-and-mountpoint-redirect',
            ],
            'after' => [
                'typo3/cms-frontend/prepare-tsfe-rendering',
            ],
        ],
    ],
];
