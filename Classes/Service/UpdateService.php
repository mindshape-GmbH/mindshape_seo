<?php
namespace Mindshape\MindshapeSeo\Service;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Daniel Dorndorf <dorndorf@mindshape.de>, mindshape GmbH
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
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * @package mindshape_seo
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class UpdateService implements SingletonInterface
{
    /**
     * @var \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected $databaseConnection;

    /**
     * @var string
     */
    protected $currentVersion;

    /**
     * @var array
     */
    protected $updateChecks = array();

    /**
     * @return \Mindshape\MindshapeSeo\Service\UpdateService
     */
    public function __construct()
    {
        $this->databaseConnection = $GLOBALS['TYPO3_DB'];
        $this->currentVersion = ExtensionManagementUtility::getExtensionVersion('mindshape_seo');
    }

    /**
     * @return bool
     */
    public function isUpdateNecessary()
    {
        $this->checkUpdatesNecessarity();

        return !empty($this->updateChecks);
    }

    /**
     * @return void
     */
    public function makeUpdates()
    {
        foreach ($this->updateChecks as $updateMethod => $updateNotice) {
            if (method_exists($this, $updateMethod)) {
                call_user_func(array($this, $updateMethod));
            }
        }
    }

    /**
     * @return string
     */
    public function getUpdateNotices()
    {
        $updateNotices = '<ul>' . PHP_EOL;

        foreach ($this->updateChecks as $updateNotice) {
            $updateNotices .= '<li>' . $updateNotice . '</li>' . PHP_EOL;
        }

        $updateNotices .= '</ul>';

        return $updateNotices;
    }

    /**
     * @return void
     */
    protected function checkUpdatesNecessarity()
    {
        $this->checkXingColumn();
    }

    /**
     * @return void
     */
    protected function checkXingColumn()
    {
        /** @var \mysqli_result $checkSortingColumn */
        $check = $this->databaseConnection->sql_query('SHOW COLUMNS FROM tx_mindshapeseo_domain_model_configuration LIKE "jsonld_same_as_xing"');

        if(0 === $check->num_rows) {
            $this->updateChecks['updateXingColumn'] = 'The missing JSON-LD SameAs Xing column was added';
        }
    }

    /**
     * @return void
     */
    protected function updateXingColumn()
    {
        $this->databaseConnection->sql_query('
            ALTER TABLE tx_mindshapeseo_domain_model_configuration
            ADD jsonld_same_as_xing varchar(255) DEFAULT \'\' NOT NULL
        ');
    }
}
