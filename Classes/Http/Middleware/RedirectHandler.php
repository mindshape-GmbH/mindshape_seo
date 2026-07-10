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
 *  (c) 2026 Daniel Dorndorf <dorndorf@mindshape.de>, mindshape GmbH
 *
 ***/

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Controller\ErrorPageController;
use TYPO3\CMS\Core\Error\PageErrorHandler\InvalidPageErrorHandlerException;
use TYPO3\CMS\Core\Error\PageErrorHandler\PageErrorHandlerNotConfiguredException;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\ErrorController;
use TYPO3\CMS\Redirects\Service\RedirectService;

class RedirectHandler implements MiddlewareInterface
{
    public function __construct(
        protected RedirectService $redirectService
    ) {
    }

    /**
     * @throws \TYPO3\CMS\Core\Error\Http\PageNotFoundException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $port = $request->getUri()->getPort();
        $matchedRedirect = $this->redirectService->matchRedirect(
            $request->getUri()->getHost() . ($port ? ':' . $port : ''),
            $request->getUri()->getPath(),
            $request->getUri()->getQuery()
        );

        if (
            true === is_array($matchedRedirect) &&
            410 === $matchedRedirect['target_statuscode']
        ) {
            /** @var \TYPO3\CMS\Frontend\Controller\ErrorController $errorController */
            $errorController = GeneralUtility::makeInstance(ErrorController::class);
            $messageTitle = 'The requested page is gone';

            // The method "customErrorAction" is not available in v13
            if (method_exists($errorController, 'customErrorAction')) {
                return $errorController->customErrorAction($request, 410, $messageTitle, '');
            }

            /** @var \TYPO3\CMS\Core\Site\Entity\Site $site */
            $site = $request->getAttribute('site');

            try {
                $errorHandler = $site->getErrorHandler(410);
                $response = $errorHandler->handlePageError($request, $messageTitle);
            } catch (PageErrorHandlerNotConfiguredException|InvalidPageErrorHandlerException) {
                if (str_contains($request->getHeaderLine('Accept'), 'application/json')) {
                    return new JsonResponse(['reason' => $messageTitle], 410);
                }

                /** @var \TYPO3\CMS\Core\Controller\ErrorPageController $errorPageController */
                $errorPageController = GeneralUtility::makeInstance(ErrorPageController::class);

                return new HtmlResponse($errorPageController->errorAction($messageTitle, ''), 410);
            }

            return $response;
        }

        return $handler->handle($request);
    }
}
