<?php

namespace Mindshape\MindshapeSeo\Service;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2021 Daniel Dorndorf <dorndorf@mindshape.de>, mindshape GmbH
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
use TYPO3\CMS\Core\SingletonInterface;

/**
 * @package mindshape_seo
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class LanguageService implements SingletonInterface
{
    /**
     * @return array
     */
    public function getLanguagesAvailable(): array
    {
        $result = DatabaseUtility::queryBuilder()
            ->select('*')
            ->from('sys_language')
            ->execute()
            ->fetchAll();

        if (true === is_array($result)) {
            return $result;
        }

        return [];
    }

    /**
     * @param int $pageUid
     * @return array
     */
    public function getPageLanguagesAvailable(int $pageUid): array
    {
        $queryBuilder = DatabaseUtility::queryBuilder();

        $result = $queryBuilder
            ->select('l.uid', 'l.title')
            ->from('sys_language', 'l')
            ->innerJoin(
                'l',
                'pages',
                'p',
                $queryBuilder->expr()->eq('p.sys_language_uid', $queryBuilder->quoteIdentifier('l.uid'))
            )
            ->where(
                $queryBuilder->expr()->eq('p.l10n_parent', $queryBuilder->createNamedParameter($pageUid, PDO::PARAM_INT)),
                $queryBuilder->expr()->neq('p.sys_language_uid', 0)
            )
            ->execute()
            ->fetchAll();

        if (is_array($result)) {
            return $result;
        } else {
            return [];
        }
    }
}
