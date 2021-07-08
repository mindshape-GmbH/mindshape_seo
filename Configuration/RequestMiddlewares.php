<?php

if (
    false === \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('redirects') ||
    false === (bool)\Mindshape\MindshapeSeo\Utility\SettingsUtility::extensionConfigurationValue('enableGoneRedirects')
) {
    return [];
}

if (true === version_compare('10.4', \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Information\Typo3Version::class)->getVersion(), '<=')) {
    $rearrangedMiddlewares = TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        TYPO3\CMS\Core\Configuration\Features::class
    )->isFeatureEnabled('rearrangedRedirectMiddlewares');

    return [
        'frontend' => [
            'mindshape/mindshape-customer/redirecthandler' => [
                'target' => \Mindshape\MindshapeSeo\Http\Middleware\RedirectHandler::class,
                'before' => [
                    'typo3/cms-redirects/redirecthandler',
                ],
                'after' => [
                    $rearrangedMiddlewares ? 'typo3/cms-frontend/authentication' : 'typo3/cms-frontend/static-route-resolver',
                ],
            ],
        ],
    ];
} else {
    return [
        'frontend' => [
            'mindshape/mindshape-customer/redirecthandler' => [
                'target' => \Mindshape\MindshapeSeo\Http\Middleware\RedirectHandler::class,
                'before' => [
                    'typo3/cms-redirects/redirecthandler',
                ],
                'after' => [
                    'typo3/cms-frontend/tsfe',
                    'typo3/cms-frontend/authentication',
                    'typo3/cms-frontend/static-route-resolver',
                ],
            ],
        ],
    ];
}
