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

use TYPO3\CMS\Core\DataHandling\TableColumnType;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\ColumnMap;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;

/**
 * @package Mindshape\MindshapeSeo\Service
 */
class TranslationService implements SingletonInterface
{
    /**
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper
     */
    protected $dataMapper;

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper $dataMapper
     * @return void
     */
    public function injectDataMapper(DataMapper $dataMapper)
    {
        $this->dataMapper = $dataMapper;
    }

    /**
     * @param \TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface $translation
     * @param int $languageUid
     * @throws \Mindshape\MindshapeSeo\Service\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     */
    public function translate(DomainObjectInterface $translation, int $languageUid, int $translationOriginUid = 0): void
    {
        $dataMap = $this->dataMapper->getDataMap(get_class($translation));

        if (true === empty($dataMap->getTranslationOriginColumnName())) {
            throw new Exception('The type is not translatable.', 1432500079);
        }

        $translationOriginPropertyName = GeneralUtility::underscoredToLowerCamelCase($dataMap->getTranslationOriginColumnName());

        if (false === $translation->_setProperty($translationOriginPropertyName, $translationOriginUid)) {
            $columnMap = $dataMap->getColumnMap($translationOriginPropertyName);
            $columnMap->setTypeOfRelation(ColumnMap::RELATION_HAS_ONE);
            $columnMap->setType(TableColumnType::cast('select'));
            $columnMap->setChildTableName($dataMap->getTableName());

            $translation->{$translationOriginPropertyName} = $translationOriginUid;
        }

        $translation->_setProperty('_languageUid', $languageUid);
    }
}
