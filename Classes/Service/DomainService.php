<?php
namespace Mindshape\MindshapeSeo\Service;

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

use Mindshape\MindshapeSeo\Domain\Model\Configuration;
use Mindshape\MindshapeSeo\Domain\Repository\ConfigurationRepository;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * @package mindshape_seo
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class DomainService implements SingletonInterface
{
    /**
     * @var \Mindshape\MindshapeSeo\Service\PageService
     */
    protected $pageService;

    /**
     * @var \TYPO3\CMS\Core\Site\SiteFinder
     */
    protected $siteFinder;

    /**
     * @var \Mindshape\MindshapeSeo\Domain\Repository\ConfigurationRepository
     */
    protected $configurationRepository;

    /**
     * @param \Mindshape\MindshapeSeo\Service\PageService $pageService
     * @return void
     */
    public function injectPageService(PageService $pageService): void
    {
        $this->pageService = $pageService;
    }

    /**
     * @param \TYPO3\CMS\Core\Site\SiteFinder $siteFinder
     */
    public function injectSiteFinder(SiteFinder $siteFinder): void
    {
        $this->siteFinder = $siteFinder;
    }

    /**
     * @param \Mindshape\MindshapeSeo\Domain\Repository\ConfigurationRepository $configurationRepository
     * @return void
     */
    public function injectConfigurationRepository(ConfigurationRepository $configurationRepository): void
    {
        $this->configurationRepository = $configurationRepository;
    }

    /**
     * @return array
     */
    public function getAvailableDomains()
    {
        $domains = ['*'];

        foreach ($this->siteFinder->getAllSites() as $site) {
            $domains[] = $site->getBase()->getHost();
        }

        return $domains;
    }

    /**
     * @param int $pageUid
     * @return \Mindshape\MindshapeSeo\Domain\Model\Configuration|null
     */
    public function getPageDomainConfiguration($pageUid = null)
    {
        if (null !== $pageUid) {
            try {
                $site = $this->siteFinder->getSiteByPageId($pageUid);
                $configuration = $this->configurationRepository->findByDomain($site->getBase()->getHost());

                if ($configuration instanceof Configuration) {
                    return $configuration;
                }
            } catch (SiteNotFoundException $exception) {
                // nothing
            }
        }

        return $this->configurationRepository->findByDomain(Configuration::DEFAULT_DOMAIN);
    }

    /**
     * @param string $currentDomain
     * @return array
     */
    public function getConfigurationDomainSelectOptions($currentDomain)
    {
        $domains = $this->getAvailableDomains();
        $domainSelectOptions = [];

        foreach ($domains as $domain) {
            if ($this->configurationRepository->findByDomain($domain) instanceof Configuration) {
                continue;
            }

            $domainSelectOptions[$domain] = $domain;
        }

        if (false === array_key_exists($currentDomain, $domainSelectOptions)) {
            $domainSelectOptions[$currentDomain] = $currentDomain;
        }

        $this->renameDomains($domainSelectOptions);

        return $domainSelectOptions;
    }

    /**
     * @param array $domains
     * @return void
     */
    protected function renameDomains(array &$domains)
    {
        foreach ($domains as &$domain) {
            $domain = Configuration::DEFAULT_DOMAIN === $domain
                ? LocalizationUtility::translate('tx_mindshapeseo_domain_model_configuration.domain.default', 'mindshape_seo')
                : $domain;
        }
    }
}
