<?php
namespace Mindshape\MindshapeSeo\Domain\Repository;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Daniel Dorndorf <dorndorf@mindshape.de>
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
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * @package mindshape_seo
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class ConfigurationRepository extends Repository
{
    /**
     * @var array $defaultOrderings
     */
    protected $defaultOrderings = array(
        'domain' => QueryInterface::ORDER_DESCENDING,
    );

    /**
     * @return void
     */
    public function initializeObject()
    {
        /** @var Typo3QuerySettings $querySettings */
        $querySettings = $this->objectManager->get(Typo3QuerySettings::class);
        $querySettings->setRespectStoragePage(false);

        $this->setDefaultQuerySettings($querySettings);
    }

    /**
     * @param string $domain
     * @return Configuration
     */
    public function findByDomain($domain)
    {
        $query = $this->createQuery();

        $query->matching(
            $query->equals('domain', $domain)
        );

        return $query->execute()->getFirst();
    }

    /**
     * @param \Mindshape\MindshapeSeo\Domain\Model\Configuration $configuration
     * @return void
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    public function update($configuration)
    {
        $this->checkFileReferences($configuration);

        parent::update($configuration);
    }

    /**
     * @param \Mindshape\MindshapeSeo\Domain\Model\Configuration $configuration
     * @return void
     */
    protected function checkFileReferences(Configuration $configuration)
    {
        /** @var \TYPO3\CMS\Core\Database\DatabaseConnection $databaseConnection */
        $databaseConnection = $GLOBALS['TYPO3_DB'];

        if (null === $configuration->getFacebookDefaultImage()) {
            $fileReference = $databaseConnection->exec_SELECTgetSingleRow(
                '*',
                'sys_file_reference',
                'deleted != 1 AND tablenames = "tx_mindshapeseo_domain_model_configuration" AND fieldname = "facebook_default_image" AND uid_foreign = ' . $configuration->getUid()
            );

            if (is_array($fileReference)) {
                $this->updateFileReferences(
                    $configuration->getUid(),
                    'facebook_default_image',
                    $fileReference
                );
            }
        }

        if (null === $configuration->getFacebookDefaultImage()) {
            $fileReference = $databaseConnection->exec_SELECTgetSingleRow(
                '*',
                'sys_file_reference',
                'deleted != 1 AND tablenames = "tx_mindshapeseo_domain_model_configuration" AND fieldname = "jsonld_logo" AND uid_foreign = ' . $configuration->getUid()
            );

            if (is_array($fileReference)) {
                $this->updateFileReferences(
                    $configuration->getUid(),
                    'jsonld_logo',
                    $fileReference
                );
            }
        }
    }

    /**
     * @param int $configurationUid
     * @param string $field
     * @param array $fileReference
     */
    protected function updateFileReferences($configurationUid, $field, array $fileReference)
    {
        /** @var \TYPO3\CMS\Core\Database\DatabaseConnection $databaseConnection */
        $databaseConnection = $GLOBALS['TYPO3_DB'];

        $databaseConnection->sql_query('START TRANSACTION');

        $databaseConnection->exec_UPDATEquery(
            'sys_file_reference',
            'uid = ' . $fileReference['uid'],
            array(
                'deleted' => 1,
            )
        );

        $databaseConnection->exec_UPDATEquery(
            'tx_mindshapeseo_domain_model_configuration',
            'uid = ' . $configurationUid,
            array(
                $field => 0,
            )
        );

        $databaseConnection->sql_query('COMMIT');
    }
}
