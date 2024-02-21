<?php

use Mindshape\MindshapeSeo\Controller\BackendController;

return [
    'mindshapeseo' => [
        'labels' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang_backend_seo_mainmodule.xlf',
        'iconIdentifier' => 'module-mindshapeseo',
        'position' => ['after' => 'web'],
    ],
    'mindshapeseo_preview' => [
        'parent' => 'mindshapeseo',
        'access' => 'user',
        'iconIdentifier' => 'module-mindshapeseo-preview',
        'labels' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang_backend_preview.xlf',
        'navigationComponent' => '@typo3/backend/page-tree/page-tree-element',
        'extensionName' => 'MindshapeSeo',
        'controllerActions' => [
            BackendController::class => ['preview'],
        ],
    ],
    'mindshapeseo_settings' => [
        'parent' => 'mindshapeseo',
        'access' => 'user',
        'iconIdentifier' => 'module-mindshapeseo-settings',
        'labels' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang_backend_settings.xlf',
        'extensionName' => 'MindshapeSeo',
        'controllerActions' => [
            BackendController::class => ['settings', 'saveConfiguration'],
        ],
    ],
];
