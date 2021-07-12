<?php
declare(strict_types=1);

namespace Mindshape\MindshapeSeo\Controller;

/***
 *
 * This file is part of the "mindshape SEO" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2021 Daniel Dorndorf <dorndorf@mindshape.de>, mindshape GmbH
 *
 ***/

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use TYPO3\CMS\Core\Error\Http\PageNotFoundException;
use TYPO3\CMS\Core\Error\PageErrorHandler\PageErrorHandlerInterface;
use TYPO3\CMS\Frontend\Controller\ErrorController as CoreErrorController;

/**
 * @package Mindshape\MindshapeCustomer\Http\Middleware
 */
class ErrorController extends CoreErrorController
{
    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param string $message
     * @param array $reasons
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \TYPO3\CMS\Core\Error\Http\PageNotFoundException
     */
    public function pageGoneAction(ServerRequestInterface $request, string $message, array $reasons = []): ResponseInterface
    {
        $errorHandler = $this->getErrorHandlerFromSite($request, 410);

        if (!$errorHandler instanceof PageErrorHandlerInterface) {
            $errorHandler = $this->getErrorHandlerFromSite($request, 404);
        }

        if ($errorHandler instanceof PageErrorHandlerInterface) {
            return $errorHandler->handlePageError($request, $message, $reasons);
        }

        try {
            return $this->handleDefaultError($request, 410, $message);
        } catch (RuntimeException $exception) {
            throw new PageNotFoundException($message, 1603365931);
        }
    }
}
