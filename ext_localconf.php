<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-preProcess'][] = \Mindshape\MindshapeSeo\Hook\RenderPreProcessHook::class . '->main';
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerTypeConverter(\Mindshape\MindshapeSeo\Property\TypeConverter\UploadedFileReferenceConverter::class);

$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Fluid\ViewHelpers\Widget\Controller\PaginateController::class] = array(
    'className' => \Mindshape\MindshapeSeo\ViewHelpers\Widget\Controller\PaginateController::class
);
