<?php
namespace Mindshape\MindshapeSeo\Generator;

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

use Mindshape\MindshapeSeo\Domain\Model\Configuration;
use Mindshape\MindshapeSeo\Domain\Model\SitemapImageNode;
use Mindshape\MindshapeSeo\Domain\Repository\ConfigurationRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * @package mindshape_seo
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class ImageSitemapGenerator extends SitemapGenerator
{
    const TAG_IMAGE = 'image:image';
    const TAG_IMAGE_LOC = 'image:loc';

    /**
     * @var \TYPO3\CMS\Core\Resource\ResourceFactory
     * @inject
     */
    protected $resourceFactory;

    /**
     * @var \Mindshape\MindshapeSeo\Domain\Model\Configuration
     */
    protected $configuration;

    /**
     * @var array
     */
    protected $settings;

    /**
     * @return \Mindshape\MindshapeSeo\Generator\ImageSitemapGenerator
     */
    public function __construct()
    {
        parent::__construct();
        /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        /** @var \Mindshape\MindshapeSeo\Domain\Repository\ConfigurationRepository $configurationRepository */
        $configurationRepository = $objectManager->get(ConfigurationRepository::class);
        /** @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManager $configurationManager */
        $configurationManager = $objectManager->get(ConfigurationManager::class);

        $this->configuration = $configurationRepository->findByDomain(GeneralUtility::getIndpEnv('HTTP_HOST'), true);
        $this->settings = $configurationManager->getConfiguration(ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'mindshape_seo');
    }

    /**
     * @return string
     */
    public function generateImageSitemapXml()
    {
        $this->getImageUrls();

        $imageSitemap = $this->getUrlsStartTag() . $this->getRenderedImageUrls() . $this->getUrlsEndTag();

        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mindshape_seo']['sitemapImage_postRendering'])) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mindshape_seo']['sitemapImage_postRendering'] as $userFunc) {
                $params = array('sitemap' => &$imageSitemap);

                GeneralUtility::callUserFunction($userFunc, $params, $this);
            }
        }

        return $imageSitemap;
    }

    /**
     * Creates start tags for this sitemap.
     *
     * @return string
     */
    protected function getUrlsStartTag()
    {
        return '<?xml version="1.0" encoding="UTF-8"?>' .
               '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" image:schemaLocation="http://www.google.com/schemas/sitemap-image/1.1/sitemap-image.xsd">';
    }

    /**
     * @return void
     */
    protected function getImageUrls()
    {
        $pages = $this->pageService->getSubPagesFromPageUid(
            $GLOBALS['TSFE']->page['uid']
        );

        foreach ($pages as $page) {
            $parentsProperties = $this->getParentProperties();

            if (
                0 !== (int) $parentsProperties['fe_group'] ||
                0 !== (int) $page['fe_group'] ||
                true === (bool) $page['hidden'] ||
                true === (bool) $page['mindshapeseo_no_index'] ||
                true === (bool) $page['mindshapeseo_exclude_from_sitemap']
            ) {
                continue;
            }

            $sitemapImageNode = new SitemapImageNode();

            $imagePaths = $this->getPageImageUrls($page['uid']);

            if (false === empty($imagePaths)) {
                $sitemapImageNode->setPageUrl(
                    $this->pageService->getPageLink(
                        $page['uid'],
                        true,
                        $GLOBALS['TSFE']->sys_language_uid
                    )
                );

                $sitemapImageNode->setImages($imagePaths);

                $this->nodes[] = $sitemapImageNode;
            }
        }
    }

    /**
     * @param int $pageUid
     * @return array
     */
    protected function getPageImageUrls($pageUid)
    {
        /** @var \TYPO3\CMS\Core\Database\DatabaseConnection $databaseConnection */
        $databaseConnection = $GLOBALS['TYPO3_DB'];

        $imageUrls = array();

        $tables = $this->settings['sitemap']['imageSitemap']['tables'];

        $fileReferences = array();

        foreach ($tables as $table => $fields) {
            $fieldsArray = GeneralUtility::trimExplode(',', $fields);
            $fieldsQuery = '';

            if (1 < count($fieldsArray)) {

                foreach ($fieldsArray as $key => $field) {
                    $fieldsQuery .= PHP_EOL . 'OR sys_file_reference.fieldname = "' . $field . '"';
                }
            }

            $fileReferences += $databaseConnection->exec_SELECTgetRows(
                'sys_file_reference.*',
                'sys_file_reference, ' . $table,
                'sys_file_reference.tablenames = "' . $table . '"' . PHP_EOL .
                'AND (sys_file_reference.fieldname = "' . $fields[0] . '"' . (
                    false === empty($fieldsQuery)
                        ? $fieldsQuery
                        : ''
                ) . ')' . PHP_EOL .
                'AND sys_file_reference.uid_foreign = ' . $table . '.uid' . PHP_EOL .
                'AND ' . $table . '.pid = ' . $pageUid
            );
        }

        foreach ($fileReferences as $row) {
            $imageUrl = urldecode($this->resourceFactory->getFileReferenceObject($row['uid'], $row)->getPublicUrl());

            if (!$this->configuration instanceof Configuration) {
                $imageUrls[] = $imageUrl;
            } elseif (
                0 === $this->configuration->getImageSitemapMinHeight() &&
                0 === $this->configuration->getImageSitemapMinWidth()
            ) {
                $imageSize = getimagesize($imageUrl);

                if (
                    $imageSize[1] >= $this->configuration->getImageSitemapMinHeight() &&
                    $imageSize[0] >= $this->configuration->getImageSitemapMinWidth()
                ) {
                    $imageUrls[] = $imageUrl;
                }
            }
        }

        return array_unique($imageUrls);
    }

    /**
     * @return string
     */
    protected function getRenderedImageUrls()
    {
        $urls = '';

        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mindshape_seo']['sitemapImage_preRendering'])) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mindshape_seo']['sitemapImage_preRendering'] as $userFunc) {
                $params = array('nodes' => &$this->nodes);

                GeneralUtility::callUserFunction($userFunc, $params, $this);
            }
        }

        /** @var \Mindshape\MindshapeSeo\Domain\Model\SitemapImageNode $node */
        foreach ($this->nodes as $node) {
            if ($node instanceof SitemapImageNode) {
                $urls .= $this->renderPageImagesEntry(
                    $node->getPageUrl(),
                    $node->getImages()
                );
            }
        }

        return $urls;
    }

    /**
     * @param int $pageUrl
     * @param array $pageImages
     * @return string
     */
    protected function renderPageImagesEntry($pageUrl, array $pageImages)
    {
        $content = '<loc>' . $pageUrl . '</loc>';

        foreach ($pageImages as $pageImageUrl) {
            $content .= '<' . self::TAG_IMAGE . '>' .
                '<' . self::TAG_IMAGE_LOC . '>' .
                GeneralUtility::getIndpEnv('TYPO3_REQUEST_HOST') . '/' . $pageImageUrl .
                '</' . self::TAG_IMAGE_LOC . '>' .
                '</' . self::TAG_IMAGE . '>';
        }

        return '<' . self::TAG_URL . '>' . $content . '</' . self::TAG_URL . '>';
    }
}
