<?php
declare(strict_types=1);

namespace Mindshape\MindshapeSeo\Utility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2020 Daniel Dorndorf <dorndorf@mindshape.de>, mindshape GmbH
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

use Doctrine\DBAL\DBALException;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;

/**
 * @package Mindshape\MindshapeSeo\Utility
 */
class DatabaseUtility
{
    /**
     * @var \TYPO3\CMS\Core\Database\Connection
     */
    private static $databaseConnection;

    /**
     * @return \TYPO3\CMS\Core\Database\Connection
     * @throws \TYPO3\CMS\Extbase\Object\Exception
     */
    public static function databaseConnection(): Connection
    {
        if (static::$databaseConnection instanceof Connection) {
            return static::$databaseConnection;
        }

        /** @var ConnectionPool $connectionPool */
        $connectionPool = ObjectUtility::makeInstance(ConnectionPool::class);
        $connection = null;

        try {
            $connection = $connectionPool->getConnectionByName(ConnectionPool::DEFAULT_CONNECTION_NAME);
        } catch (DBALException $exception) {
            // ignore
        }

        static::$databaseConnection = $connection;

        return $connection;
    }

    /**
     * @return \TYPO3\CMS\Core\Database\Query\QueryBuilder
     */
    public static function queryBuilder(): QueryBuilder
    {
        $connection = static::databaseConnection();

        return $connection->createQueryBuilder();
    }
}
