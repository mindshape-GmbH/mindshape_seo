<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$columns = array(
    'lastUpdated' => array(
        'config' => array(
            'type' => 'passthrough',
        ),
    ),
    'keywords' => array(
        'config' => array(
            'type' => 'passthrough',
        ),
    ),
    'mindshapeseo_focus_keyword' => array(
        'exclude' => 0,
        'config' => array(
            'type' => 'user',
            'size' => 30,
            'userFunc' => \Mindshape\MindshapeSeo\Userfuncs\Tca\GooglePreviewField::class . '->render',
        ),
    ),
    'mindshapeseo_ogtitle' => array(
        'exclude' => 0,
        'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_pages.mindshapeseo_ogtitle',
        'config' => array(
            'type' => 'input',
            'size' => 30,
            'eval' => 'trim',
        ),
    ),
    'mindshapeseo_ogurl' => array(
        'exclude' => 1,
        'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_pages.mindshapeseo_ogurl',
        'config' => array(
            'type' => 'input',
            'size' => 30,
            'eval' => 'trim',
        ),
    ),
    'mindshapeseo_ogimage' => array(
        'exclude' => 0,
        'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_pages.mindshapeseo_ogimage',
        'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
            'ogimage',
            array(
                'appearance' => array(
                    'createNewRelationLinkTitle' => 'LLL:EXT:cms/locallang_ttc.xlf:media.addFileReference',
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
    'mindshapeseo_ogdescription' => array(
        'exclude' => 1,
        'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_pages.mindshapeseo_ogdescription',
        'config' => array(
            'type' => 'text',
            'cols' => 40,
            'rows' => 15,
            'eval' => 'trim',
        ),
    ),
    'mindshapeseo_disable_title_attachment' => array(
        'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_pages.mindshapeseo_disable_title_attachment',
        'exclude' => 1,
        'config' => array(
            'type' => 'check',
        ),
    ),
    'mindshapeseo_priority' => array(
        'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_pages.mindshapeseo_priority',
        'exclude' => 1,
        'config' => array(
            'type' => 'input',
            'size' => 5,
            'eval' => 'double2',
        ),
    ),
    'mindshapeseo_change_frequency' => array(
        'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_pages.mindshapeseo_change_frequency',
        'exclude' => 1,
        'config' => array(
            'type' => 'select',
            'renderType' => 'selectSingle',
            'default' => '',
            'items' => array(
                array(
                    'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_pages.mindshapeseo_change_frequency.none',
                    null,
                ),
                array(
                    'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_pages.mindshapeseo_change_frequency.always',
                    \Mindshape\MindshapeSeo\Domain\Model\SitemapNode::CHANGE_FREQUENCY_ALWAYS,
                ),
                array(
                    'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_pages.mindshapeseo_change_frequency.hourly',
                    \Mindshape\MindshapeSeo\Domain\Model\SitemapNode::CHANGE_FREQUENCY_HOULRY,
                ),
                array(
                    'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_pages.mindshapeseo_change_frequency.daily',
                    \Mindshape\MindshapeSeo\Domain\Model\SitemapNode::CHANGE_FREQUENCY_DAILY,
                ),
                array(
                    'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_pages.mindshapeseo_change_frequency.weekly',
                    \Mindshape\MindshapeSeo\Domain\Model\SitemapNode::CHANGE_FREQUENCY_WEEKLY,
                ),
                array(
                    'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_pages.mindshapeseo_change_frequency.monthly',
                    \Mindshape\MindshapeSeo\Domain\Model\SitemapNode::CHANGE_FREQUENCY_MONTHLY,
                ),
                array(
                    'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_pages.mindshapeseo_change_frequency.yearly',
                    \Mindshape\MindshapeSeo\Domain\Model\SitemapNode::CHANGE_FREQUENCY_YEARLY,
                ),
                array(
                    'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_pages.mindshapeseo_change_frequency.never',
                    \Mindshape\MindshapeSeo\Domain\Model\SitemapNode::CHANGE_FREQUENCY_NEVER,
                ),
            ),
        ),
    ),
    'mindshapeseo_no_index' => array(
        'label' => '',
        'exclude' => 1,
        'config' => array(
            'type' => 'check',
            'items' => array(
                array(
                    'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_pages.mindshapeseo_no_index',
                    '',
                ),
            ),
        ),
    ),
    'mindshapeseo_no_index_recursive' => array(
        'label' => '',
        'exclude' => 1,
        'config' => array(
            'type' => 'check',
            'items' => array(
                array(
                    'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_pages.mindshapeseo_no_index_recursive',
                    '',
                ),
            ),
        ),
    ),
    'mindshapeseo_no_follow' => array(
        'label' => '',
        'exclude' => 1,
        'config' => array(
            'type' => 'check',
            'items' => array(
                array(
                    'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_pages.mindshapeseo_no_follow',
                    '',
                ),
            ),
        ),
    ),
    'mindshapeseo_no_follow_recursive' => array(
        'label' => '',
        'exclude' => 1,
        'config' => array(
            'type' => 'check',
            'items' => array(
                array(
                    'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_pages.mindshapeseo_no_follow_recursive',
                    '',
                ),
            ),
        ),
    ),
    'mindshapeseo_exclude_from_sitemap' => array(
        'label' => '',
        'exclude' => 1,
        'config' => array(
            'type' => 'check',
            'items' => array(
                array(
                    'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_pages.mindshapeseo_exclude_from_sitemap',
                    '',
                ),
            ),
        ),
    ),
    'mindshapeseo_exclude_suppages_from_sitemap' => array(
        'label' => '',
        'exclude' => 1,
        'config' => array(
            'type' => 'check',
            'items' => array(
                array(
                    'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_pages.mindshapeseo_exclude_suppages_from_sitemap',
                    '',
                ),
            ),
        ),
    ),
    'mindshapeseo_sub_sitemap' => array(
        'label' => '',
        'exclude' => 1,
        'config' => array(
            'type' => 'check',
            'items' => array(
                array(
                    'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_pages.mindshapeseo_sub_sitemap',
                    '',
                ),
            ),
        ),
    ),
    'mindshapeseo_canonical' => array(
        'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_pages.mindshapeseo_canonical',
        'exclude' => 1,
        'config' => array(
            'type' => 'input',
            'size' => '30',
            'softref' => 'typolink',
            'wizards' => array(
                '_PADDING' => 2,
                'link' => array(
                    'type' => 'popup',
                    'title' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_label.link_wizard',
                    'module' => array(
                        'name' => 'wizard_element_browser',
                        'urlParameters' => array(
                            'mode' => 'wizard',
                        ),
                    ),
                    'JSopenParams' => 'height=600,width=500,status=0,menubar=0,scrollbars=1',
                ),
            ),
        ),
    ),
    'mindshapeseo_alternative_title' => array(
        'exclude' => 1,
        'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_pages.mindshapeseo_alternative_title',
        'config' => array(
            'type' => 'input',
            'size' => 50,
            'eval' => 'trim',
            'max' => 255,
        ),
    ),
    'mindshapeseo_jsonld_breadcrumb_title' => array(
        'exclude' => 1,
        'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_pages.mindshapeseo_jsonld_breadcrumb_title',
        'config' => array(
            'type' => 'input',
            'size' => 50,
            'eval' => 'trim',
            'max' => 255
        ),
    ),
    'mindshapeseo_jsonld_breadcrumb_title' => array(
        'exclude' => 1,
        'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_domain_model_pages.mindshapeseo_jsonld_breadcrumb_title',
        'config' => array(
            'type' => 'input',
            'size' => 50,
            'eval' => 'trim',
            'max' => 255
        ),
    ),
);

$GLOBALS['TCA']['pages']['columns']['description']['label'] = 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_label.page_description';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages', $columns);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages_language_overlay', $columns);
unset($columns);

$tables = array('pages', 'pages_language_overlay');

foreach ($tables as $table) {
    $GLOBALS['TCA'][$table]['palettes']['mindshape_seo_sitemap_pallette'] = array(
        'showitem' => 'mindshapeseo_priority, --linebreak--,
                   mindshapeseo_change_frequency, --linebreak--,
                   mindshapeseo_exclude_from_sitemap, --linebreak--,
                   mindshapeseo_exclude_suppages_from_sitemap, --linebreak--,
                   mindshapeseo_sub_sitemap',
    );

    $GLOBALS['TCA'][$table]['palettes']['mindshape_seo_indexing_pallette'] = array(
        'showitem' => 'mindshapeseo_no_index,
                       --linebreak--,
                       mindshapeseo_no_index_recursive,
                       --linebreak--,
                       mindshapeseo_no_follow,
                       --linebreak--,
                       mindshapeseo_no_follow_recursive',
    );

    $GLOBALS['TCA'][$table]['palettes']['mindshape_seo_jsonld_pallette'] = array(
        'showitem' => 'mindshapeseo_jsonld_breadcrumb_title',
    );

    $GLOBALS['TCA'][$table]['palettes']['mindshape_seo_meta_pallette']['showitem'] =
        'mindshapeseo_focus_keyword,--linebreak--,mindshapeseo_disable_title_attachment,--linebreak--,' .
        'description,--linebreak--,mindshapeseo_canonical';

    unset($GLOBALS['TCA'][$table]['palettes']['metatags']);

    // Facebook metadata tab
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
        $table,
        '--div--;LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_label.facebook_metadata,
        mindshapeseo_ogtitle,
        mindshapeseo_ogurl,
        mindshapeseo_ogimage,
        mindshapeseo_ogdescription',
        '1,4',
        ''
    );

    // SEO tab
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
        $table,
        '--div--;LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_label.seo,
        --palette--;LLL:EXT:mindshape_seo/Resources/Private/Language/locallang_backend_preview.xlf:label.general;mindshape_seo_google_preview_pallette,
        --palette--;LLL:EXT:mindshape_seo/Resources/Private/Language/locallang_backend_preview.xlf:label.general;mindshape_seo_general_pallette,
        --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.metadata;mindshape_seo_meta_pallette, 
        --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.editorial;mindshape_seo_editorial_pallette, 
        --palette--;LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_label.jsonld;mindshape_seo_jsonld_pallette, 
        --palette--;LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_label.indexing;mindshape_seo_indexing_pallette, 
        --palette--;LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_mindshapeseo_label.sitemap;mindshape_seo_sitemap_pallette',
        '1,4',
        ''
    );

    // Force inject alternative page title into title palette after nav_title
    $GLOBALS['TCA'][$table]['palettes']['title']['showitem'] = preg_replace_callback(
        '/(title.*?,)/i',
        function ($matches) {
            return $matches[1] . '--linebreak--,mindshapeseo_alternative_title,';
        },
        $GLOBALS['TCA'][$table]['palettes']['title']['showitem'],
        1
    );
}

unset($tables);
