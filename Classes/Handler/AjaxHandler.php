<?php

namespace Mindshape\MindshapeSeo\Handler;

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

use Doctrine\DBAL\ParameterType;
use Mindshape\MindshapeSeo\Domain\Repository\ConfigurationRepository;
use Mindshape\MindshapeSeo\Utility\DatabaseUtility;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

class AjaxHandler implements SingletonInterface
{
    public function __construct(
        protected ConfigurationRepository $configurationRepository,
        protected PersistenceManager $persistenceManager
    ) {
    }

    public function savePage(ServerRequestInterface $request): ResponseInterface
    {
        $data = json_decode($request->getBody()->getContents(), true);

        $response = ['saved' => false];
        $statusCode = 200;

        if (is_array($data)) {
            if (0 < $data['pageUid'] && !empty($data['title'])) {
                $this->savePageData(
                    (int)$data['pageUid'],
                    (int)($data['sysLanguageUid'] ?? 0),
                    [
                        'title' => $data['title'],
                        'seo_title' => $data['seoTitle'],
                        'description' => $data['description'] ?? '',
                        'mindshapeseo_focus_keyword' => $data['focusKeyword'] ?? '',
                        'no_index' => $data['noindex'] ? 1 : 0,
                        'no_follow' => $data['nofollow'] ? 1 : 0,
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
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    public function deleteConfiguration(ServerRequestInterface $request): ResponseInterface
    {
        $data = json_decode($request->getBody()->getContents(), true);

        $response = ['deleted' => false];
        $statusCode = 200;

        if (is_array($data)) {
            if (0 < (int)$data['configurationUid']) {
                $configuration = $this->configurationRepository->findByUid($data['configurationUid']);

                if ($configuration !== null) {
                    $this->configurationRepository->remove($configuration);
                    $this->persistenceManager->persistAll();

                    $response['deleted'] = true;
                } else {
                    $statusCode = 404;
                }
            } else {
                $statusCode = 500;
            }
        } else {
            $statusCode = 500;
        }

        return new JsonResponse($response, $statusCode);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
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
                    $queryBuilder->createNamedParameter($pageUid, ParameterType::INTEGER)
                ),
                $queryBuilder->expr()->eq(
                    'p.sys_language_uid',
                    $queryBuilder->createNamedParameter($sysLanguageUid, ParameterType::INTEGER)
                )
            )
            ->executeQuery();

        if (0 === $result->rowCount()) {
            $queryBuilder = DatabaseUtility::queryBuilder();

            $pageUid = $queryBuilder
                ->select('p.uid')
                ->from('pages', 'p')
                ->where(
                    $queryBuilder->expr()->eq(
                        'p.' . $GLOBALS['TCA']['pages']['ctrl']['transOrigPointerField'],
                        $queryBuilder->createNamedParameter($pageUid, ParameterType::INTEGER)
                    ),
                    $queryBuilder->expr()->eq(
                        'p.sys_language_uid',
                        $queryBuilder->createNamedParameter($sysLanguageUid, ParameterType::INTEGER)
                    )
                )
                ->executeQuery()
                ->fetchOne();
        }

        $queryBuilder = DatabaseUtility::queryBuilder();

        $queryBuilder
            ->update('pages')
            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($pageUid, ParameterType::INTEGER))
            );

        foreach ($data as $column => $value) {
            $queryBuilder->set($column, $value);
        }

        $queryBuilder->executeStatement();
    }
}
