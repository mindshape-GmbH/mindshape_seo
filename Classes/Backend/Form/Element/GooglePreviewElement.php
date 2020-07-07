<?php


namespace Mindshape\MindshapeSeo\Backend\Form\Element;


use Mindshape\MindshapeSeo\Domain\Model\Configuration;
use Mindshape\MindshapeSeo\Service\DomainService;
use Mindshape\MindshapeSeo\Service\PageService;
use Mindshape\MindshapeSeo\Service\StandaloneTemplateRendererService;
use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Backend\Form\Element\UserElement;
use TYPO3\CMS\Backend\Form\NodeFactory;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

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

        /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->pageService = $objectManager->get(PageService::class);
        $this->domainService = $objectManager->get(DomainService::class);
        $this->standaloneTemplateRendererService = $objectManager->get(StandaloneTemplateRendererService::class);
        /** @var \TYPO3\CMS\Core\Page\PageRenderer $pageRenderer */
        $pageRenderer = $objectManager->get(PageRenderer::class);

        $pageRenderer->addRequireJsConfiguration(
            [
                'paths' => [
                    'jquery' => 'sysext/core/Resources/Public/JavaScript/Contrib/jquery/jquery.min',
                ],
            ]
        );

        if (\TYPO3\CMS\Core\Core\Environment::getContext()->isProduction()) {
            $pageRenderer->addCssFile('/typo3conf/ext/mindshape_seo/Resources/Public/css/backend.min.css');
            $pageRenderer->addJsFile('/typo3conf/ext/mindshape_seo/Resources/Public/js/backend.min.js');
        } else {
            $pageRenderer->addCssFile(
                '/typo3conf/ext/mindshape_seo/Resources/Public/css/backend.min.css',
                'stylesheet',
                'all',
                '',
                false,
                false,
                '',
                true
            );
            $pageRenderer->addJsFile(
                '/typo3conf/ext/mindshape_seo/Resources/Public/js/backend.min.js',
                'text/javascript',
                false,
                false,
                '',
                true
            );
        }
    }


    /**
     * @return array|string
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\InvalidExtensionNameException
     */
    public function render()
    {
        $result = $this->initializeResultArray();
        $pageUid =  $this->data['databaseRow']['uid'];

        if ($pageUid > 0) {
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
            $result['html'] = $this->standaloneTemplateRendererService->render('TCA', 'GooglePreview', [
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