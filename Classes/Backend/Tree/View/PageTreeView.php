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
     * @param array|int $row Item row or uid
     * @return string Image tag.
     */
    public function getIcon($row)
    {
        if (is_int($row)) {
            $row = BackendUtility::getRecord($this->table, $row);
        }
        $title = $this->showDefaultTitleAttribute ? htmlspecialchars('UID: ' . $row['uid']) : $this->getTitleAttrib($row);
        $iconFactory = GeneralUtility::makeInstance(IconFactory::class);
        $icon = '<span title="' . $title . '">' . $iconFactory->getIconForRecord('pages', $row, Icon::SIZE_SMALL)->render() . '</span>';

        return $this->wrapIcon($icon, $row);
    }

    /**
     * @param int $uid
     * @return int
     */
    public function getCount($uid)
    {
        if (is_array($this->data)) {
            $res = $this->getDataInit($uid);

            return $this->getDataCount($res);
        } else {
            $db = $this->getDatabaseConnection();
            $where = $this->parentField . '=' . $db->fullQuoteStr($uid, $this->table) . BackendUtility::deleteClause($this->table) . BackendUtility::versioningPlaceholderClause($this->table) . $this->clause;

            return $db->exec_SELECTcountRows('pages.uid', $this->table, $where);
        }
    }
}
