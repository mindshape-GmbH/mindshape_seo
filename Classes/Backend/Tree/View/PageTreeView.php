<?php
namespace Mindshape\MindshapeSeo\Backend\Tree\View;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017 Daniel Dorndorf <dorndorf@mindshape.de>, mindshape GmbH
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

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryHelper;
use TYPO3\CMS\Core\Database\Query\Restriction\BackendWorkspaceRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @package mindshape_seo
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class PageTreeView extends \TYPO3\CMS\Backend\Tree\View\PageTreeView
{
    /**
     * @var int
     */
    public $sysLanguageUid = 0;

    /**
     * @param array|int $row Item row or uid
     * @return string Image tag.
     */
    public function getIcon($row)
    {
        $row = true === is_int($row)
            ? BackendUtility::getRecord($this->table, $row)
            : $row;

        $title = $this->showDefaultTitleAttribute ? htmlspecialchars('UID: ' . $row['uid']) : $this->getTitleAttrib($row);
        $iconFactory = GeneralUtility::makeInstance(IconFactory::class);
        $icon = '<span title="' . $title . '">' . $iconFactory->getIconForRecord('pages', $row, Icon::SIZE_SMALL)->render() . '</span>';

        return $this->wrapIcon($icon, $row);
    }

    /**
     * @param int $parentId
     * @return mixed
     */
    public function getDataInit($parentId)
    {
        if (is_array($this->data)) {
            if (!is_array($this->dataLookup[$parentId][$this->subLevelID])) {
                $parentId = -1;
            } else {
                reset($this->dataLookup[$parentId][$this->subLevelID]);
            }
            return $parentId;
        } else {
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($this->table);
            $queryBuilder->getRestrictions()
                ->removeAll()
                ->add(GeneralUtility::makeInstance(DeletedRestriction::class))
                ->add(GeneralUtility::makeInstance(BackendWorkspaceRestriction::class));
            $queryBuilder
                ->select(...$this->fieldArray)
                ->from($this->table)
                ->where(
                    $queryBuilder->expr()->eq(
                        $this->parentField,
                        $queryBuilder->createNamedParameter($parentId, \PDO::PARAM_INT)
                    ),
                    QueryHelper::stripLogicalOperatorPrefix($this->clause)
                );

            if (0 < $this->sysLanguageUid) {
                // LEFT JOIN pages_language_overlay ON pages.uid = pages_language_overlay.pid
                $queryBuilder->leftJoin('pages', 'pages_language_overlay', 'pages_language_overlay', 'pages.uid = pages_language_overlay.pid');
                $queryBuilder->andWhere('pages_language_overlay.sys_language_uid = ' . $this->sysLanguageUid);
            }

            foreach (QueryHelper::parseOrderBy($this->orderByFields) as $orderPair) {
                list($fieldName, $order) = $orderPair;
                $queryBuilder->addOrderBy($fieldName, $order);
            }

            return $queryBuilder->execute();
        }
    }
}
