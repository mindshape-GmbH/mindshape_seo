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
        'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_minshapeseo_domain_model_pages.mindshapeseo_focus_keyword',
        'config' => array(
            'type' => 'input',
            'size' => 30,
            'eval' => 'trim',
        ),
    ),
    'mindshapeseo_ogtitle' => array(
        'exclude' => 0,
        'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_minshapeseo_domain_model_pages.mindshapeseo_ogtitle',
        'config' => array(
            'type' => 'input',
            'size' => 30,
            'eval' => 'trim',
        ),
    ),
    'mindshapeseo_ogurl' => array(
        'exclude' => 1,
        'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_minshapeseo_domain_model_pages.mindshapeseo_ogurl',
        'config' => array(
            'type' => 'input',
            'size' => 30,
            'eval' => 'trim',
        ),
    ),
    'mindshapeseo_ogimage' => array(
        'exclude' => 0,
        'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_minshapeseo_domain_model_pages.mindshapeseo_ogimage',
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
        'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_minshapeseo_domain_model_pages.mindshapeseo_ogdescription',
        'config' => array(
            'type' => 'text',
            'cols' => 40,
            'rows' => 15,
            'eval' => 'trim',
        ),
    ),
    'mindshapeseo_disable_title_attachment' => array(
        'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_minshapeseo_domain_model_pages.mindshapeseo_disable_title_attachment',
        'exclude' => 1,
        'config' => array(
            'type' => 'check',
        ),
    ),
    'mindshapeseo_priority' => array(
        'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_minshapeseo_domain_model_pages.mindshapeseo_priority',
        'exclude' => 1,
        'config' => array(
            'type' => 'input',
            'size' => 5,
            'eval' => 'double2',
        ),
    ),
    'mindshapeseo_change_frequency' => array(
        'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_minshapeseo_domain_model_pages.mindshapeseo_change_frequency',
        'exclude' => 1,
        'config' => array(
            'type' => 'select',
            'renderType' => 'selectSingle',
            'default' => null,
            'items' => array(
                array('LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_minshapeseo_domain_model_pages.mindshapeseo_change_frequency.none', null),
                array('LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_minshapeseo_domain_model_pages.mindshapeseo_change_frequency.always', \Mindshape\MindshapeSeo\Generator\SitemapGenerator::CHANGE_FREQUENCY_ALWAYS),
                array('LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_minshapeseo_domain_model_pages.mindshapeseo_change_frequency.hourly', \Mindshape\MindshapeSeo\Generator\SitemapGenerator::CHANGE_FREQUENCY_HOULRY),
                array('LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_minshapeseo_domain_model_pages.mindshapeseo_change_frequency.daily', \Mindshape\MindshapeSeo\Generator\SitemapGenerator::CHANGE_FREQUENCY_DAILY),
                array('LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_minshapeseo_domain_model_pages.mindshapeseo_change_frequency.weekly', \Mindshape\MindshapeSeo\Generator\SitemapGenerator::CHANGE_FREQUENCY_WEEKLY),
                array('LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_minshapeseo_domain_model_pages.mindshapeseo_change_frequency.monthly', \Mindshape\MindshapeSeo\Generator\SitemapGenerator::CHANGE_FREQUENCY_MONTHLY),
                array('LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_minshapeseo_domain_model_pages.mindshapeseo_change_frequency.yearly', \Mindshape\MindshapeSeo\Generator\SitemapGenerator::CHANGE_FREQUENCY_YEARLY),
                array('LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_minshapeseo_domain_model_pages.mindshapeseo_change_frequency.never', \Mindshape\MindshapeSeo\Generator\SitemapGenerator::CHANGE_FREQUENCY_NEVER),
            ),
        ),
    ),
    'mindshapeseo_no_index' => array(
        'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_minshapeseo_domain_model_pages.mindshapeseo_no_index',
        'exclude' => 1,
        'config' => array(
            'type' => 'check',
        ),
    ),
    'mindshapeseo_no_index_recursive' => array(
        'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_minshapeseo_domain_model_pages.mindshapeseo_no_index_recursive',
        'exclude' => 1,
        'config' => array(
            'type' => 'check',
        ),
    ),
    'mindshapeseo_no_follow' => array(
        'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_minshapeseo_domain_model_pages.mindshapeseo_no_follow',
        'exclude' => 1,
        'config' => array(
            'type' => 'check',
        ),
    ),
    'mindshapeseo_no_follow_recursive' => array(
        'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_minshapeseo_domain_model_pages.mindshapeseo_no_follow_recursive',
        'exclude' => 1,
        'config' => array(
            'type' => 'check',
        ),
    ),
    'mindshapeseo_exclude_from_sitemap' => array(
        'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_minshapeseo_domain_model_pages.mindshapeseo_exclude_from_sitemap',
        'exclude' => 1,
        'config' => array(
            'type' => 'check',
        ),
    ),
    'mindshapeseo_exclude_suppages_from_sitemap' => array(
        'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_minshapeseo_domain_model_pages.mindshapeseo_exclude_suppages_from_sitemap',
        'exclude' => 1,
        'config' => array(
            'type' => 'check',
        ),
    ),
    'mindshapeseo_sub_sitemap' => array(
        'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_minshapeseo_domain_model_pages.mindshapeseo_sub_sitemap',
        'exclude' => 1,
        'config' => array(
            'type' => 'check',
        ),
    ),
    'mindshapeseo_canonical' => array(
        'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_minshapeseo_domain_model_pages.mindshapeseo_canonical',
        'exclude' => 1,
        'config' => array(
            'type' => 'group',
            'internal_type' => 'db',
            'allowed' => 'pages',
            'default' => 0,
            'size' => 1,
            'maxitems' => 1,
            'minitems' => 0,
            'wizards' => array(
                'suggest' => array(
                    'type' => 'suggest',
                    'default' => array(
                        'additionalSearchFields' => 'nav_title, alias, url',
                    ),
                ),
            ),
        ),
    ),
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages', $columns);
unset($columns);

$GLOBALS['TCA']['pages']['palettes']['mindshape_seo_general_pallette'] = array(
    'showitem' => 'mindshapeseo_disable_title_attachment',
);

$GLOBALS['TCA']['pages']['palettes']['mindshape_seo_sitemap_pallette'] = array(
    'showitem' => 'mindshapeseo_priority, --linebreak--,
                   mindshapeseo_change_frequency, --linebreak--,
                   mindshapeseo_exclude_from_sitemap, --linebreak--,
                   mindshapeseo_exclude_suppages_from_sitemap, --linebreak--,
                   mindshapeseo_sub_sitemap',
);

$GLOBALS['TCA']['pages']['palettes']['mindshape_seo_indexing_pallette'] = array(
    'showitem' => 'mindshapeseo_no_index,
                   mindshapeseo_no_index_recursive,
                   --linebreak--,
                   mindshapeseo_no_follow,
                   mindshapeseo_no_follow_recursive',
);

$GLOBALS['TCA']['pages']['palettes']['mindshape_seo_meta_pallette']['showitem'] =
    'mindshapeseo_focus_keyword,--linebreak--,' .
    $GLOBALS['TCA']['pages']['palettes']['metatags']['showitem'] . ',--linebreak--,' .
    $GLOBALS['TCA']['pages']['palettes']['abstract']['showitem'] . ',--linebreak--,
    mindshapeseo_canonical';

$GLOBALS['TCA']['pages']['palettes']['mindshape_seo_editorial_pallette'] = $GLOBALS['TCA']['pages']['palettes']['editorial'];

unset(
    $GLOBALS['TCA']['pages']['palettes']['abstract'],
    $GLOBALS['TCA']['pages']['palettes']['metatags'],
    $GLOBALS['TCA']['pages']['palettes']['editorial']
);

// Facebook metadata tab
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'pages',
    '--div--;LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_minshapeseo_label.facebook_metadata,
    mindshapeseo_ogtitle,
    mindshapeseo_ogurl,
    mindshapeseo_ogimage,
    mindshapeseo_ogdescription',
    '1,4',
    ''
);

// SEO tab
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'pages',
    '--div--;LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_minshapeseo_label.seo,
    --palette--;LLL:EXT:mindshape_seo/Resources/Private/Language/locallang_backend_preview.xlf:label.general;mindshape_seo_general_pallette,
    --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.metadata;mindshape_seo_meta_pallette, 
    --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.editorial;mindshape_seo_editorial_pallette, 
    --palette--;LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_minshapeseo_label.indexing;mindshape_seo_indexing_pallette, 
    --palette--;LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_minshapeseo_label.sitemap;mindshape_seo_sitemap_pallette',
    '1,4',
    ''
);
