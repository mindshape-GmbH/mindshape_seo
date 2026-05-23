<?php
namespace Mindshape\MindshapeSeo\Service;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2026 Daniel Dorndorf <dorndorf@mindshape.de>, mindshape GmbH
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

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

class SessionService implements SingletonInterface
{
    public const SESSION_KEY_PREFIX = 'mindshape_seo_';


    protected BackendUserAuthentication|FrontendUserAuthentication|null $userAuthentication = null;


    public function setKey(string $key, mixed $data): void
    {
        $this->getUserAuthentication()->setAndSaveSessionData(
            self::SESSION_KEY_PREFIX . $key,
            $data
        );
    }

    public function deleteKey(string $key): void
    {
        $this->setKey($key, null);
    }

    public function getKey(string $key): mixed
    {
        return $this->getUserAuthentication()->getSessionData(self::SESSION_KEY_PREFIX . $key);
    }

    public function hasKey(string $key): bool
    {
        return null !== $this->getUserAuthentication()->getSessionData(self::SESSION_KEY_PREFIX . $key);
    }

    /**
     * Lazily resolve the current user authentication. Constructor-time access to
     * `$GLOBALS['TYPO3_REQUEST']` is unsafe (CLI / scheduler) and `$GLOBALS['TSFE']`
     * is deprecated, so we resolve on demand from the request attributes that are
     * guaranteed to exist when this service is actually used.
     *
     * @throws \RuntimeException when called outside of a BE/FE request context.
     */
    protected function getUserAuthentication(): BackendUserAuthentication|FrontendUserAuthentication
    {
        if ($this->userAuthentication !== null) {
            return $this->userAuthentication;
        }

        $request = $GLOBALS['TYPO3_REQUEST'] ?? null;

        if (!$request instanceof ServerRequestInterface) {
            throw new \RuntimeException(
                'SessionService requires an active HTTP request and cannot be used in CLI contexts.',
                1716460900
            );
        }

        $applicationType = ApplicationType::fromRequest($request);

        if ($applicationType->isBackend() && $GLOBALS['BE_USER'] instanceof BackendUserAuthentication) {
            return $this->userAuthentication = $GLOBALS['BE_USER'];
        }

        $frontendUser = $request->getAttribute('frontend.user');

        if ($frontendUser instanceof FrontendUserAuthentication) {
            return $this->userAuthentication = $frontendUser;
        }

        throw new \RuntimeException(
            'SessionService could not resolve a user authentication object from the current request.',
            1716460901
        );
    }
}
