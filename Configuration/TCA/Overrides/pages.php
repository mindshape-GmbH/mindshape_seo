<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$GLOBALS['TCA']['pages']['columns']['description']['label'] = 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_label.page_description';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
    'pages',
    [
        'lastUpdated' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'keywords' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'mindshapeseo_focus_keyword' => [
            'exclude' => 0,
            'config' => [
                'type' => 'user',
                'renderType' => 'googlePreview',
                'size' => 30,
            ],
        ],
        'mindshapeseo_disable_title_attachment' => [
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_pages.mindshapeseo_disable_title_attachment',
            'exclude' => 1,
            'config' => [
                'type' => 'check',
            ],
        ],
        'mindshapeseo_jsonld_breadcrumb_title' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_pages.mindshapeseo_jsonld_breadcrumb_title',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'eval' => 'trim',
                'max' => 255,
            ],
        ],
        'mindshapeseo_no_index_recursive' => [
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_pages.mindshapeseo_no_index_recursive',
            'exclude' => 1,
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
            ],
        ],
        'mindshapeseo_no_follow_recursive' => [
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_pages.mindshapeseo_no_follow_recursive',
            'exclude' => 1,
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
            ],
        ],
    ]
);

$GLOBALS['TCA']['pages']['palettes']['mindshape_seo_jsonld_pallette'] = [
    'showitem' => 'mindshapeseo_jsonld_breadcrumb_title',
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'pages',
    'seo',
    '--linebreak--, mindshapeseo_focus_keyword,--linebreak--,mindshapeseo_disable_title_attachment'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'pages',
    'robots',
    '--linebreak--, mindshapeseo_no_index_recursive, mindshapeseo_no_follow_recursive'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'pages',
    '--palette--;LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_label.jsonld;mindshape_seo_jsonld_pallette',
    '1,4',
    'after:mindshapeseo_disable_title_attachment'
);
