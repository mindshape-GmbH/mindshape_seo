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

use Mindshape\MindshapeSeo\Utility\DatabaseUtility;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Site\SiteFinder;

class LanguageService implements SingletonInterface
{

    protected SiteFinder $siteFinder;


    public function __construct(SiteFinder $siteFinder)
    {
        $this->siteFinder = $siteFinder;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getLanguagesAvailable(?string $domain = null): array
    {
        $sites = $this->siteFinder->getAllSites();
        $siteLanguages = [];
        $languages = [];

        foreach ($sites as $site) {
            if ($domain && $site->getBase()->getHost() !== $domain) {
                continue;
            }

            $siteLanguages = $site->getAllLanguages();
            break;
        }

        foreach ($siteLanguages as $siteLanguage) {
            $languages[] = [
                'uid' => $siteLanguage->getLanguageId(),
                'title' => $siteLanguage->getTitle(),
            ];
        }

        return $languages;
    }

    public function getPageLanguagesAvailable(int $pageUid): array
    {
        foreach ($this->siteFinder->getSiteByPageId($pageUid)->getAllLanguages() as $siteLanguage) {
            $languages[] = [
                'uid' => $siteLanguage->getLanguageId(),
                'title' => $siteLanguage->getTitle(),
            ];
        }

        return $languages;
    }
}
