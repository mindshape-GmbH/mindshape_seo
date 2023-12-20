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

use Mindshape\MindshapeSeo\Utility\DatabaseUtility;
use PDO;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * @package mindshape_seo
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class LanguageService implements SingletonInterface
{
    /**
     * @return array
     * @throws \Doctrine\DBAL\Exception
     */
    public function getLanguagesAvailable(): array
    {
        if ((new Typo3Version())->getMajorVersion() < 12) {
            return DatabaseUtility::queryBuilder()
                ->select('*')
                ->from('sys_language')
                ->executeQuery()
                ->fetchAllAssociative();
        }

        // TODO: update languages getter

        /** @var \TYPO3\CMS\Core\Site\Entity\SiteInterface $currentSite */
        $currentSite = $GLOBALS['TYPO3_REQUEST']->getAttribute('site');

        $siteLanguages = $currentSite->getAvailableLanguages(
            $GLOBALS['BE_USER'],
            false,
            (int) ($GLOBALS['TYPO3_REQUEST']->getParsedBody()['id'] ?? $GLOBALS['TYPO3_REQUEST']->getQueryParams()['id'] ?? 0)
        );

        $languages = [];

        foreach ($siteLanguages as $siteLanguage) {
            $languages[] = [
                'uid' => $siteLanguage->getLanguageId(),
                'title' => $siteLanguage->getTitle(),
            ];
        }

        return $languages;
    }

    /**
     * @param int $pageUid
     * @return array
     * @throws \Doctrine\DBAL\Exception
     */
    public function getPageLanguagesAvailable(int $pageUid): array
    {
        if ((new Typo3Version())->getMajorVersion() < 12) {
            $queryBuilder = DatabaseUtility::queryBuilder();

            return $queryBuilder
                ->select('l.uid', 'l.title')
                ->from('sys_language', 'l')
                ->innerJoin(
                    'l',
                    'pages',
                    'p',
                    $queryBuilder->expr()->eq('p.sys_language_uid', $queryBuilder->quoteIdentifier('l.uid'))
                )
                ->where(
                    $queryBuilder->expr()->eq('p.' . $GLOBALS['TCA']['pages']['ctrl']['transOrigPointerField'], $queryBuilder->createNamedParameter($pageUid, PDO::PARAM_INT)),
                    $queryBuilder->expr()->neq('p.sys_language_uid', 0)
                )
                ->executeQuery()
                ->fetchAllAssociative();
        }


        // TODO: update languages getter
        return [];
    }
}
