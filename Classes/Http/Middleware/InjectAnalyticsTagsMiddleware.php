<?php
declare(strict_types=1);

namespace Mindshape\MindshapeSeo\Http\Middleware;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2023 Can Karadag <karadag@mindshape.de>
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

use Mindshape\MindshapeSeo\Service\HeaderDataService;
use Mindshape\MindshapeSeo\Utility\TypoScriptUtility;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Http\StreamFactory;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;


class InjectAnalyticsTagsMiddleware implements MiddlewareInterface
{

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Server\RequestHandlerInterface $handler
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $applicationType = ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST']);

        if ($applicationType->isFrontend()) {
            $response = $handler->handle($request);

            $backupRequest = null;

            /** @var \TYPO3\CMS\Core\Information\Typo3Version $typo3Version */
            $typo3Version = GeneralUtility::makeInstance(Typo3Version::class);
            if ($typo3Version->getMajorVersion() >= 13) {
                /** @var \TYPO3\CMS\Core\TypoScript\FrontendTypoScript $typoScirpt */
                $frontendTypoScript = $GLOBALS['TYPO3_REQUEST']->getAttribute('frontend.typoscript');

                if ($frontendTypoScript->hasSetup() === false) {
                    $backupRequest = $GLOBALS['TYPO3_REQUEST'];
                    $GLOBALS['TYPO3_REQUEST'] = $GLOBALS['TYPO3_REQUEST']->withAttribute('frontend.typoscript', TypoScriptUtility::getCoreTypoScriptFrontendByRequest($GLOBALS['TYPO3_REQUEST']));
                }
            }

            $headerDataService = GeneralUtility::makeInstance(HeaderDataService::class);
            $html = (string) $response->getBody();
            $html = $headerDataService->addGoogleTagmanagerBodyToHtml($html);
            $analyticsData = $headerDataService->getAnalyticsTags();

            if ($backupRequest !== null) {
                $GLOBALS['TYPO3_REQUEST'] = $backupRequest;
            }

            if (count($analyticsData) > 0) {
                foreach ($analyticsData as $data) {
                    if ($data !== '' && mb_strpos($html, $data) === false) {
                        $html = str_ireplace("</head>", "$data</head>", $html);
                    }
                }

                $stream = GeneralUtility::makeInstance(StreamFactory::class)->createStream($html);
                $response = $response->withBody($stream);

                if ($response->hasHeader('Content-Length')) {
                    $response = $response->withHeader('Content-Length', (string) $response->getBody()->getSize());
                }
            }

            return $response;
        }

        return $handler->handle($request);
    }
}
