<?php
declare(strict_types=1);

namespace Mindshape\MindshapeSeo\Utility;


use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\VisibilityAspect;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\TypoScript\FrontendTypoScript;
use TYPO3\CMS\Core\TypoScript\IncludeTree\SysTemplateRepository;
use TYPO3\CMS\Core\TypoScript\IncludeTree\SysTemplateTreeBuilder;
use TYPO3\CMS\Core\TypoScript\IncludeTree\Traverser\ConditionVerdictAwareIncludeTreeTraverser;
use TYPO3\CMS\Core\TypoScript\IncludeTree\Traverser\IncludeTreeTraverser;
use TYPO3\CMS\Core\TypoScript\Tokenizer\LossyTokenizer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\RootlineUtility;

class TypoScriptUtility
{
    public static function getCoreTypoScriptFrontendByRequest(ServerRequestInterface $request): FrontendTypoScript
    {
        $typo3Site = $request->getAttribute('site');
        $sysTemplateRows = self::getSysTemplateRowsForAssociatedContextPageId($request);

        $frontendTypoScriptFactory = GeneralUtility::makeInstance(
            \TYPO3\CMS\Core\TypoScript\FrontendTypoScriptFactory::class,
            GeneralUtility::makeInstance(ContainerInterface::class),
            GeneralUtility::makeInstance(EventDispatcherInterface::class),
            GeneralUtility::makeInstance(SysTemplateTreeBuilder::class),
            GeneralUtility::makeInstance(LossyTokenizer::class),
            GeneralUtility::makeInstance(IncludeTreeTraverser::class),
            GeneralUtility::makeInstance(ConditionVerdictAwareIncludeTreeTraverser::class),
        );
        $frontendTypoScript = $frontendTypoScriptFactory->createSettingsAndSetupConditions(
            $typo3Site,
            $sysTemplateRows,
            [],
            null,
        );
        return $frontendTypoScriptFactory->createSetupConfigOrFullSetup(
            true,
            $frontendTypoScript,
            $typo3Site,
            $sysTemplateRows,
            [],
            '0',
            null,
            null,
        );
    }

    protected static function getSysTemplateRowsForAssociatedContextPageId(ServerRequestInterface $request): array
    {
        $pageUid = (int)(
            $request->getParsedBody()['id']
            ?? $request->getQueryParams()['id']
            ?? $request->getAttribute('site')?->getRootPageId()
        );

        /** @var Context $coreContext */
        $coreContext = clone GeneralUtility::makeInstance(Context::class);
        $coreContext->setAspect(
            'visibility',
            GeneralUtility::makeInstance(
                VisibilityAspect::class,
                false,
                false
            )
        );
        /** @var RootLineUtility $rootlineUtility */
        $rootlineUtility = GeneralUtility::makeInstance(
            RootLineUtility::class,
            $pageUid,
            '',
            $coreContext,
        );
        $rootline = $rootlineUtility->get();
        if ($rootline === []) {
            return [];
        }

        /** @var SysTemplateRepository $sysTemplateRepository */
        $sysTemplateRepository = GeneralUtility::makeInstance(
            SysTemplateRepository::class,
            GeneralUtility::makeInstance(EventDispatcherInterface::class),
            GeneralUtility::makeInstance(ConnectionPool::class),
            $coreContext,
        );

        return $sysTemplateRepository->getSysTemplateRowsByRootline(
            $rootline,
            $request
        );
    }
}
