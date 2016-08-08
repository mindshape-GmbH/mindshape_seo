<?php
namespace Mindshape\MindshapeSeo\Generator;

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

use Mindshape\MindshapeSeo\Service\PageService;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
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
     * @return string
     */
    public function generateImageSitemapXml() {
        return $this->getStartTags() . $this->getImageUrls() . $this->getEndTags();
    }

    /**
     * Creates start tags for this sitemap.
     *
     * @return string
     */
    protected function getStartTags()
    {
        return '<?xml version="1.0" encoding="UTF-8"?>' .
        '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" image:schemaLocation="http://www.google.com/schemas/sitemap-image/1.1/sitemap-image.xsd">';
    }

    /**
     * @return string
     */
    protected function getImageUrls()
    {
        $urls = '';

        $pages = $this->pageService->getSubPagesFromPageUid(
            $GLOBALS['TSFE']->page['uid']
        );

        foreach ($pages as $page) {
            $pageImages = $this->getPageImages($page['uid']);

            if (0 < count($pageImages)) {
                $urls .= $this->renderPageImagesEntry(
                    $this->pageService->getPageLink($page['uid'], true),
                    $pageImages
                );
            }
        }

        return $urls;
    }

    /**
     * @param int $pageUid
     * @return array
     */
    protected function getPageImages($pageUid)
    {
        /** @var DatabaseConnection $databaseConnection */
        $databaseConnection = $GLOBALS['TYPO3_DB'];

        $images = array();

        $rows = $databaseConnection->exec_SELECTgetRows(
            'sys_file_reference.*',
            'sys_file_reference, tt_content, pages',
            'sys_file_reference.tablenames = "tt_content" ' .
            'AND (sys_file_reference.fieldname = "image" ' .
            'OR sys_file_reference.fieldname = "media")' .
            'AND sys_file_reference.uid_foreign = tt_content.uid ' .
            'AND tt_content.pid = pages.uid AND pages.uid = ' . $pageUid
        );

        foreach ($rows as $row) {
            $images[] = $this->resourceFactory->getFileReferenceObject($row['uid'], $row);
        }

        return $images;
    }

    /**
     * @param int $pageUrl
     * @param array $pageImages
     * @return string
     */
    protected function renderPageImagesEntry($pageUrl, array $pageImages)
    {
        $content = '<loc>' . $pageUrl . '</loc>';

        /** @var FileReference $pageImage */
        foreach ($pageImages as $pageImage) {
            $content .= '<' . self::TAG_IMAGE . '>' .
                            '<' . self::TAG_IMAGE_LOC . '>' .
                                GeneralUtility::getIndpEnv('TYPO3_REQUEST_HOST') . '/' . $pageImage->getOriginalFile()->getPublicUrl() .
                            '</' . self::TAG_IMAGE_LOC . '>' .
                        '</'. self::TAG_IMAGE . '>';
        }

        return '<' . self::TAG_URL . '>' . $content . '</' . self::TAG_URL . '>';
    }
}
