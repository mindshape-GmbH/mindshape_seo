<?php
namespace Mindshape\MindshapeSeo\Service;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2023 Daniel Dorndorf <dorndorf@mindshape.de>, mindshape GmbH
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

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

/**
 * @package mindshape_seo
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class SessionService implements SingletonInterface
{
    const SESSION_KEY_PREFIX = 'mindshape_seo_';

    /**
     * @var \TYPO3\CMS\Core\Authentication\BackendUserAuthentication|\TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication
     */
    protected BackendUserAuthentication|FrontendUserAuthentication $userAuthentication;

    public function __construct()
    {
        $this->userAuthentication = true === ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isBackend()
            ? $GLOBALS['BE_USER']
            : $GLOBALS['TSFE']->fe_user;
    }

    /**
     * @param string $key
     * @param mixed $data
     */
    public function setKey(string $key, mixed $data): void
    {
        $this->userAuthentication->setAndSaveSessionData(
            self::SESSION_KEY_PREFIX . $key,
            $data
        );
    }

    /**
     * @param string $key
     */
    public function deleteKey(string $key): void
    {
        $this->setKey($key, null);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getKey(string $key): mixed
    {
        return $this->userAuthentication->getSessionData(self::SESSION_KEY_PREFIX . $key);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function hasKey(string $key): bool
    {
        return null !== $this->userAuthentication->getSessionData(self::SESSION_KEY_PREFIX . $key);
    }
}
