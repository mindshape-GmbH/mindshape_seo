<?php

namespace Mindshape\MindshapeSeo\Domain\Repository;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2021 Daniel Dorndorf <dorndorf@mindshape.de>
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
use PDO;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
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
    protected $defaultOrderings = [
        'domain' => QueryInterface::ORDER_DESCENDING,
    ];

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
     * @param bool $returnDefaultIfNotFound
     * @param int|null $sysLanguageUid
     * @return \Mindshape\MindshapeSeo\Domain\Model\Configuration
     */
    public function findByDomain(string $domain, bool $returnDefaultIfNotFound = false, int $sysLanguageUid = null)
    {
        if (0 < $sysLanguageUid) {
            return $this->findByDomainTranslation($domain, $returnDefaultIfNotFound, $sysLanguageUid);
        }

        $query = $this->createQuery();

        $constraint[] = $query->equals('domain', $domain);

        if ($returnDefaultIfNotFound) {
            $constraint[] = $query->equals('domain', Configuration::DEFAULT_DOMAIN);
        }

        $query->matching(
            $query->logicalOr($constraint)
        );

        return $query->execute()->getFirst();
    }

    /**
     * @param string $domain
     * @param bool $returnDefaultIfNotFound
     * @param null $sysLanguageUid
     * @return \Mindshape\MindshapeSeo\Domain\Model\Configuration
     */
    public function findByDomainTranslation(string $domain, bool $returnDefaultIfNotFound = false, $sysLanguageUid = null): ?Configuration
    {
        $queryBuilder = DatabaseUtility::queryBuilder();

        $queryBuilder->getRestrictions()->removeAll();
        $queryBuilder->getRestrictions()->add(GeneralUtility::makeInstance(DeletedRestriction::class));

        $queryBuilder
            ->select('*')
            ->from(Configuration::TABLE)
            ->where(
                $queryBuilder->expr()->eq(
                    'sys_language_uid',
                    $queryBuilder->createNamedParameter($sysLanguageUid, PDO::PARAM_INT)
                )
            );


        $domainQueryExpression = $queryBuilder->expr()->eq(
            'domain',
            $queryBuilder->createNamedParameter($domain)
        );

        if (false === $returnDefaultIfNotFound) {
            $queryBuilder->andWhere($domainQueryExpression);
        } else {
            $queryBuilder->orWhere(
                $queryBuilder->expr()->orX(
                    $domainQueryExpression,
                    $queryBuilder->expr()->eq('domain', '*')
                )
            );
        }

        $rawConfiguration = $queryBuilder->execute()->fetch();

        if (
            true === is_array($rawConfiguration) &&
            0 < count($rawConfiguration)
        ) {
            return $this->mapRawConfiguration($rawConfiguration);
        }

        return null;
    }

    /**
     * @param array $record
     * @return \Mindshape\MindshapeSeo\Domain\Model\Configuration|null
     */
    protected function mapRawConfiguration(array $record): ?Configuration
    {
        /** @var \TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper $dataMapper */
        $dataMapper = GeneralUtility::makeInstance(DataMapper::class);
        $records = $dataMapper->map(Configuration::class, [$record]);

        if (count($records) > 0) {
            /** @var \Mindshape\MindshapeSeo\Domain\Model\Configuration $configuration */
            $configuration = array_shift($records);
        } else {
            $configuration = null;
        }

        return $configuration;
    }

    /**
     * @param \Mindshape\MindshapeSeo\Domain\Model\Configuration $configuration
     * @return void
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     */
    public function save(Configuration $configuration)
    {
        if ($configuration->_isNew()) {
            $this->add($configuration);
        } else {
            $this->update($configuration);
        }
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
     */
    public function mergeConfigurationWithDefault(Configuration $configuration): void
    {
        if (Configuration::DEFAULT_DOMAIN !== $configuration->getDomain()) {
            $defaultConfiguration = $this->findByDomain(Configuration::DEFAULT_DOMAIN);

            if ($defaultConfiguration instanceof Configuration) {
                $configuration->mergeConfiguration($defaultConfiguration);
            }
        }
    }

    /**
     * @param \Mindshape\MindshapeSeo\Domain\Model\Configuration $configuration
     * @return void
     */
    protected function checkFileReferences(Configuration $configuration)
    {
        if (null === $configuration->getJsonldLogo()) {
            $queryBuilder = DatabaseUtility::queryBuilder();

            $fileReference = $queryBuilder
                ->select('f.uid')
                ->from('sys_file_reference', 'f')
                ->where(
                    $queryBuilder->expr()->eq(
                        'f.tablenames',
                        $queryBuilder->createNamedParameter(Configuration::TABLE, PDO::PARAM_STR)
                    ),
                    $queryBuilder->expr()->eq(
                        'f.fieldname',
                        $queryBuilder->createNamedParameter('jsonld_logo', PDO::PARAM_STR)
                    ),
                    $queryBuilder->expr()->eq(
                        'f.uid_foreign',
                        $queryBuilder->createNamedParameter($configuration->getUid(), PDO::PARAM_INT)
                    )
                )
                ->execute()
                ->fetch();

            if (is_array($fileReference)) {
                $queryBuilder = DatabaseUtility::queryBuilder();
                $queryBuilder
                    ->update('sys_file_reference', 'f')
                    ->where(
                        $queryBuilder->expr()->eq(
                            'f.uid',
                            $queryBuilder->createNamedParameter($fileReference['uid'], PDO::PARAM_INT)
                        )
                    )
                    ->set('deleted', 1, true, PDO::PARAM_INT)
                    ->execute();

                $queryBuilder = DatabaseUtility::queryBuilder();
                $queryBuilder
                    ->update(Configuration::TABLE, 'c')
                    ->where(
                        $queryBuilder->expr()->eq(
                            'c.uid',
                            $queryBuilder->createNamedParameter($configuration->getUid(), PDO::PARAM_INT)
                        )
                    )
                    ->set('jsonld_logo', 0, true, PDO::PARAM_INT)
                    ->execute();
            }
        }
    }
}
