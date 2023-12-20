<?php

use Mindshape\MindshapeSeo\Domain\Model\Configuration;
use TYPO3\CMS\Core\Resource\AbstractFile;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

return [
    'ctrl' => [
        'title' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration',
        'label' => 'domain',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,
        'versioningWS' => false,
        'hideTable' => true,
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'translationSource' => 'l10n_source',
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],
        'delete' => 'deleted',
        'enablecolumns' => [],
        'searchFields' => 'domain,google_analytics,title_attachment,add_analytics,add_jsonld,add_jsonld_breadcrumb,jsonld_custom_url,jsonld_type,jsonld_telephone,jsonld_fax,jsonld_email,jsonld_same_as_facebook,jsonld_same_as_twitter,jsonld_same_as_instagram,jsonld_same_as_youtube,jsonld_same_as_linkedin,jsonld_same_as_xing,jsonld_same_as_printerest,jsonld_same_as_soundcloud,jsonld_same_as_tumblr,jsonld_logo,jsonld_address_locality,jsonld_address_postalcode,jsonld_address_street,',
        'iconfile' => 'EXT:mindshape_seo/Resources/Public/Icons/tx_mindshapeseo_domain_model_configuration.gif',
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, domain, merge_with_default, google_analytics, google_tagmanager, matomo_url, matomo_idsite, title_attachment, title_attachment_seperator, title_attachment_position, add_analytics, google_analytics_use_cookie_consent, tagmanager_use_cookie_consent, matomo_use_cookie_consent, add_jsonld, add_jsonld_breadcrumb, jsonld_custom_url, jsonld_type, jsonld_telephone, jsonld_fax, jsonld_email, jsonld_same_as_facebook, jsonld_same_as_twitter, jsonld_same_as_instagram, jsonld_same_as_youtube, jsonld_same_as_linkedin, jsonld_same_as_xing, jsonld_same_as_printerest, jsonld_same_as_soundcloud, jsonld_same_as_tumblr, jsonld_logo, jsonld_address_locality, jsonld_address_postalcode, jsonld_address_street',
    ],
    'types' => [
        '1' => ['showitem' => 'sys_language_uid, domain, merge_with_default, google_analytics, google_tagmanager, matomo_url, matomo_idsite, title_attachment, title_attachment_seperator, title_attachment_position, add_analytics, google_analytics_use_cookie_consent, tagmanager_use_cookie_consent, matomo_use_cookie_consent, add_jsonld, add_jsonld_breadcrumb, jsonld_custom_url, jsonld_type, jsonld_telephone, jsonld_fax, jsonld_email, jsonld_same_as_facebook, jsonld_same_as_twitter, jsonld_same_as_instagram, jsonld_same_as_youtube, jsonld_same_as_linkedin, jsonld_same_as_xing, jsonld_same_as_printerest, jsonld_same_as_soundcloud, jsonld_same_as_tumblr, jsonld_logo, jsonld_address_locality, jsonld_address_postalcode, jsonld_address_street, '],
    ],
    'palettes' => [
        '1' => ['showitem' => ''],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages',
                'items' => [
                    [
                        'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.allLanguages',
                        -1,
                        'flags-multiple',
                    ],
                ],
                'default' => 0,
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'default' => 0,
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_mindshapeseo_domain_model_configuration',
                'foreign_table_where' => 'AND tx_mindshapeseo_domain_model_configuration.pid=###CURRENT_PID### AND tx_mindshapeseo_domain_model_configuration.sys_language_uid IN (-1,0)',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'domain' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.domain',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'merge_with_default' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.merge_with_default',
            'config' => [
                'type' => 'check',
                'default' => 1,
            ],
        ],
        'google_analytics' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.google_analytics_id',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'google_analytics_v4' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.google_analytics_id',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'google_tagmanager' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.google_tagmanager_id',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'title_attachment' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.title_attachment',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'title_attachment_seperator' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.title_attachment_seperator',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'title_attachment_position' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.title_attachment_position',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'add_analytics' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.add_analytics',
            'config' => [
                'type' => 'check',
                'default' => 0,
            ],
        ],
        'google_analytics_use_cookie_consent' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.google_analytics_use_cookie_consent',
            'config' => [
                'type' => 'check',
                'default' => 0,
            ],
        ],
        'google_analytics_v4_use_cookie_consent' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.google_analytics_use_cookie_consent',
            'config' => [
                'type' => 'check',
                'default' => 0,
            ],
        ],
        'tagmanager_use_cookie_consent' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.tagmanager_use_cookie_consent',
            'config' => [
                'type' => 'check',
                'default' => 0,
            ],
        ],
        'matomo_use_cookie_consent' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.matomo_use_cookie_consent',
            'config' => [
                'type' => 'check',
                'default' => 0,
            ],
        ],
        'add_jsonld' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.add_jsonld',
            'config' => [
                'type' => 'check',
                'default' => 0,
            ],
        ],
        'add_jsonld_breadcrumb' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.add_jsonld_breadcrumb',
            'config' => [
                'type' => 'check',
                'default' => 0,
            ],
        ],
        'jsonld_custom_url' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.jsonld.custom_url',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'jsonld_type' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.jsonld.type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'size' => 1,
                'maxitems' => 1,
                'minitems' => 1,
                'items' => [
                    [
                        'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.jsonld.type.organization',
                        Configuration::JSONLD_TYPE_ORGANIZATION,
                    ],
                    [
                        'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.jsonld.type.person',
                        Configuration::JSONLD_TYPE_PERSON,
                    ],
                ],
            ],
        ],
        'jsonld_name' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.jsonld.name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'jsonld_telephone' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.jsonld.telephone',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'jsonld_fax' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.jsonld.fax',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'jsonld_email' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.jsonld.email',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'jsonld_same_as_facebook' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.jsonld.same_as.facebook',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'jsonld_same_as_twitter' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.jsonld.same_as.twitter',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'jsonld_same_as_instagram' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.jsonld.same_as.instagram',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'jsonld_same_as_youtube' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.jsonld.same_as.youtube',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'jsonld_same_as_linkedin' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.jsonld.same_as.linkedin',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'jsonld_same_as_xing' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.jsonld.same_as.xing',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'jsonld_same_as_printerest' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.jsonld.same_as.printerest',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'jsonld_same_as_soundcloud' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.jsonld.same_as.soundcloud',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'jsonld_same_as_tumblr' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.jsonld.same_as.tumblr',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'jsonld_logo' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.jsonld.logo',
            'config' => ExtensionManagementUtility::getFileFieldTCAConfig(
                'jsonld_logo',
                [
                    'appearance' => [
                        'createNewRelationLinkTitle' => 'LLL:EXT:cms/locallang_ttc.xlf:images.addFileReference',
                    ],
                    'foreign_types' => [
                        AbstractFile::FILETYPE_IMAGE => [
                            'showitem' => '
							--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
							--palette--;;filePalette',
                        ],
                    ],
                    'maxitems' => 1,
                ],
                $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
            ),
        ],
        'jsonld_address_locality' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.jsonld.address.locality',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'jsonld_address_postalcode' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.jsonld.address.postalcode',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'jsonld_address_street' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.jsonld.address.street',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'matomo_url' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.matomo_url',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'matomo_idsite' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.matomo_idsite',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
    ],
];
