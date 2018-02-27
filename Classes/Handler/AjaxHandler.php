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

use Facebook\Exceptions\FacebookAuthenticationException;
use Facebook\Exceptions\FacebookClientException;
use Facebook\Exceptions\FacebookOtherException;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Facebook\FacebookApp;
use Mindshape\MindshapeSeo\Domain\Repository\ConfigurationRepository;
use Mindshape\MindshapeSeo\Domain\Repository\RedirectRepository;
use Mindshape\MindshapeSeo\Utility\PageUtility;
use Mindshape\MindshapeSeo\Service\PageService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\Page\PageRepository;
use TYPO3\CMS\Frontend\Utility\EidUtility;

/**
 * @package mindshape_seo
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class AjaxHandler implements SingletonInterface
{

    /**
     * @param \Mindshape\MindshapeSeo\Domain\Repository\ConfigurationRepository $configurationRepository
     * @return void
     */
    public function injectConfigurationRepository(ConfigurationRepository $configurationRepository)
    {
        $this->configurationRepository = $configurationRepository;
    }
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
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function deleteRedirect(ServerRequestInterface $request, ResponseInterface $response)
    {
        /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        /** @var \Mindshape\MindshapeSeo\Domain\Repository\redirectRepository $redirectRepository */
        $redirectRepository = $objectManager->get(RedirectRepository::class);
        /** @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager $persistenceManager */
        $persistenceManager = $objectManager->get(PersistenceManager::class);

        $data = $request->getParsedBody();

        $responseArray = array(
            'deleted' => false,
        );

        if (is_array($data)) {
            if (0 < (int) $data['redirectUid']) {
                $redirect = $redirectRepository->findByUid($data['redirectUid']);

                $redirectRepository->remove($redirect);

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
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     */
    public function hideRedirect(ServerRequestInterface $request, ResponseInterface $response)
    {
        /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        /** @var \Mindshape\MindshapeSeo\Domain\Repository\redirectRepository $redirectRepository */
        $redirectRepository = $objectManager->get(RedirectRepository::class);
        /** @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager $persistenceManager */
        $persistenceManager = $objectManager->get(PersistenceManager::class);

        $data = $request->getParsedBody();

        $responseArray = array(
            'hidden' => false,
        );

        if (is_array($data)) {
            if (0 < (int) $data['redirectUid']) {
                $redirect = $redirectRepository->findByUid($data['redirectUid']);


                $redirect->setHidden(1);

                $redirectRepository->update($redirect);

                $persistenceManager->persistAll();

                $responseArray['hidden'] = true;

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
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     */
    public function unhideRedirect(ServerRequestInterface $request, ResponseInterface $response)
    {
        /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        /** @var \Mindshape\MindshapeSeo\Domain\Repository\redirectRepository $redirectRepository */
        $redirectRepository = $objectManager->get(RedirectRepository::class);
        /** @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager $persistenceManager */
        $persistenceManager = $objectManager->get(PersistenceManager::class);

        $data = $request->getParsedBody();

        $responseArray = array(
            'hidden' => false,
        );

        if (is_array($data)) {
            if (0 < (int) $data['redirectUid']) {
                $redirect = $redirectRepository->findByUid($data['redirectUid']);

                $redirect->setHidden(0);

                $redirectRepository->update($redirect);

                $persistenceManager->persistAll();

                $responseArray['hidden'] = true;

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
     */
    public function facebookScrape(ServerRequestInterface $request, ResponseInterface $response, $currentSysLanguageUid = 0)
    {
        /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        /** @var \Mindshape\MindshapeSeo\Domain\Repository\ConfigurationRepository $configurationRepository */
        $configurationRepository = $objectManager->get(ConfigurationRepository::class);

        $configuration = $configurationRepository->findByDomain($_SERVER['HTTP_HOST']);

        /** @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManager $configurationManager */
        $configurationManager = $objectManager->get(ConfigurationManager::class);

        $data = $request->getParsedBody();

        $data['pageUid'] = (int)$data['pageUid'];

        //$pageService = $objectManager->get(PageService::class);

        //$pageLink = $pageService->getPageLink($data['pageUid'], true);

        $feUserObj 	= EidUtility::initFeUser();
        $pageId 	= 1;
        $typoScriptFrontendController = GeneralUtility::makeInstance(
            TypoScriptFrontendController::class,
            $GLOBALS['TYPO3_CONF_VARS'],
            $pageId,
            0,
            true
        );

        $GLOBALS['TSFE'] = $typoScriptFrontendController;

        $typoScriptFrontendController->connectToDB();
        $typoScriptFrontendController->fe_user = $feUserObj;
        $typoScriptFrontendController->id = $pageId;
        $typoScriptFrontendController->determineId();
        $typoScriptFrontendController->initTemplate();
        $typoScriptFrontendController->getConfigArray();

        if( \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded( 'realurl' ) )
        {
            $rootline 	= \TYPO3\CMS\Backend\Utility\BackendUtility::BEgetRootLine( $pageId );
            $host 		= \TYPO3\CMS\Backend\Utility\BackendUtility::firstDomainRecord( $rootline );

            $_SERVER['HTTP_HOST'] = $host;
        }

        EidUtility::initTCA();

        $configurationManager->setContentObject(
            $objectManager->get(ContentObjectRenderer::class)
        );

        $uriBuilder = $objectManager->get(UriBuilder::class);
        $uriBuilder->injectConfigurationManager($configurationManager);

        $url = $uriBuilder
            ->reset()
            ->setTargetPageUid($data['pageUid'])
            ->setCreateAbsoluteUri(true)
            ->setArguments(array('L' => 0))
            ->buildFrontendUri();

        $facebookApp = new FacebookApp(
            $configuration->getFbAppId(),
            $configuration->getFbAppKey()
        );

        $facebookSdk = new Facebook([
            'app_id' => $configuration->getFbAppId(),
            'app_secret' => $configuration->getFbAppKey(),
            'default_graph_version' => 'v2.12',
        ]);

        $facebookSdk->setDefaultAccessToken($facebookApp->getAccessToken());

        $responseArray = [
            'scraped' => false,
        ];

        try {
            $apiResponse = $facebookSdk->post(
                '/',
                [
                    'id' => $url,
                    'scrape' => 'true',
                ]

            );
            if (200 === $apiResponse->getHttpStatusCode()) {
                $responseArray['scraped'] = true;
                $responseArray['msg'] = 'Scraped successfully';
            } else {
                $responseArray['scraped'] = false;
                $responseArray['msg'] = 'Error';
            }

        } catch (FacebookResponseException $e) {
            $error = 'Graph returned an error: ' . $e->getMessage();
            $responseArray['msg'] = $error;

        } catch (FacebookSDKException $e) {
            $error = 'Facebook SDK returned an error: ' . $e->getMessage();
            $responseArray['msg'] = $error;

        } catch (\Exception $e) {
            $error = 'Facebook returned an error: ' . $e->getMessage();
            $responseArray['msg'] = $error;
        }


        $response->getBody()->write(json_encode($responseArray));

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
