<?php
namespace Mindshape\MindshapeSeo\Hook;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Daniel Dorndorf <dorndorf@mindshape.de>
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

use GeorgRinger\News\Domain\Repository\NewsRepository;
use Mindshape\MindshapeSeo\Domain\Model\SitemapNode;
use Mindshape\MindshapeSeo\Generator\SitemapGenerator;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * @package mindshape_seo
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class NewsSitemapHook extends SitemapHook
{
    /**
     * @var \GeorgRinger\News\Domain\Repository\NewsRepository
     */
    protected $newsRepository;

    /**
     * @var array
     */
    protected $settings;

    /**
     * @return \Mindshape\MindshapeSeo\Hook\NewsSitemapHook
     */
    public function __construct()
    {
        parent::__construct();

        /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->newsRepository = $objectManager->get(NewsRepository::class);

        /** @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManager $configurationManager */
        $configurationManager = $objectManager->get(ConfigurationManager::class);

        $this->settings = $configurationManager->getConfiguration(ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'mindshape_seo');
    }

    /**
     * @param array $params
     * @param \Mindshape\MindshapeSeo\Generator\SitemapGenerator $sitemapGenerator
     * @return void
     */
    public function sitemapPreRendering(array &$params, SitemapGenerator $sitemapGenerator)
    {
        $newsDetailPageUid = (int) $this->settings['sitemap']['newsDetailPage'];

        if (0 < $newsDetailPageUid) {
            $news = $this->newsRepository->findAll();

            /** @var \GeorgRinger\News\Domain\Model\News $newsItem */
            foreach ($news as $newsItem) {
                $sitemapNode = new SitemapNode();

                $newsUrl = $this->uriBuilder
                    ->reset()
                    ->setTargetPageUid($this->settings['sitemap']['newsDetailPage'])
                    ->setAbsoluteUriScheme(true)
                    ->uriFor(
                        'detail',
                        array('news' => $newsItem),
                        'News',
                        'news',
                        'Pi1'
                    );

                $lastModification = new \DateTime();
                $lastModification->setTimestamp($newsItem->getTstamp());

                $sitemapNode->setUrl($newsUrl);
                $sitemapNode->setPriority(SitemapNode::DEFAULT_PRIORITY);
                $sitemapNode->setChangeFrequency(SitemapNode::DEFAULT_CHANGE_FREQUENCY);
                $sitemapNode->setLastModification($lastModification);

                $params['nodes'][] = $sitemapNode;
            }
        }
    }
}
