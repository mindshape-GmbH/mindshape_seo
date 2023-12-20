<?php

namespace Mindshape\MindshapeSeo\Backend\Form\Element;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2023 Daniel Dorndorf <dorndorf@mindshape.de>, mindshape GmbH
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use Mindshape\MindshapeSeo\Domain\Model\Configuration;
use Mindshape\MindshapeSeo\Service\DomainService;
use Mindshape\MindshapeSeo\Service\PageService;
use Mindshape\MindshapeSeo\Service\StandaloneTemplateRendererService;
use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Backend\Form\NodeFactory;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @package mindshape_seo
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class GooglePreviewElement extends AbstractFormElement
{

    /**
     * @var \Mindshape\MindshapeSeo\Service\PageService
     */
    protected $pageService;

    /**
     * @var \Mindshape\MindshapeSeo\Service\DomainService
     */
    protected $domainService;

    /**
     * @var \Mindshape\MindshapeSeo\Service\StandaloneTemplateRendererService
     */
    protected $standaloneTemplateRendererService;


    public function __construct(NodeFactory $nodeFactory, array $data)
    {
        parent::__construct($nodeFactory, $data);

        $this->pageService =  GeneralUtility::makeInstance(PageService::class);
        $this->domainService =  GeneralUtility::makeInstance(DomainService::class);
        $this->standaloneTemplateRendererService =  GeneralUtility::makeInstance(StandaloneTemplateRendererService::class);
    }


    /**
     * @return array
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\InvalidExtensionNameException
     */
    public function render()
    {
        $result = $this->initializeResultArray();

        $result['stylesheetFiles'][]  = 'EXT:mindshape_seo/Resources/Public/css/backend.min.css';

        /** @var \TYPO3\CMS\Core\Information\Typo3Version $typo3Version */
        $typo3Version = GeneralUtility::makeInstance(Typo3Version::class);
        if (version_compare( $typo3Version->getMajorVersion(), 11,'>=')) {
            $result['requireJsModules'][]  = \TYPO3\CMS\Core\Page\JavaScriptModuleInstruction::forRequireJS(
                'TYPO3/CMS/MindshapeSeo/PreviewModule'
            )->invoke('init');
        } else {
            $result['requireJsModules'][]  = ['TYPO3/CMS/MindshapeSeo/PreviewModule' => 'function (PreviewModule) {PreviewModule.init()}'];
        }

        $pageUid = $this->data['databaseRow']['uid'];
        $languageUid = (int)(true === is_array($this->data['databaseRow']['sys_language_uid'])
            ? reset($this->data['databaseRow']['sys_language_uid'])
            : $this->data['databaseRow']['sys_language_uid']);

        if (is_int($pageUid) && $pageUid > 0) {
            $configuration = $this->domainService->getPageDomainConfiguration($pageUid, $languageUid);

            $metadata = null;
            $titleAttachment = null;
            $titleAttachmentSeperator = null;
            $titleAttachmentPosition = null;

            if ($this->pageService->hasFrontendController()) {
                if ($configuration instanceof Configuration) {
                    $metadata = $this->pageService->getPageMetaData(
                        $pageUid,
                        $languageUid,
                        $configuration->getJsonldCustomUrl(),
                        $configuration->getAddJsonldBreadcrumb()
                    );

                    $titleAttachment = $configuration->getTitleAttachment();
                    $titleAttachmentSeperator = $configuration->getTitleAttachmentSeperator();
                    $titleAttachmentPosition = $configuration->getTitleAttachmentPosition();
                } else {
                    $metadata = $this->pageService->getPageMetaData($pageUid, $this->pageService->getCurrentSysLanguageUid());
                }
            }

            $result['html'] = $this->standaloneTemplateRendererService->render('TCA', 'GooglePreview', [
                'typo3Version' => $typo3Version->getMajorVersion(),
                'metadata' => $metadata,
                'titleAttachment' => $titleAttachment,
                'titleAttachmentSeperator' => $titleAttachmentSeperator,
                'titleAttachmentPosition' => $titleAttachmentPosition,
                'tcaName' => $this->data['parameterArray']['itemFormElName'],
                'focusKeyword' => $this->data['parameterArray']['itemFormElValue'],
            ]);
        }

        return $result;
    }
}
