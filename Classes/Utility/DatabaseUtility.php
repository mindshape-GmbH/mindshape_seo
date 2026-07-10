<?php
declare(strict_types=1);

namespace Mindshape\MindshapeSeo\Utility;

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

use Doctrine\DBAL\Exception as DBALException;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class DatabaseUtility
{

    private static ?Connection $databaseConnection = null;


    public static function databaseConnection(): Connection
    {
        if (static::$databaseConnection instanceof Connection) {
            return static::$databaseConnection;
        }

        /** @var ConnectionPool $connectionPool */
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);

        try {
            $connection = $connectionPool->getConnectionByName(ConnectionPool::DEFAULT_CONNECTION_NAME);
        } catch (DBALException) {
            throw new \RuntimeException(
                'Unable to obtain default database connection for mindshape_seo.',
                1716460800
            );
        }

        return static::$databaseConnection = $connection;
    }

    public static function queryBuilder(): QueryBuilder
    {
        $connection = static::databaseConnection();

        return $connection->createQueryBuilder();
    }
}
