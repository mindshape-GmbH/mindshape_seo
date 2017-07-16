<?php

namespace Mindshape\MindshapeSeo\Handler;

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

use Mindshape\MindshapeSeo\Domain\Repository\ConfigurationRepository;
use Mindshape\MindshapeSeo\Utility\PageUtility;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

/**
 * @package mindshape_seo
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class AjaxHandler implements SingletonInterface
{
    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function savePage(ServerRequestInterface $request, ResponseInterface $response)
    {
        $data = $request->getParsedBody();

        $responseArray = array(
            'saved' => false,
        );

        if (is_array($data)) {
            if (
                0 < $data['pageUid'] &&
                !empty($data['title'])
            ) {
                $page = PageUtility::getPage((int) $data['pageUid']);

                $titleField = 'title';

                if (false === empty($page['mindshapeseo_alternative_title'])) {
                    $titleField = 'mindshapeseo_alternative_title';
                }

                $this->savePageData(
                    (int) $data['pageUid'],
                    (int) $data['sysLanguageUid'],
                    array(
                        $titleField => $data['title'],
                        'description' => $data['description'],
                        'mindshapeseo_focus_keyword' => $data['focusKeyword'],
                        'mindshapeseo_no_index' => (bool) $data['noindex'] ? 1 : 0,
                        'mindshapeseo_no_follow' => (bool) $data['nofollow'] ? 1 : 0,
                    )
                );

                $responseArray['saved'] = true;

                $response->getBody()->write(json_encode($responseArray));
            } else {
                $response
                    ->withStatus(500, ' Invalid Data');
                $response->getBody()->write(json_encode($responseArray));
            }
        }

        return $response;
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function deleteConfiguration(ServerRequestInterface $request, ResponseInterface $response)
    {
        /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        /** @var \Mindshape\MindshapeSeo\Domain\Repository\ConfigurationRepository $configurationRepository */
        $configurationRepository = $objectManager->get(ConfigurationRepository::class);
        /** @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager $persistenceManager */
        $persistenceManager = $objectManager->get(PersistenceManager::class);

        $data = $request->getParsedBody();

        $responseArray = array(
            'deleted' => false,
        );

        if (is_array($data)) {
            if (0 < (int) $data['configurationUid']) {
                $configuration = $configurationRepository->findByUid($data['configurationUid']);

                $configurationRepository->remove($configuration);

                $persistenceManager->persistAll();

                $responseArray['deleted'] = true;

                $response->getBody()->write(json_encode($responseArray));
            } else {
                $response
                    ->withStatus(500, ' Invalid Data');
                $response->getBody()->write(json_encode($responseArray));
            }
        }

        return $response;
    }

    /**
     * @param int $pageUid
     * @param int $sysLanguageUid
     * @param array $data
     */
    protected function savePageData($pageUid, $sysLanguageUid = 0, array $data)
    {
        /** @var \TYPO3\CMS\Core\Database\DatabaseConnection $databaseConnection */
        $databaseConnection = $GLOBALS['TYPO3_DB'];

        if (0 < $sysLanguageUid) {
            $pageOverlay = $databaseConnection->exec_SELECTgetSingleRow(
                'p.*',
                'pages_language_overlay p',
                'pid = ' . $pageUid . ' AND sys_language_uid = ' . $sysLanguageUid
            );

            $pageUid = (int) $pageOverlay['uid'];
        }

        $databaseConnection->exec_UPDATEquery(
            0 < $sysLanguageUid
                ? 'pages_language_overlay'
                : 'pages',
            'uid = ' . $pageUid,
            $data
        );
    }
}
