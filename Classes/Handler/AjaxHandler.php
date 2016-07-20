<?php
namespace Mindshape\MindshapeSeo\Handler;

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

use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\Http\AjaxRequestHandler;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * @package mindshape_seo
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class AjaxHandler implements SingletonInterface
{
    /**
     * @param array $params
     * @param AjaxRequestHandler $ajaxRequestHandler
     * @return string
     */
    public function savePage(array $params = array(), AjaxRequestHandler $ajaxRequestHandler = null)
    {
        /** @var ServerRequest $request */
        $request = $params['request'];

        if ($request instanceof ServerRequest) {
            $pageData = $request->getParsedBody();

            if (is_array($pageData)) {
                if (
                    0 < $pageData['pageUid'] &&
                    !empty($pageData['title'])
                ) {
                    $this->savePageData($pageData);
                } else {
                    $ajaxRequestHandler->setError('Invalid Data');
                }
            }
        }

        return $ajaxRequestHandler->render();
    }

    /**
     * @param array $pageData
     * @return void
     */
    protected function savePageData(array $pageData)
    {
        /** @var DatabaseConnection $databaseConnection */
        $databaseConnection = $GLOBALS['TYPO3_DB'];

        $databaseConnection->exec_UPDATEquery(
            'pages',
            'uid = ' . $pageData['pageUid'],
            array(
                'title' => $pageData['title'],
                'description' => $pageData['description']
            )
        );
    }
}
