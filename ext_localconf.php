<?php

use Mindshape\MindshapeSeo\Backend\Form\Element\GooglePreviewElement;
use Mindshape\MindshapeSeo\Backend\Form\Element\GooglePreviewElementV12;
use Mindshape\MindshapeSeo\Hook\RenderPreProcessHook;
use Mindshape\MindshapeSeo\Property\TypeConverter\UploadFileReferenceConverter;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') or die();

call_user_func(function () {
    ExtensionManagementUtility::addTypoScriptConstants(
        '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:mindshape_seo/Configuration/TypoScript/constants.typoscript">'
    );

    ExtensionManagementUtility::addTypoScriptSetup(
        '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:mindshape_seo/Configuration/TypoScript/setup.typoscript">'
    );

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-preProcess'][] = RenderPreProcessHook::class . '->main';

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1594739604] = [
        'nodeName' => 'googlePreview',
        'priority' => 40,
        'class' => GeneralUtility::makeInstance(Typo3Version::class)->getBranch() === '12.4'
            ? GooglePreviewElementV12::class
            : GooglePreviewElement::class,
    ];

});
