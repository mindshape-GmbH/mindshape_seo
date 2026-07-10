<?php
declare(strict_types=1);

namespace Mindshape\MindshapeSeo\Updates;

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

use Mindshape\MindshapeSeo\Domain\Model\Configuration;
use Mindshape\MindshapeSeo\Utility\DatabaseUtility;
use TYPO3\CMS\Install\Attribute\UpgradeWizard;
use TYPO3\CMS\Install\Updates\DatabaseUpdatedPrerequisite;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

#[UpgradeWizard('mindshapeSeo_titleAttachmentSeparator')]
class TitleAttachmentSeparatorUpdateWizard implements UpgradeWizardInterface
{
    protected const COLUMN_OLD = 'title_attachment_seperator';
    protected const COLUMN_NEW = 'title_attachment_separator';

    public function getTitle(): string
    {
        return 'Migrate mindshape SEO title attachment separator';
    }

    public function getDescription(): string
    {
        return 'Copies the values from the misspelled column "' . self::COLUMN_OLD . '" to the renamed column "' . self::COLUMN_NEW . '" in the mindshape SEO configuration table.';
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function updateNecessary(): bool
    {
        if (!$this->oldColumnExists()) {
            return false;
        }

        $connection = DatabaseUtility::databaseConnection();

        return (int) $connection->executeQuery(
            'SELECT COUNT(*) FROM ' . $connection->quoteIdentifier(Configuration::TABLE) .
            ' WHERE ' . $connection->quoteIdentifier(self::COLUMN_OLD) . ' != \'\'' .
            ' AND ' . $connection->quoteIdentifier(self::COLUMN_NEW) . ' = \'\''
        )->fetchOne() > 0;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function executeUpdate(): bool
    {
        $connection = DatabaseUtility::databaseConnection();

        $connection->executeStatement(
            'UPDATE ' . $connection->quoteIdentifier(Configuration::TABLE) .
            ' SET ' . $connection->quoteIdentifier(self::COLUMN_NEW) . ' = ' . $connection->quoteIdentifier(self::COLUMN_OLD) .
            ' WHERE ' . $connection->quoteIdentifier(self::COLUMN_OLD) . ' != \'\'' .
            ' AND ' . $connection->quoteIdentifier(self::COLUMN_NEW) . ' = \'\''
        );

        return true;
    }

    public function getPrerequisites(): array
    {
        return [
            DatabaseUpdatedPrerequisite::class,
        ];
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    protected function oldColumnExists(): bool
    {
        $columns = DatabaseUtility::databaseConnection()
            ->createSchemaManager()
            ->listTableColumns(Configuration::TABLE);

        return array_key_exists(self::COLUMN_OLD, $columns);
    }
}
