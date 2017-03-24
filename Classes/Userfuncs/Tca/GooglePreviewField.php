<?php
namespace Mindshape\MindshapeSeo\Userfuncs\Tca;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017 Daniel Dorndorf <dorndorf@mindshape.de>, mindshape GmbH
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
use TYPO3\CMS\Backend\Form\Element\UserElement;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * @package mindshape_seo
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class GooglePreviewField
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

    /**
     * @return \Mindshape\MindshapeSeo\Userfuncs\Tca\GooglePreviewField
     */
    public function __construct()
    {
        /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->pageService = $objectManager->get(PageService::class);
        $this->domainService = $objectManager->get(DomainService::class);
        $this->standaloneTemplateRendererService = $objectManager->get(StandaloneTemplateRendererService::class);
        /** @var \TYPO3\CMS\Core\Page\PageRenderer $pageRenderer */
        $pageRenderer = $objectManager->get(PageRenderer::class);
        $pageRenderer->setBackPath('../typo3/');
        $pageRenderer->loadJquery();

        if (GeneralUtility::getApplicationContext()->isProduction()) {
            $pageRenderer->addCssFile(ExtensionManagementUtility::extRelPath('mindshape_seo') . 'Resources/Public/css/backend.min.css');
            $pageRenderer->addJsFile(ExtensionManagementUtility::extRelPath('mindshape_seo') . 'Resources/Public/js/backend.min.js');
        } else {
            $pageRenderer->addCssFile(
                ExtensionManagementUtility::extRelPath('mindshape_seo') . 'Resources/Public/css/backend.min.css',
                'stylesheet',
                'all',
                '',
                false,
                false,
                '',
                true
            );
            $pageRenderer->addJsFile(
                ExtensionManagementUtility::extRelPath('mindshape_seo') . 'Resources/Public/js/backend.min.js',
                'text/javascript',
                false,
                false,
                '',
                true
            );
        }
    }

    /**
     * @param array $params
     * @param \TYPO3\CMS\Backend\Form\Element\UserElement $userElement
     * @return string
     */
    public function render(array $params, UserElement $userElement)
    {
        if ('pages_language_overlay' === $params['table']) {
            $pageUid = $params['row']['pid'];
        } else {
            $pageUid = $params['row']['uid'];
        }

        $configuration = $this->domainService->getPageDomainConfiguration($pageUid);

        $metadata = null;
        $titleAttachment = null;
        $titleAttachmentSeperator = null;
        $titleAttachmentPosition = null;

        if ($this->pageService->hasFrontendController()) {
            if ($configuration instanceof Configuration) {
                $metadata = $this->pageService->getPageMetaData(
                    $pageUid,
                    $this->pageService->getCurrentSysLanguageUid(),
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

        return $this->standaloneTemplateRendererService->render('TCA', 'GooglePreview', array(
            'metadata' => $metadata,
            'titleAttachment' => $titleAttachment,
            'titleAttachmentSeperator' => $titleAttachmentSeperator,
            'titleAttachmentPosition' => $titleAttachmentPosition,
            'tcaName' => $params['itemFormElName'],
            'focusKeyword' => $params['itemFormElValue'],
        ));
    }
}
