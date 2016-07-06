<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

if (TYPO3_MODE === 'BE') {

    /**
     * Registers a Backend Module
     */
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'Mindshape.' . $_EXTKEY,
        'web',     // Make module a submodule of 'web'
        'settings',    // Submodule key
        '',                        // Position
        array(
            'Seo' => 'index',
        ),
        array(
            'access' => 'user,group',
            'icon' => 'EXT:' . $_EXTKEY . '/ext_icon.png',
            'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_backend.xlf',
        )
    );

    /**
     * Registers a Backend Module
     */
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'Mindshape.' . $_EXTKEY,
        'tools',     // Make module a submodule of 'web'
        'settings_general',    // Submodule key
        '',                        // Position
        array(
            'Seo' => 'index',
        ),
        array(
            'access' => 'user,group',
            'icon' => 'EXT:' . $_EXTKEY . '/ext_icon.png',
            'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_backend.xlf',
        )
    );

}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'mindshape SEO');

$facebookColumns = array(
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
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages', $facebookColumns, 1);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('pages', '--div--;Facebook Metadaten,mindshapeseo_ogtitle, mindshapeseo_ogurl, mindshapeseo_ogimage, mindshapeseo_ogdescription', '', '');

unset($facebookColumns);

$seoColumns = array(
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
            'default' => NULL,
            'items' => array(
                array('LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_minshapeseo_domain_model_pages.mindshapeseo_change_frequency.none', NULL),
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
    'mindshapeseo_no_follow' => array(
        'label' => 'LLL:EXT:mindshape_seo/Resources/Private/Language/locallang.xlf:tx_minshapeseo_domain_model_pages.mindshapeseo_no_follow',
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
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages', $seoColumns, 1);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('pages', '--div--;SEO,mindshapeseo_priority, mindshapeseo_change_frequency, mindshapeseo_no_index, mindshapeseo_no_follow, mindshapeseo_exclude_from_sitemap, mindshapeseo_exclude_suppages_from_sitemap, mindshapeseo_sub_sitemap', '', '');

unset($seoColumns);
