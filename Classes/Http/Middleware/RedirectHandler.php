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
 *  (c) 2020 Daniel Dorndorf <dorndorf@mindshape.de>, mindshape GmbH
 *
 ***/

use Mindshape\MindshapeSeo\Controller\ErrorController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\LinkHandling\LinkService;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Redirects\Service\RedirectCacheService;
use TYPO3\CMS\Redirects\Service\RedirectService;

/**
 * @package Mindshape\MindshapeCustomer\Http\Middleware
 */
class RedirectHandler implements MiddlewareInterface
{
    /**
     * @var \TYPO3\CMS\Redirects\Service\RedirectService
     */
    protected $redirectService;

    public function __construct()
    {
        if (true === version_compare('10.4', GeneralUtility::makeInstance(Typo3Version::class)->getVersion(), '<=')) {
            $this->redirectService = GeneralUtility::makeInstance(
                RedirectService::class,
                GeneralUtility::makeInstance(RedirectCacheService::class),
                GeneralUtility::makeInstance(LinkService::class),
                GeneralUtility::makeInstance(SiteFinder::class)
            );
        } else {
            $this->redirectService = GeneralUtility::makeInstance(RedirectService::class);
        }
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
            return GeneralUtility::makeInstance(ErrorController::class)->pageGoneAction($request, 'The requested page is gone');
        }

        return $handler->handle($request);
    }
}
