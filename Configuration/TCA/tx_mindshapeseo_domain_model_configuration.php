<?php
return array(
    'ctrl' => array(
        'title' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration',
        'label' => 'domain',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,
        'versioningWS' => 2,
        'versioning_followPages' => true,
        'hideTable' => true,

        'delete' => 'deleted',
        'enablecolumns' => array(),
        'searchFields' => 'domain,google_analytics,piwik_url,piwik_idsite,title_attachment,add_hreflang,add_jsonld,add_jsonld_breadcrumb,facebook_default_image,jsonld_custom_url,jsonld_type,jsonld_telephone,jsonld_fax,jsonld_email,jsonld_same_as_facebook,jsonld_same_as_twitter,jsonld_same_as_googleplus,jsonld_same_as_instagram,jsonld_same_as_youtube,jsonld_same_as_linkedin,jsonld_same_as_xing,jsonld_same_as_printerest,jsonld_same_as_soundcloud,jsonld_same_as_tumblr,jsonld_logo,jsonld_address_locality,jsonld_address_postalcode,jsonld_address_street,',
        'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('mindshape_seo') . 'Resources/Public/Icons/tx_mindshapeseo_domain_model_configuration.gif',
    ),
    'interface' => array(
        'showRecordFieldList' => 'domain, google_analytics, piwik_url, piwik_idsite, title_attachment, title_attachment_seperator, title_attachment_position, add_hreflang, add_jsonld, add_jsonld_breadcrumb, facebook_default_image, image_sitemap_min_height, jsonld_custom_url, jsonld_type, jsonld_telephone, jsonld_fax, jsonld_email, jsonld_same_as_facebook, jsonld_same_as_twitter, jsonld_same_as_googleplus, jsonld_same_as_instagram, jsonld_same_as_youtube, jsonld_same_as_linkedin, jsonld_same_as_xing, jsonld_same_as_printerest, jsonld_same_as_soundcloud, jsonld_same_as_tumblr, jsonld_logo, jsonld_address_locality, jsonld_address_postalcode, jsonld_address_street',
    ),
    'types' => array(
        '1' => array('showitem' => 'domain, google_analytics, piwik_url, piwik_idsite, title_attachment, title_attachment_seperator, title_attachment_position, add_hreflang, add_jsonld, add_jsonld_breadcrumb, facebook_default_image, image_sitemap_min_width, jsonld_custom_url, jsonld_type, jsonld_telephone, jsonld_fax, jsonld_email, jsonld_same_as_facebook, jsonld_same_as_twitter, jsonld_same_as_googleplus, jsonld_same_as_instagram, jsonld_same_as_youtube, jsonld_same_as_linkedin, jsonld_same_as_xing, jsonld_same_as_printerest, jsonld_same_as_soundcloud, jsonld_same_as_tumblr, jsonld_logo, jsonld_address_locality, jsonld_address_postalcode, jsonld_address_street, '),
    ),
    'palettes' => array(
        '1' => array('showitem' => ''),
    ),
    'columns' => array(
        'domain' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.domain',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),
        'google_analytics' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.google_analytics_id',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),
        'piwik_url' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.piwik_url',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),
        'piwik_idsite' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.piwik_idsite',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),
        'title_attachment' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.title_attachment',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),
        'title_attachment_seperator' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.title_attachment_seperator',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),
        'title_attachment_position' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.title_attachment_position',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),
        'add_hreflang' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.add_hreflang',
            'config' => array(
                'type' => 'check',
                'default' => 0,
            ),
        ),
        'add_jsonld' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.add_jsonld',
            'config' => array(
                'type' => 'check',
                'default' => 0,
            ),
        ),
        'add_jsonld_breadcrumb' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.add_jsonld_breadcrumb',
            'config' => array(
                'type' => 'check',
                'default' => 0,
            ),
        ),
        'facebook_default_image' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.facebook_default_image',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                'facebook_default_image',
                array(
                    'appearance' => array(
                        'createNewRelationLinkTitle' => 'LLL:EXT:cms/locallang_ttc.xlf:images.addFileReference',
                    ),
                    'foreign_match_fields' => array(
                        'fieldname' => 'facebook_default_image',
                        'tablenames' => 'tx_mindshapeseo_domain_model_configuration',
                        'table_local' => 'sys_file',
                    ),
                    'foreign_types' => array(
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => array(
                            'showitem' => '
							--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
							--palette--;;filePalette',
                        ),
                    ),
                    'maxitems' => 1,
                ),
                $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
            ),
        ),
        'image_sitemap_min_height' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.image_sitemap_min_height',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),
        'image_sitemap_min_width' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.image_sitemap_min_width',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),
        'jsonld_custom_url' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.jsonld.custom_url',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),
        'jsonld_type' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.jsonld.type',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectSingle',
                'size' => 1,
                'maxitems' => 1,
                'minitems' => 1,
                'items' => array(
                    array('LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.jsonld.type.organization', \Mindshape\MindshapeSeo\Domain\Model\Configuration::JSONLD_TYPE_ORGANIZATION),
                    array('LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.jsonld.type.person', \Mindshape\MindshapeSeo\Domain\Model\Configuration::JSONLD_TYPE_PERSON),
                ),
            ),
        ),
        'jsonld_name' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.jsonld.name',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),
        'jsonld_telephone' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.jsonld.telephone',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),
        'jsonld_fax' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.jsonld.fax',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),
        'jsonld_email' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.jsonld.email',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),
        'jsonld_same_as_facebook' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.jsonld.same_as.facebook',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),
        'jsonld_same_as_twitter' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.jsonld.same_as.twitter',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),
        'jsonld_same_as_googleplus' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.jsonld.same_as.googleplus',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),
        'jsonld_same_as_instagram' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.jsonld.same_as.instagram',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),
        'jsonld_same_as_youtube' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.jsonld.same_as.youtube',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),
        'jsonld_same_as_linkedin' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.jsonld.same_as.linkedin',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),
        'jsonld_same_as_xing' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.jsonld.same_as.xing',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),
        'jsonld_same_as_printerest' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.jsonld.same_as.printerest',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),
        'jsonld_same_as_soundcloud' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.jsonld.same_as.soundcloud',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),
        'jsonld_same_as_tumblr' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.jsonld.same_as.tumblr',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),
        'jsonld_logo' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.jsonld.logo',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                'jsonld_logo',
                array(
                    'appearance' => array(
                        'createNewRelationLinkTitle' => 'LLL:EXT:cms/locallang_ttc.xlf:images.addFileReference',
                    ),
                    'foreign_match_fields' => array(
                        'fieldname' => 'jsonld_logo',
                        'tablenames' => 'tx_mindshapeseo_domain_model_configuration',
                        'table_local' => 'sys_file',
                    ),
                    'foreign_types' => array(
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => array(
                            'showitem' => '
							--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
							--palette--;;filePalette',
                        ),
                    ),
                    'maxitems' => 1,
                ),
                $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
            ),
        ),
        'jsonld_address_locality' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.jsonld.address.locality',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),
        'jsonld_address_postalcode' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.jsonld.address.postalcode',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),
        'jsonld_address_street' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.jsonld.address.street',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),

    ),
);
