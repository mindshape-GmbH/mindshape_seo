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

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\QueryGenerator;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\TimeTracker\NullTimeTracker;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\Page\PageRepository;
/**
 * @package mindshape_seo
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class PageService implements SingletonInterface
{
    /**
     * @var \TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder
     */
    protected $uriBuilder;

    /**
     * @var \TYPO3\CMS\Frontend\Page\PageRepository
     */
    protected $pageRepository;

    /**
     * @return PageService
     * @throws \Mindshape\MindshapeSeo\Service\Exception
     */
    public function __construct()
    {
        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        /** @var ConfigurationManager $configurationManager */
        $configurationManager = $objectManager->get(ConfigurationManager::class);
        $this->pageRepository = $objectManager->get(PageRepository::class);

        if ('BE' === TYPO3_MODE) {
            if (!is_object($GLOBALS['TT'])) {
                $GLOBALS['TT'] = GeneralUtility::makeInstance(NullTimeTracker::class);
                $GLOBALS['TT']->start();
            }

            $GLOBALS['TSFE'] = $objectManager->get(
                TypoScriptFrontendController::class,
                $GLOBALS['TYPO3_CONF_VARS'],
                GeneralUtility::_GET('id'),
                GeneralUtility::_GET('type')
            );

            $GLOBALS['TSFE']->connectToDB();
            $GLOBALS['TSFE']->initFEuser();
            $GLOBALS['TSFE']->determineId();
            $GLOBALS['TSFE']->initTemplate();
            $GLOBALS['TSFE']->getConfigArray();

            if (ExtensionManagementUtility::isLoaded('realurl')) {
                $_SERVER['HTTP_HOST'] = BackendUtility::firstDomainRecord(
                    BackendUtility::BEgetRootLine(GeneralUtility::_GET('id'))
                );
            }
        } elseif ('FE' !== TYPO3_MODE) {
            throw new Exception('Illegal TYPO3_MODE');
        }

        $configurationManager->setContentObject(
            $objectManager->get(ContentObjectRenderer::class)
        );

        $this->uriBuilder = $objectManager->get(UriBuilder::class);
        $this->uriBuilder->injectConfigurationManager($configurationManager);
    }

    /**
     * Creates a link to a single page
     *
     * @param int $pageId
     * @param bool $absolute
     * @param int $sysLanguageUid
     * @return string
     */
    public function getPageLink($pageId, $absolute = false, $sysLanguageUid = 0)
    {
        return $this->uriBuilder
            ->reset()
            ->setTargetPageUid($pageId)
            ->setCreateAbsoluteUri($absolute)
            ->setArguments(
                0 < $sysLanguageUid ? array('L' => $sysLanguageUid) : array()
            )
            ->buildFrontendUri();
    }

    /**
     * @param int $pageUid
     * @param int $sysLanguageUid
     * @return array
     */
    public function getPage($pageUid, $sysLanguageUid = 0)
    {
        if (0 === $sysLanguageUid) {
            return $this->pageRepository->getPage($pageUid);
        } else {
            return $this->pageRepository->getPageOverlay($pageUid, $sysLanguageUid);
        }
    }

    /**
     * @return array
     */
    public function getCurrentPage()
    {
        return $this->getPage($GLOBALS['TSFE']->id);
    }

    /**
     * @param int $pageUid
     * @param int $sysLanguageUid
     * @return array
     */
    public function getPageMetaData($pageUid, $sysLanguageUid = 0)
    {
        $page = $this->getPage($pageUid, $sysLanguageUid);

        return array(
            'uid' => $page['uid'],
            'title' => $page['title'],
            'disableTitleAttachment' => (bool) $page['mindshapeseo_disable_title_attachment'],
            'canonicalUrl' => 0 < (int) $page['mindshapeseo_canonical'] ?
                $this->getPageLink(
                    (int) $page['mindshapeseo_canonical'],
                    true,
                    $GLOBALS['TSFE']->sys_language_uid
                ) :
                null,
            'meta' => array(
                'author' => $page['author'],
                'contact' => $page['author_email'],
                'description' => $page['description'],
                'robots' => array(
                    'noindex' => (bool) $page['mindshapeseo_no_index'],
                    'nofollow' => (bool) $page['mindshapeseo_no_follow'],
                ),
            ),
            'facebook' => array(
                'title' => $page['mindshapeseo_ogtitle'],
                'url' => $page['mindshapeseo_ogurl'],
                'description' => $page['mindshapeseo_ogdescription'],
            ),
        );
    }

    /**
     * @param int $pageUid
     * @param int $sysLanguageUid
     * @return array
     */
    public function getSubpagesMetaData($pageUid, $sysLanguageUid = 0)
    {
        $metadata = array();

        foreach ($this->getSubPageUidsFromPageUid($pageUid) as $subPageUid) {
            if ((int) $subPageUid !== $pageUid) {
                $metadata[] = $this->getPageMetaData($subPageUid, $sysLanguageUid);
            }
        }

        return $metadata;
    }

    /**
     * @return array
     */
    public function getRootline()
    {
        $pages = array();

        foreach ($GLOBALS['TSFE']->rootLine as $index => $page) {
            $pages[] = $this->getPage($page['uid']);
        }

        return $pages;
    }

    /**
     * @param int $pageUid
     * @return array
     */
    public function getSubPageUidsFromPageUid($pageUid)
    {
        /** @var QueryGenerator $queryGenerator */
        $queryGenerator = GeneralUtility::makeInstance(QueryGenerator::class);

        return GeneralUtility::trimExplode(
            ',',
            $queryGenerator->getTreeList($pageUid, 9999999, 0, 1)
        );
    }

    /**
     * @param int $pageUid
     * @return array
     */
    public function getSubPagesFromPageUid($pageUid)
    {
        $pages = array();

        foreach ($this->getSubPageUidsFromPageUid($pageUid) as $uid) {
            $pages[] = $this->pageRepository->getPage($uid);
        }

        return $pages;
    }
}
