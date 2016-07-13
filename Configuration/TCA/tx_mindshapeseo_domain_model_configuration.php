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

        'delete' => 'deleted',
        'enablecolumns' => array(),
        'searchFields' => 'domain,google_analytics,piwik_url,piwik_idsite,title_attachment,generate_sitemap,add_hreflang,add_jsonld,facebook_default_image,jsonld_custom_url,jsonld_type,jsonld_telephone,jsonld_fax,jsonld_email,jsonld_same_as,jsonld_logo,jsonld_address_locality,jsonld_address_postalcode,jsonld_address_street,',
        'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('mindshape_seo') . 'Resources/Public/Icons/tx_mindshapeseo_domain_model_configuration.gif',
    ),
    'interface' => array(
        'showRecordFieldList' => 'domain, google_analytics, piwik_url, piwik_idsite, title_attachment, generate_sitemap, add_hreflang, add_jsonld, facebook_default_image, jsonld_custom_url, jsonld_type, jsonld_telephone, jsonld_fax, jsonld_email, jsonld_same_as, jsonld_logo, jsonld_address_locality, jsonld_address_postalcode, jsonld_address_street',
    ),
    'types' => array(
        '1' => array('showitem' => 'domain, google_analytics, piwik_url, piwik_idsite, title_attachment, generate_sitemap, add_hreflang, add_jsonld, facebook_default_image, jsonld_custom_url, jsonld_type, jsonld_telephone, jsonld_fax, jsonld_email, jsonld_same_as, jsonld_logo, jsonld_address_locality, jsonld_address_postalcode, jsonld_address_street, '),
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
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.google_analytics',
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
        'generate_sitemap' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.generate_sitemap',
            'config' => array(
                'type' => 'check',
                'default' => 0,
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
        'facebook_default_image' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.facebook_default_image',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                'facebook_default_image',
                array(
                    'appearance' => array(
                        'createNewRelationLinkTitle' => 'LLL:EXT:cms/locallang_ttc.xlf:images.addFileReference',
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
        'jsonld_same_as' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_configuration.jsonld.same_as',
            'config' => array(
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
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
