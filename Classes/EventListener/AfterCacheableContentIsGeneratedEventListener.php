<?php
declare(strict_types=1);

namespace Mindshape\MindshapeSeo\EventListener;

/***
 *
 * This file is part of the "mindshape SEO" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2026 Daniel Dorndorf <dorndorf@featdd.de>
 *
 ***/

use Mindshape\MindshapeSeo\Service\HeaderDataService;
use TYPO3\CMS\Frontend\Event\AfterCacheableContentIsGeneratedEvent;

class AfterCacheableContentIsGeneratedEventListener
{
    public function __construct(
        protected HeaderDataService $headerDataService
    )
    {}

    /**
     * @param \TYPO3\CMS\Frontend\Event\AfterCacheableContentIsGeneratedEvent $afterCacheableContentIsGeneratedEvent
     * @throws \Featdd\DpnGlossary\Service\Exception
     */
    public function __invoke(AfterCacheableContentIsGeneratedEvent $afterCacheableContentIsGeneratedEvent): void
    {
        // Fallback for v13 event where content still lays in the TypoScriptFrontendController
        if (method_exists($afterCacheableContentIsGeneratedEvent, 'getController')) {
            $typoScriptFrontendController = $afterCacheableContentIsGeneratedEvent->getController();
            $setContentCallback = fn($content) => $typoScriptFrontendController->content = $content;
            $content = $typoScriptFrontendController->content;
        } else {
            $setContentCallback = fn($content) => $afterCacheableContentIsGeneratedEvent->setContent($content);
            $content = $afterCacheableContentIsGeneratedEvent->getContent();
        }

        $content = $this->headerDataService->addGoogleTagmanagerBodyToHtml($content);
        $analyticsData = $this->headerDataService->getAnalyticsTags();

        if (empty($analyticsData)) {
            return;
        }

        foreach ($analyticsData as $data) {
            if ($data !== '' && mb_strpos($content, $data) === false) {
                $content = str_ireplace("</head>", "$data</head>", $content);
            }
        }

        if (is_string($content)) {
            $setContentCallback($content);
        }
    }
}
