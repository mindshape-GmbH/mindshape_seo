<?php

namespace Mindshape\MindshapeSeo\Handler;

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

use Mindshape\MindshapeSeo\Domain\Repository\ConfigurationRepository;
use Mindshape\MindshapeSeo\Utility\DatabaseUtility;
use Mindshape\MindshapeSeo\Utility\PageUtility;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\JsonResponse;
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
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \TYPO3\CMS\Extbase\Object\Exception
     */
    public function savePage(ServerRequestInterface $request): ResponseInterface
    {
        $data = $request->getParsedBody();

        $response = ['saved' => false];
        $statusCode = 200;

        if (is_array($data)) {
            if (0 < $data['pageUid'] && !empty($data['title'])) {
                $page = PageUtility::getPage((int) $data['pageUid']);

                $titleField = 'title';

                if (false === empty($page['seo_title'])) {
                    $titleField = 'seo_title';
                }

                $this->savePageData(
                    (int) $data['pageUid'],
                    (int) ($data['sysLanguageUid'] ?? 0),
                    [
                        $titleField => $data['title'],
                        'description' => $data['description'] ?? '',
                        'mindshapeseo_focus_keyword' => $data['focusKeyword'] ?? '',
                        'no_index' => (bool) $data['noindex'] ? 1 : 0,
                        'no_follow' => (bool) $data['nofollow'] ? 1 : 0,
                    ]
                );

                $response['saved'] = true;
            } else {
                $statusCode = 500;
            }
        } else {
            $statusCode = 500;
        }

        return new JsonResponse($response, $statusCode);
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \TYPO3\CMS\Extbase\Object\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    public function deleteConfiguration(ServerRequestInterface $request): ResponseInterface
    {
        /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        /** @var \Mindshape\MindshapeSeo\Domain\Repository\ConfigurationRepository $configurationRepository */
        $configurationRepository = $objectManager->get(ConfigurationRepository::class);
        /** @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager $persistenceManager */
        $persistenceManager = $objectManager->get(PersistenceManager::class);

        $data = $request->getParsedBody();

        $response = ['deleted' => false];
        $statusCode = 200;

        if (is_array($data)) {
            if (0 < (int) $data['configurationUid']) {
                $configuration = $configurationRepository->findByUid($data['configurationUid']);
                $configurationRepository->remove($configuration);
                $persistenceManager->persistAll();

                $response['deleted'] = true;
            } else {
                $statusCode = 500;
            }
        } else {
            $statusCode = 500;
        }

        return new JsonResponse($response, $statusCode);
    }

    /**
     * @param int $pageUid
     * @param int $sysLanguageUid
     * @param array $data
     */
    protected function savePageData(int $pageUid, int $sysLanguageUid, array $data): void
    {
        $queryBuilder = DatabaseUtility::queryBuilder();

        $result = $queryBuilder
            ->select('p.uid')
            ->from('pages', 'p')
            ->where(
                $queryBuilder->expr()->eq(
                    'p.uid',
                    $queryBuilder->createNamedParameter($pageUid, PDO::PARAM_INT)
                ),
                $queryBuilder->expr()->eq('p.sys_language_uid', $queryBuilder->createNamedParameter(
                    $sysLanguageUid,
                    PDO::PARAM_INT)
                )
            )
            ->execute();

        if (0 === $result->rowCount()) {
            $queryBuilder = DatabaseUtility::queryBuilder();

            $page = $queryBuilder
                ->select('p.uid')
                ->from('pages', 'p')
                ->where(
                    $queryBuilder->expr()->eq(
                        'p.' . $GLOBALS['TCA']['pages']['ctrl']['transOrigPointerField'],
                        $queryBuilder->createNamedParameter($pageUid, PDO::PARAM_INT)),
                    $queryBuilder->expr()->eq('p.sys_language_uid', $queryBuilder->createNamedParameter(
                        $sysLanguageUid,
                        PDO::PARAM_INT)
                    )
                )
                ->execute()
                ->fetch();

            $pageUid = $page['uid'];
        }

        $queryBuilder = DatabaseUtility::queryBuilder();

        $queryBuilder
            ->update('pages')
            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($pageUid, PDO::PARAM_INT))
            );

        foreach ($data as $column => $value) {
            $queryBuilder->set($column, $value);
        }

        $queryBuilder->execute();
    }
}
