<?php
declare(strict_types=1);

namespace Mindshape\MindshapeSeo\Http\Middleware;

/***
 *
 * This file is part of the "mindshape SEO" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2023 Daniel Dorndorf <dorndorf@mindshape.de>, mindshape GmbH
 *
 ***/

use Mindshape\MindshapeSeo\Controller\ErrorController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Redirects\Service\RedirectService;

/**
 * @package Mindshape\MindshapeCustomer\Http\Middleware
 */
class RedirectHandler implements MiddlewareInterface
{
    /**
     * @var \TYPO3\CMS\Redirects\Service\RedirectService
     */
    protected RedirectService $redirectService;

    public function __construct()
    {
        $this->redirectService = GeneralUtility::makeInstance(RedirectService::class);
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Server\RequestHandlerInterface $handler
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \TYPO3\CMS\Core\Error\Http\PageNotFoundException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $port = $request->getUri()->getPort();
        $matchedRedirect = $this->redirectService->matchRedirect(
            $request->getUri()->getHost() . ($port ? ':' . $port : ''),
            $request->getUri()->getPath(),
            $request->getUri()->getQuery() ?? ''
        );

        if (
            true === is_array($matchedRedirect) &&
            410 === $matchedRedirect['target_statuscode']
        ) {
            /** @var \Mindshape\MindshapeSeo\Controller\ErrorController $errorController */
            $errorController = GeneralUtility::makeInstance(ErrorController::class);

            return $errorController->pageGoneAction($request, 'The requested page is gone');
        }

        return $handler->handle($request);
    }
}
