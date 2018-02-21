<?php
return array(
    'ctrl' => array(
        'title' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_redirect',
        'label' => 'domain',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,
        'versioningWS' => 2,
        'versioning_followPages' => true,
        'hideTable' => true,
        'hidden' => 'hidden',
        'delete' => 'deleted',
        'edited' => 'edited',
        'regexp' => 'regexp',
        'hits' => 'hits',
        'last_hit_on' => 'last_hit_on',
        'enablecolumns' => array(),
        'searchFields' => 'source_domain,',
        'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('mindshape_seo') . 'Resources/Public/Icons/tx_mindshapeseo_domain_model_redirect.gif',
    ),
    'interface' => array(
        'showRecordFieldList' => 'source_domain, ',
    ),
    'types' => array(
        '1' => array('showitem' => 'source_domain, '),
    ),
    'palettes' => array(
        '1' => array('showitem' => ''),
    ),
    'columns' => array(
        'source_domain' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_redirect.source_domain',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),

        'source_path'=> array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_redirect.source_path',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),

        'target'=> array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_redirect.target',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),

        'http_statuscode'=> array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_redirect.http_statuscode',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'items' => [
                    '1' => [
                        '0' => 'LLL:EXT:lang/locallang_core.xlf:labels.enabled'
                    ]
                ],
            ],
        ],
        'edited' => [
            'exclude' => true,
            'label' => 'Edited',
            'config' => [
                'type' => 'input',
                'size' => 10,
                'eval' => 'integer'
            ],
        ],
        'last_hit_on' => [
            'exclude' => true,
            'label' => 'Last Hit on',
            'config' => [
                'type' => 'input',
                'size' => 10,
                'eval' => 'integer'
            ],
        ],

        'hits' => [
            'exclude' => true,
            'label' => 'Hits',
            'config' => [
                'type' => 'input',
                'size' => 10,
                'eval' => 'integer'
            ],
        ],
        'regexp' => [
            'exclude' => true,
            'label' => 'Regexp',
            'config' => [
                'type' => 'check',
                'items' => [
                    '1' => [
                        '0' => 'LLL:EXT:lang/locallang_core.xlf:labels.enabled'
                    ]
                ],
                'default' => 0
            ]
        ],

    ),
);
