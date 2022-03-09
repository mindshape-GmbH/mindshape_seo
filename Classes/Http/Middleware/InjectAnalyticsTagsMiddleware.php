<?php
declare(strict_types=1);

namespace Mindshape\MindshapeSeo\Http\Middleware;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2021 Can Karadag <karadag@mindshape.de>
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
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Http\StreamFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Exception\InvalidExtensionNameException;


class InjectAnalyticsTagsMiddleware implements MiddlewareInterface
{

    protected $headerDataService;

    /**
     * @param \Mindshape\MindshapeSeo\Service\HeaderDataService $headerDataService
     */
    public function __construct(HeaderDataService $headerDataService) {
        $this->headerDataService = $headerDataService;
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Server\RequestHandlerInterface $handler
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            $applicationType = ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST']);
            if ($applicationType->isFrontend()) {
                $response = $handler->handle($request);
                $html = (string)$response->getBody();
                try {
                    $html = $this->headerDataService->addGoogleTagmanagerBodyToHtml($html);
                    $analyticsData = $this->headerDataService->getAnalyticsTags();
                    if (count($analyticsData) > 0) {
                        foreach ($analyticsData as $data) {
                            if ($data !== '' && mb_strpos($html, $data) === false) {
                                $html = str_ireplace("</head>", "$data</head>", $html);
                            }
                        }

                        $stream = GeneralUtility::makeInstance(StreamFactory::class)->createStream($html);
                        $response = $response->withBody($stream);
                        if ($response->hasHeader('Content-Length')) {
                            $response = $response->withHeader('Content-Length', (string)$response->getBody()->getSize());
                        }
                    }
                } catch (InvalidExtensionNameException $e) {
                }
                return $response;
            }
        } catch (\RuntimeException $e) {
        }
        return  $handler->handle($request);
    }
}
