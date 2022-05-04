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
use Mindshape\MindshapeSeo\Utility\PageUtility;
use TYPO3\CMS\Backend\FrontendBackendUserAuthentication;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\MetaTag\MetaTagManagerRegistry;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * @package mindshape_seo
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class HeaderDataService implements SingletonInterface
{
    /**
     * @var \TYPO3\CMS\Core\Page\PageRenderer
     */
    protected $pageRenderer;

    /**
     * @var \Mindshape\MindshapeSeo\Service\PageService
     */
    protected $pageService;

    /**
     * @var \Mindshape\MindshapeSeo\Service\StandaloneTemplateRendererService
     */
    protected $standaloneTemplateRendererService;

    /**
     * @var \Mindshape\MindshapeSeo\Domain\Repository\ConfigurationRepository
     */
    protected $configurationRepository;

    /**
     * @var \Mindshape\MindshapeSeo\Domain\Model\Configuration
     */
    protected $domainConfiguration;

    /**
     * @var array
     */
    protected $params = [];

    /**
     * @var array
     */
    protected $jsonLd = [];

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * @var array
     */
    protected $currentPageMetaData;

    /**
     * @var string
     */
    protected $currentDomainUrl;

    /**
     * @param \Mindshape\MindshapeSeo\Domain\Repository\ConfigurationRepository $configurationRepository
     * @param \Mindshape\MindshapeSeo\Service\PageService $pageService
     * @param \Mindshape\MindshapeSeo\Service\StandaloneTemplateRendererService $standaloneTemplateRendererService
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     * @throws \TYPO3\CMS\Extbase\Object\Exception
     */
    public function __construct(
        ConfigurationRepository           $configurationRepository,
        PageService                       $pageService,
        StandaloneTemplateRendererService $standaloneTemplateRendererService
    )
    {
        /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->pageService = $pageService;
        $this->standaloneTemplateRendererService = $standaloneTemplateRendererService;
        $this->configurationRepository = $configurationRepository;

        /** @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManager $configurationManager */
        $configurationManager = $objectManager->get(ConfigurationManager::class);

        $this->settings = $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS, 'mindshapeseo');

        $page = $this->pageService->getCurrentPage();

        $this->currentPageMetaData = $this->pageService->getPageMetaData(
            $page['uid'],
            $this->pageService->getCurrentSysLanguageUid()
        );

        $currentDomain = GeneralUtility::getIndpEnv('HTTP_HOST');

        $this->domainConfiguration = $this->configurationRepository->findByDomain($currentDomain, true);

        $this->currentDomainUrl = $this->pageService->getPageLink(
            $GLOBALS['TSFE']->rootLine[0]['uid'],
            true,
            $this->pageService->getCurrentSysLanguageUid()
        );

        if (
            $this->domainConfiguration instanceof Configuration &&
            true === $this->domainConfiguration->isMergeWithDefault()
        ) {
            $this->configurationRepository->mergeConfigurationWithDefault($this->domainConfiguration);
            $this->addJsonLd();
        }

        $this->pageRenderer = PageUtility::getPageRenderer();
    }

    /**
     * @return void
     */
    public function manipulateHeaderData()
    {
        $this->setRobotsMetaTag();

        if ($this->domainConfiguration instanceof Configuration) {
            $this->addTitle();

            if ($this->domainConfiguration->getAddJsonld()) {
                $this->renderJsonLd();
            }

            if ($this->domainConfiguration->getAddJsonldBreadcrumb()) {
                $this->addJsonLdBreadcrumb();
            }
        }
    }

    /**
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\InvalidExtensionNameException
     */
    public function getAnalyticsTags(): array
    {
        $data = [];
        if ($this->injectAnalyticsData()) {
            if ('' !== $this->domainConfiguration->getGoogleAnalytics()) {
                $data[] = $this->getGoogleAnalyticsTag();
            }

            if ('' !== $this->domainConfiguration->getGoogleAnalyticsV4()) {
                $data[] = $this->getGoogleAnalyticsV4Tag();
            }

            if ('' !== $this->domainConfiguration->getGoogleTagmanager()) {
                $data[] = $this->getGoogleTagmanagerTag();
            }

            if (
                '' !== $this->domainConfiguration->getMatomoUrl() &&
                '' !== $this->domainConfiguration->getMatomoIdsite()
            ) {
                $data[] = $this->getMatomoTag();
            }
        }

        return $data;
    }

    /**
     * @return bool
     */
    protected function injectAnalyticsData()
    {
        $analyticsDisabled = false;

        if (isset($this->settings['analytics']['disable'])) {
            $analyticsDisabled = (bool)$this->settings['analytics']['disable'];
        }

        if (
            $this->domainConfiguration instanceof Configuration &&
            $this->domainConfiguration->getAddAnalytics() &&
            !$analyticsDisabled
        ) {
            $disableOnBackendLogin = false;
            if (isset($this->settings['analytics']['disableOnBackendLogin'])) {
                $disableOnBackendLogin = (bool)$this->settings['analytics']['disableOnBackendLogin'];
            }

            $context = GeneralUtility::makeInstance(Context::class);
            try {
                $backendIsLoggedIn = $context->getPropertyFromAspect('backend.user', 'isLoggedIn');
            } catch (AspectNotFoundException $e) {
                $backendIsLoggedIn = false;
            }

            if (!$disableOnBackendLogin || !$backendIsLoggedIn) {
                return true;
            }
        }

        return false;
    }

    protected function addTitle()
    {
        $title = $this->pageRenderer->getTitle();

        if (true === empty($title)) {
            $title = $this->currentPageMetaData['title'];
        }

        if (
            $this->domainConfiguration instanceof Configuration &&
            !$this->currentPageMetaData['disableTitleAttachment'] &&
            !empty($this->domainConfiguration->getTitleAttachment())
        ) {
            if ($this->domainConfiguration->getTitleAttachmentPosition() === Configuration::TITLE_ATTACHMENT_POSITION_PREFIX) {
                $title = $this->domainConfiguration->getTitleAttachment() . ' ' . trim($this->domainConfiguration->getTitleAttachmentSeperator()) . ' ' . $title;
            } else {
                $title = $title . ' ' . trim($this->domainConfiguration->getTitleAttachmentSeperator()) . ' ' . $this->domainConfiguration->getTitleAttachment();
            }
        }

        $this->pageRenderer->setTitle($title);
    }

    /**
     * @return array
     */
    public function getJsonLd()
    {
        return $this->jsonLd;
    }

    /**
     * @param array $jsonLd
     * @return void
     */
    public function setJsonLd(array $jsonLd)
    {
        $this->jsonLd = $jsonLd;
    }

    /**
     * @param string $html
     * @return string
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\InvalidExtensionNameException
     */
    public function addGoogleTagmanagerBodyToHtml(string $html)
    {
        if ($this->injectAnalyticsData()) {
            $tagmanagerBody = $this->standaloneTemplateRendererService->render('Analytics', 'GoogleTagmanagerBody', [
                'tagmanagerId' => $this->domainConfiguration->getGoogleTagmanager(),
            ]);

            $tagmanagerBody = trim(preg_replace('/\\>\\s+\\</', '><', $tagmanagerBody));

            return preg_replace('/<body(.*?)>/', '<body$1>' . $tagmanagerBody, $html, 1);
        }

        return $html;
    }

    protected function setRobotsMetaTag()
    {
        $noindexInherited = (bool)$this->currentPageMetaData['meta']['robots']['noindexInherited'];
        $nofollowInherited = (bool)$this->currentPageMetaData['meta']['robots']['nofollowInherited'];

        if (
            true === $noindexInherited ||
            true === $nofollowInherited
        ) {
            $noindex = false;
            $nofollow = false;

            $robotsMetaTagManager = GeneralUtility::makeInstance(MetaTagManagerRegistry::class)->getManagerForProperty('robots');

            $originalRobotsMetaTagValue = $robotsMetaTagManager->getProperty('robots');

            if (0 < count($originalRobotsMetaTagValue)) {
                $originalRobotsMetaTagValue = GeneralUtility::trimExplode(',', $originalRobotsMetaTagValue[0]['content']);

                $noindex = true === in_array('noindex', $originalRobotsMetaTagValue, true);
                $nofollow = true === in_array('nofollow', $originalRobotsMetaTagValue, true);
            }

            if (true === $noindexInherited) {
                $noindex = true;
            }

            if (true === $nofollowInherited) {
                $nofollow = true;
            }

            $robotsMetaTagManager->addProperty(
                'robots',
                (true === $noindex ? 'noindex' : 'index') . ',' . (true === $nofollow ? 'nofollow' : 'follow'),
                [],
                true
            );
        }
    }

    /**
     * @return string
     */
    protected function getGoogleAnalyticsTag(): string
    {
        return $this->standaloneTemplateRendererService->render(
            'Analytics',
            true === $this->domainConfiguration->getGoogleAnalyticsUseCookieConsent()
                ? 'GoogleAnalyticsCookieConsent'
                : 'GoogleAnalytics',
            ['analyticsId' => $this->domainConfiguration->getGoogleAnalytics()]);
    }

    /**
     * @return string
     */
    protected function getGoogleAnalyticsV4Tag(): string
    {
        return $this->standaloneTemplateRendererService->render(
            'Analytics',
            true === $this->domainConfiguration->getGoogleAnalyticsV4UseCookieConsent()
                ? 'GoogleAnalyticsV4CookieConsent'
                : 'GoogleAnalyticsV4',
            ['analyticsId' => $this->domainConfiguration->getGoogleAnalyticsV4()]);
    }

    /**
     * @return string
     */
    protected function getGoogleTagmanagerTag(): string
    {
        return $this->standaloneTemplateRendererService->render(
            'Analytics',
            true === $this->domainConfiguration->getTagmanagerUseCookieConsent()
                ? 'GoogleTagmanagerHeadCookieConsent'
                : 'GoogleTagmanagerHead',
            [
                'tagmanagerId' => $this->domainConfiguration->getGoogleTagmanager(),
            ]
        );
    }

    /**
     * @return string
     */
    protected function getMatomoTag(): string
    {
        return $this->standaloneTemplateRendererService->render(
            'Analytics',
            true === $this->domainConfiguration->getMatomoUseCookieConsent()
                ? 'MatomoCookieConsent'
                : 'Matomo',
            [
                'matomoUrl' => $this->domainConfiguration->getMatomoUrl(),
                'matomoIdSite' => $this->domainConfiguration->getMatomoIdsite(),
            ]
        );
    }

    /**
     * @return void
     */
    protected function addJsonLd()
    {
        if ($this->domainConfiguration->getAddJsonld()) {
            $this->jsonLd[] = $this->renderJsonWebsiteName();
            $this->jsonLd[] = $this->renderJsonLdInformation();
        }
    }

    /**
     * @return void
     */
    protected function renderJsonLd()
    {
        if (
            true === array_key_exists('mindshape_seo', $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']) &&
            true === is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mindshape_seo']['jsonld_preRendering'] ?? null)
        ) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mindshape_seo']['jsonld_preRendering'] as $userFunc) {
                $params = ['jsonld' => &$this->jsonLd];

                GeneralUtility::callUserFunction($userFunc, $params, $this);
            }
        }

        if (0 < count($this->jsonLd)) {
            $this->pageRenderer->addHeaderData(
                '<script type="application/ld+json" data-ignore="1">' . json_encode($this->jsonLd) . '</script>'
            );
        }
    }

    /**
     * @return array
     */
    protected function renderJsonWebsiteName()
    {
        return [
            '@context' => 'http://schema.org',
            '@type' => 'WebSite',
            'url' => '' !== $this->domainConfiguration->getJsonldCustomUrl() ?
                $this->domainConfiguration->getJsonldCustomUrl() :
                GeneralUtility::getIndpEnv('TYPO3_REQUEST_HOST'),
        ];
    }

    /**
     * @return array
     */
    protected function renderJsonLdInformation()
    {
        $jsonld = [
            '@context' => 'http://schema.org',
            '@type' => $this->domainConfiguration->getJsonldType(),
            'url' => $this->currentDomainUrl,
        ];

        if (false === empty($this->domainConfiguration->getJsonldName())) {
            $jsonld['name'] = $this->domainConfiguration->getJsonldName();
        }

        if (false === empty($this->domainConfiguration->getJsonldTelephone())) {
            $jsonld['telephone'] = $this->domainConfiguration->getJsonldTelephone();
        }

        if (false === empty($this->domainConfiguration->getJsonldFax())) {
            $jsonld['faxNumber'] = $this->domainConfiguration->getJsonldFax();
        }

        if (false === empty($this->domainConfiguration->getJsonldEmail())) {
            $jsonld['email'] = $this->domainConfiguration->getJsonldEmail();
        }

        if (
            false === empty($this->domainConfiguration->getJsonldAddressLocality()) &&
            false === empty($this->domainConfiguration->getJsonldAddressPostalcode()) &&
            false === empty($this->domainConfiguration->getJsonldAddressStreet())
        ) {
            $jsonld['address'] = ['@type' => 'PostalAddress',];

            if (false === empty($this->domainConfiguration->getJsonldAddressLocality())) {
                $jsonld['address']['addressLocality'] = $this->domainConfiguration->getJsonldAddressLocality();
            }


            if (false === empty($this->domainConfiguration->getJsonldAddressPostalcode())) {
                $jsonld['address']['postalcode'] = $this->domainConfiguration->getJsonldAddressPostalcode();
            }


            if (false === empty($this->domainConfiguration->getJsonldAddressStreet())) {
                $jsonld['address']['streetAddress'] = $this->domainConfiguration->getJsonldAddressStreet();
            }
        }

        if (
            null !== $this->domainConfiguration->getJsonldLogo() &&
            Configuration::JSONLD_TYPE_PERSON !== $this->domainConfiguration->getJsonldType()
        ) {
            $jsonld['logo'] = GeneralUtility::getIndpEnv('TYPO3_SITE_URL') . ltrim($this->domainConfiguration
                    ->getJsonldLogo()
                    ->getOriginalResource()
                    ->getPublicUrl(), '/');
        }

        $socialMediaLinks = [
            'facebook' => $this->domainConfiguration->getJsonldSameAsFacebook(),
            'twitter' => $this->domainConfiguration->getJsonldSameAsTwitter(),
            'instagram' => $this->domainConfiguration->getJsonldSameAsInstagram(),
            'youtube' => $this->domainConfiguration->getJsonldSameAsYoutube(),
            'linkedin' => $this->domainConfiguration->getJsonldSameAsLinkedin(),
            'xing' => $this->domainConfiguration->getJsonldSameAsXing(),
            'printerest' => $this->domainConfiguration->getJsonldSameAsPrinterest(),
            'soundcloud' => $this->domainConfiguration->getJsonldSameAsSoundcloud(),
            'tumblr' => $this->domainConfiguration->getJsonldSameAsTumblr(),
        ];

        foreach ($socialMediaLinks as $socialMediaLink) {
            if (!empty($socialMediaLink)) {
                if (!is_array($jsonld['sameAs'])) {
                    $jsonld['sameAs'] = [];
                }

                $jsonld['sameAs'][] = $socialMediaLink;
            }
        }

        return $jsonld;
    }

    /**
     * @return void
     */
    protected function addJsonLdBreadcrumb()
    {
        $jsonLdbreadcrumb = $this->renderJsonLdBreadcrum();

        if (
            true === array_key_exists('mindshape_seo', $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']) &&
            true === is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mindshape_seo']['jsonldBreadcrumb_preRendering'] ?? null)
        ) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mindshape_seo']['jsonldBreadcrumb_preRendering'] as $userFunc) {
                $params = ['jsonldBreadcrumb' => &$jsonLdbreadcrumb];

                GeneralUtility::callUserFunction($userFunc, $params, $this);
            }
        }

        if (0 < count($jsonLdbreadcrumb['itemListElement'])) {
            $this->pageRenderer->addFooterData(
                '<script type="application/ld+json" data-ignore="1">' . json_encode($jsonLdbreadcrumb) . '</script>'
            );
        }
    }

    /**
     * @return array
     */
    protected function renderJsonLdBreadcrum()
    {
        $respectDoktypes = GeneralUtility::trimExplode(',', $this->settings['breadcrumb']['respectDoktypes']);
        $breadcrumb = [
            '@context' => 'http://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [],
        ];

        foreach ($this->pageService->getRootlineReverse(null, true) as $index => $page) {
            if (false === in_array($page['doktype'], $respectDoktypes)) {
                continue;
            }

            $breadcrumb['itemListElement'][] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'item' => [
                    '@id' => $this->pageService->getPageLink(
                        $page['uid'],
                        true,
                        $this->pageService->getCurrentSysLanguageUid()
                    ),
                    'name' => false === empty($page['mindshapeseo_jsonld_breadcrumb_title'])
                        ? $page['mindshapeseo_jsonld_breadcrumb_title']
                        : $page['title'],
                ],
            ];
        }

        return $breadcrumb;
    }
}
