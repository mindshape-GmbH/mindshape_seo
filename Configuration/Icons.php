<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

return [
    'module-mindshapeseo-preview' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:mindshape_seo/Resources/Public/Icons/seo-preview.svg',
    ],
    'module-mindshapeseo-settings' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:mindshape_seo/Resources/Public/Icons/seo-settings.svg',
    ],
    'module-mindshapeseo' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:mindshape_seo/Resources/Public/Icons/FontAwesome/pie-chart.svg',
    ],
    'provider-fontawesome-warning' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:mindshape_seo/Resources/Public/Icons/FontAwesome/exclamation-circle.svg',
    ],
    'provider-fontawesome-error' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:mindshape_seo/Resources/Public/Icons/FontAwesome/exclamation-triangle.svg',
    ],
    'provider-fontawesome-success' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:mindshape_seo/Resources/Public/Icons/FontAwesome/check-circle.svg',
    ],
    'provider-fontawesome-caret-up' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:mindshape_seo/Resources/Public/Icons/FontAwesome/caret-up.svg',
    ],
    'provider-fontawesome-caret-down' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:mindshape_seo/Resources/Public/Icons/FontAwesome/caret-down.svg',
    ],
    'provider-fontawesome-angle-up' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:mindshape_seo/Resources/Public/Icons/FontAwesome/angle-up.svg',
    ],
    'provider-fontawesome-angle-down' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:mindshape_seo/Resources/Public/Icons/FontAwesome/angle-down.svg',
    ],
];
