<?php

namespace Mindshape\MindshapeSeo\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2023 Daniel Dorndorf <dorndorf@mindshape.de>, mindshape GmbH
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

use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * @package mindshape_seo
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Configuration extends AbstractEntity
{
    public const TABLE = 'tx_mindshapeseo_domain_model_configuration';

    public const DEFAULT_DOMAIN = '*';
    public const DEFAULT_TITLE_ATTACHMENT_SEPERATOR = '|';

    public const JSONLD_TYPE_ORGANIZATION = 'Organization';
    public const JSONLD_TYPE_PERSON = 'Person';

    public const TITLE_ATTACHMENT_POSITION_PREFIX = 'prefix';
    public const TITLE_ATTACHMENT_POSITION_SUFFIX = 'suffix';

    /**
     * @var string
     */
    protected string $domain = '';

    /**
     * @var bool
     */
    protected bool $mergeWithDefault = true;

    /**
     * @var string
     */
    protected string $googleAnalytics = '';

    /**
     * @var string
     */
    protected string $googleAnalyticsV4 = '';

    /**
     * @var string
     */
    protected string $googleTagmanager = '';

    /**
     * @var string
     */
    protected string $titleAttachment = '';

    /**
     * @var string
     */
    protected string $titleAttachmentSeperator = '';

    /**
     * @var string
     */
    protected string $titleAttachmentPosition = '';

    /**
     * @var bool
     */
    protected bool $addAnalytics = false;

    /**
     * @var bool
     */
    protected bool $googleAnalyticsUseCookieConsent = false;

    /**
     * @var bool
     */
    protected bool $googleAnalyticsV4UseCookieConsent = false;

    /**
     * @var bool
     */
    protected bool $tagmanagerUseCookieConsent = false;

    /**
     * @var bool
     */
    protected bool $matomoUseCookieConsent = false;

    /**
     * @var bool
     */
    protected bool $addJsonld = false;

    /**
     * @var bool
     */
    protected bool $addJsonldBreadcrumb = false;

    /**
     * @var string
     */
    protected string $jsonldCustomUrl = '';

    /**
     * @var string
     */
    protected string $jsonldType = '';

    /**
     * @var string
     */
    protected string $jsonldName = '';

    /**
     * @var string
     */
    protected string $jsonldTelephone = '';

    /**
     * @var string
     */
    protected string $jsonldFax = '';

    /**
     * @var string
     */
    protected string $jsonldEmail = '';

    /**
     * @var string
     */
    protected string $jsonldSameAsFacebook = '';

    /**
     * @var string
     */
    protected string $jsonldSameAsTwitter = '';

    /**
     * @var string
     */
    protected string $jsonldSameAsInstagram = '';

    /**
     * @var string
     */
    protected string $jsonldSameAsYoutube = '';

    /**
     * @var string
     */
    protected string $jsonldSameAsLinkedin = '';

    /**
     * @var string
     */
    protected string $jsonldSameAsXing = '';

    /**
     * @var string
     */
    protected string $jsonldSameAsPrinterest = '';

    /**
     * @var string
     */
    protected string $jsonldSameAsSoundcloud = '';

    /**
     * @var string
     */
    protected string $jsonldSameAsTumblr = '';

    /**
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference|null
     */
    protected ?FileReference $jsonldLogo;

    /**
     * @var string
     */
    protected string $jsonldAddressLocality = '';

    /**
     * @var string
     */
    protected string $jsonldAddressPostalcode = '';

    /**
     * @var string
     */
    protected string $jsonldAddressStreet = '';

    /**
     * @var string
     */
    protected string $matomoUrl = '';

    /**
     * @var string
     */
    protected string $matomoIdsite = '';

    /**
     * @return string $domain
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     */
    public function setDomain(string $domain): void
    {
        $this->domain = $domain;
    }

    /**
     * @return bool
     */
    public function isMergeWithDefault(): bool
    {
        return $this->mergeWithDefault;
    }

    /**
     * @param bool $mergeWithDefault
     */
    public function setMergeWithDefault(bool $mergeWithDefault): void
    {
        $this->mergeWithDefault = $mergeWithDefault;
    }

    /**
     * @return string $googleAnalytics
     */
    public function getGoogleAnalytics(): string
    {
        return $this->googleAnalytics;
    }

    /**
     * @param string $googleAnalytics
     */
    public function setGoogleAnalytics(string $googleAnalytics): void
    {
        $this->googleAnalytics = $googleAnalytics;
    }

    /**
     * @return string
     */
    public function getGoogleAnalyticsV4(): string
    {
        return $this->googleAnalyticsV4;
    }

    /**
     * @param string $googleAnalyticsV4
     */
    public function setGoogleAnalyticsV4(string $googleAnalyticsV4): void
    {
        $this->googleAnalyticsV4 = $googleAnalyticsV4;
    }

    /**
     * @return string
     */
    public function getGoogleTagmanager(): string
    {
        return $this->googleTagmanager;
    }

    /**
     * @param string $googleTagmanager
     */
    public function setGoogleTagmanager(string $googleTagmanager): void
    {
        $this->googleTagmanager = $googleTagmanager;
    }

    /**
     * @return string $titleAttachment
     */
    public function getTitleAttachment(): string
    {
        return $this->titleAttachment;
    }

    /**
     * @param string $titleAttachment
     */
    public function setTitleAttachment(string $titleAttachment): void
    {
        $this->titleAttachment = $titleAttachment;
    }

    /**
     * @return string $titleAttachmentSeperator
     */
    public function getTitleAttachmentSeperator(): string
    {
        return $this->titleAttachmentSeperator;
    }

    /**
     * @param string $titleAttachmentSeperator
     */
    public function setTitleAttachmentSeperator(string $titleAttachmentSeperator): void
    {
        $this->titleAttachmentSeperator = $titleAttachmentSeperator;
    }

    /**
     * @return string $titleAttachmentPosition
     */
    public function getTitleAttachmentPosition(): string
    {
        return $this->titleAttachmentPosition;
    }

    /**
     * @param string $titleAttachmentPosition
     */
    public function setTitleAttachmentPosition(string $titleAttachmentPosition): void
    {
        $this->titleAttachmentPosition = $titleAttachmentPosition;
    }

    /**
     * @return bool $addAnalytics
     */
    public function getAddAnalytics(): bool
    {
        return $this->addAnalytics;
    }

    /**
     * @param bool $addAnalytics
     */
    public function setAddAnalytics(bool $addAnalytics): void
    {
        $this->addAnalytics = $addAnalytics;
    }

    /**
     * @return bool
     */
    public function isAddAnalytics(): bool
    {
        return $this->addAnalytics;
    }

    /**
     * @return bool
     */
    public function getGoogleAnalyticsUseCookieConsent(): bool
    {
        return $this->googleAnalyticsUseCookieConsent;
    }

    /**
     * @param bool $googleAnalyticsUseCookieConsent
     */
    public function setGoogleAnalyticsUseCookieConsent(bool $googleAnalyticsUseCookieConsent): void
    {
        $this->googleAnalyticsUseCookieConsent = $googleAnalyticsUseCookieConsent;
    }

    /**
     * @return bool
     */
    public function getGoogleAnalyticsV4UseCookieConsent(): bool
    {
        return $this->googleAnalyticsV4UseCookieConsent;
    }

    /**
     * @param bool $googleAnalyticsV4UseCookieConsent
     */
    public function setGoogleAnalyticsV4UseCookieConsent(bool $googleAnalyticsV4UseCookieConsent): void
    {
        $this->googleAnalyticsV4UseCookieConsent = $googleAnalyticsV4UseCookieConsent;
    }

    /**
     * @return bool
     */
    public function getTagmanagerUseCookieConsent(): bool
    {
        return $this->tagmanagerUseCookieConsent;
    }

    /**
     * @param bool $tagmanagerUseCookieConsent
     */
    public function setTagmanagerUseCookieConsent(bool $tagmanagerUseCookieConsent): void
    {
        $this->tagmanagerUseCookieConsent = $tagmanagerUseCookieConsent;
    }

    /**
     * @return bool
     */
    public function getMatomoUseCookieConsent(): bool
    {
        return $this->matomoUseCookieConsent;
    }

    /**
     * @param bool $matomoUseCookieConsent
     */
    public function setMatomoUseCookieConsent(bool $matomoUseCookieConsent): void
    {
        $this->matomoUseCookieConsent = $matomoUseCookieConsent;
    }

    /**
     * @return bool $addJsonld
     */
    public function getAddJsonld(): bool
    {
        return $this->addJsonld;
    }

    /**
     * @param bool $addJsonld
     */
    public function setAddJsonld(bool $addJsonld): void
    {
        $this->addJsonld = $addJsonld;
    }

    /**
     * @return bool
     */
    public function isAddJsonld(): bool
    {
        return $this->addJsonld;
    }

    /**
     * @return bool $addJsonldBreadcrumb
     */
    public function getAddJsonldBreadcrumb(): bool
    {
        return $this->addJsonldBreadcrumb;
    }

    /**
     * @param bool $addJsonldBreadcrumb
     */
    public function setAddJsonldBreadcrumb(bool $addJsonldBreadcrumb): void
    {
        $this->addJsonldBreadcrumb = $addJsonldBreadcrumb;
    }

    /**
     * @return bool
     */
    public function isAddJsonldBreadcrumb(): bool
    {
        return $this->addJsonldBreadcrumb;
    }

    /**
     * @return string $jsonldCustomUrl
     */
    public function getJsonldCustomUrl(): string
    {
        return $this->jsonldCustomUrl;
    }

    /**
     * @param string $jsonldCustomUrl
     */
    public function setJsonldCustomUrl(string $jsonldCustomUrl): void
    {
        $this->jsonldCustomUrl = $jsonldCustomUrl;
    }

    /**
     * @return string $jsonldType
     */
    public function getJsonldType(): string
    {
        return $this->jsonldType;
    }

    /**
     * @param string $jsonldType
     */
    public function setJsonldType(string $jsonldType): void
    {
        $this->jsonldType = $jsonldType;
    }

    /**
     * @return string $jsonldName
     */
    public function getJsonldName(): string
    {
        return $this->jsonldName;
    }

    /**
     * @param string $jsonldName
     */
    public function setJsonldName(string $jsonldName): void
    {
        $this->jsonldName = $jsonldName;
    }

    /**
     * @return string $jsonldTelephone
     */
    public function getJsonldTelephone(): string
    {
        return $this->jsonldTelephone;
    }

    /**
     * @param string $jsonldTelephone
     */
    public function setJsonldTelephone(string $jsonldTelephone): void
    {
        $this->jsonldTelephone = $jsonldTelephone;
    }

    /**
     * @return string $jsonldFax
     */
    public function getJsonldFax(): string
    {
        return $this->jsonldFax;
    }

    /**
     * @param string $jsonldFax
     */
    public function setJsonldFax(string $jsonldFax): void
    {
        $this->jsonldFax = $jsonldFax;
    }

    /**
     * @return string $jsonldEmail
     */
    public function getJsonldEmail(): string
    {
        return $this->jsonldEmail;
    }

    /**
     * @param string $jsonldEmail
     */
    public function setJsonldEmail(string $jsonldEmail): void
    {
        $this->jsonldEmail = $jsonldEmail;
    }

    /**
     * @return string $jsonldSameAs
     */
    public function getJsonldSameAsFacebook(): string
    {
        return $this->jsonldSameAsFacebook;
    }

    /**
     * @param string $jsonldSameAsFacebook
     */
    public function setJsonldSameAsFacebook(string $jsonldSameAsFacebook): void
    {
        $this->jsonldSameAsFacebook = $jsonldSameAsFacebook;
    }

    /**
     * @return string $jsonldSameAs
     */
    public function getJsonldSameAsTwitter(): string
    {
        return $this->jsonldSameAsTwitter;
    }

    /**
     * @param string $jsonldSameAsTwitter
     */
    public function setJsonldSameAsTwitter(string $jsonldSameAsTwitter): void
    {
        $this->jsonldSameAsTwitter = $jsonldSameAsTwitter;
    }

    /**
     * @return string $jsonldSameAs
     */
    public function getJsonldSameAsInstagram(): string
    {
        return $this->jsonldSameAsInstagram;
    }

    /**
     * @param string $jsonldSameAsInstagram
     */
    public function setJsonldSameAsInstagram(string $jsonldSameAsInstagram): void
    {
        $this->jsonldSameAsInstagram = $jsonldSameAsInstagram;
    }

    /**
     * @return string $jsonldSameAs
     */
    public function getJsonldSameAsYoutube(): string
    {
        return $this->jsonldSameAsYoutube;
    }

    /**
     * @param string $jsonldSameAsYoutube
     */
    public function setJsonldSameAsYoutube(string $jsonldSameAsYoutube): void
    {
        $this->jsonldSameAsYoutube = $jsonldSameAsYoutube;
    }

    /**
     * @return string $jsonldSameAs
     */
    public function getJsonldSameAsLinkedin(): string
    {
        return $this->jsonldSameAsLinkedin;
    }

    /**
     * @param string $jsonldSameAsLinkedin
     */
    public function setJsonldSameAsLinkedin(string $jsonldSameAsLinkedin): void
    {
        $this->jsonldSameAsLinkedin = $jsonldSameAsLinkedin;
    }

    /**
     * @return string
     */
    public function getJsonldSameAsXing(): string
    {
        return $this->jsonldSameAsXing;
    }

    /**
     * @param string $jsonldSameAsXing
     */
    public function setJsonldSameAsXing(string $jsonldSameAsXing): void
    {
        $this->jsonldSameAsXing = $jsonldSameAsXing;
    }

    /**
     * @return string $jsonldSameAs
     */
    public function getJsonldSameAsPrinterest(): string
    {
        return $this->jsonldSameAsPrinterest;
    }

    /**
     * @param string $jsonldSameAsPrinterest
     */
    public function setJsonldSameAsPrinterest(string $jsonldSameAsPrinterest): void
    {
        $this->jsonldSameAsPrinterest = $jsonldSameAsPrinterest;
    }

    /**
     * @return string $jsonldSameAs
     */
    public function getJsonldSameAsSoundcloud(): string
    {
        return $this->jsonldSameAsSoundcloud;
    }

    /**
     * @param string $jsonldSameAsSoundcloud
     */
    public function setJsonldSameAsSoundcloud(string $jsonldSameAsSoundcloud): void
    {
        $this->jsonldSameAsSoundcloud = $jsonldSameAsSoundcloud;
    }

    /**
     * @return string $jsonldSameAs
     */
    public function getJsonldSameAsTumblr(): string
    {
        return $this->jsonldSameAsTumblr;
    }

    /**
     * @param string $jsonldSameAsTumblr
     */
    public function setJsonldSameAsTumblr(string $jsonldSameAsTumblr): void
    {
        $this->jsonldSameAsTumblr = $jsonldSameAsTumblr;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference|null $jsonldLogo
     */
    public function getJsonldLogo(): ?FileReference
    {
        return $this->jsonldLogo;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference|null $jsonldLogo
     */
    public function setJsonldLogo(?FileReference $jsonldLogo): void
    {
        $this->jsonldLogo = $jsonldLogo;
    }

    /**
     * @return string $jsonldAddressLocality
     */
    public function getJsonldAddressLocality(): string
    {
        return $this->jsonldAddressLocality;
    }

    /**
     * @param string $jsonldAddressLocality
     */
    public function setJsonldAddressLocality(string $jsonldAddressLocality): void
    {
        $this->jsonldAddressLocality = $jsonldAddressLocality;
    }

    /**
     * @return string $jsonldAddressPostalcode
     */
    public function getJsonldAddressPostalcode(): string
    {
        return $this->jsonldAddressPostalcode;
    }

    /**
     * @param string $jsonldAddressPostalcode
     */
    public function setJsonldAddressPostalcode(string $jsonldAddressPostalcode): void
    {
        $this->jsonldAddressPostalcode = $jsonldAddressPostalcode;
    }

    /**
     * @return string $jsonldAddressStreet
     */
    public function getJsonldAddressStreet(): string
    {
        return $this->jsonldAddressStreet;
    }

    /**
     * @param string $jsonldAddressStreet
     */
    public function setJsonldAddressStreet(string $jsonldAddressStreet): void
    {
        $this->jsonldAddressStreet = $jsonldAddressStreet;
    }

    /**
     * @return string
     */
    public function getMatomoIdsite(): string
    {
        return $this->matomoIdsite;
    }

    /**
     * @param string $matomoIdsite
     */
    public function setMatomoIdsite(string $matomoIdsite): void
    {
        $this->matomoIdsite = $matomoIdsite;
    }

    /**
     * @return string
     */
    public function getMatomoUrl(): string
    {
        return $this->matomoUrl;
    }

    /**
     * @param string $matomoUrl
     */
    public function setMatomoUrl(string $matomoUrl): void
    {
        $this->matomoUrl = $matomoUrl;
    }

    /**
     * @param \Mindshape\MindshapeSeo\Domain\Model\Configuration $configuration
     */
    public function mergeConfiguration(Configuration $configuration): void
    {
        $this->googleAnalytics = true === empty($this->googleAnalytics)
            ? $configuration->getGoogleAnalytics()
            : $this->googleAnalytics;

        $this->googleAnalyticsV4 = true === empty($this->googleAnalyticsV4)
            ? $configuration->getGoogleAnalyticsV4()
            : $this->googleAnalyticsV4;

        $this->googleTagmanager = true === empty($this->googleTagmanager)
            ? $configuration->getGoogleTagmanager()
            : $this->googleTagmanager;

        $this->matomoUrl = true === empty($this->matomoUrl)
            ? $configuration->getMatomoUrl()
            : $this->matomoUrl;

        $this->matomoIdsite = true === empty($this->matomoIdsite)
            ? $configuration->getMatomoIdsite()
            : $this->matomoIdsite;

        $this->titleAttachment = true === empty($this->titleAttachment)
            ? $configuration->getTitleAttachment()
            : $this->titleAttachment;

        $this->jsonldCustomUrl = true === empty($this->jsonldCustomUrl)
            ? $configuration->getJsonldCustomUrl()
            : $this->jsonldCustomUrl;

        $this->jsonldType = true === empty($this->jsonldType)
            ? $configuration->getJsonldType()
            : $this->jsonldType;

        $this->jsonldName = true === empty($this->jsonldName)
            ? $configuration->getJsonldName()
            : $this->jsonldName;

        $this->jsonldTelephone = true === empty($this->jsonldTelephone)
            ? $configuration->getJsonldTelephone()
            : $this->jsonldTelephone;

        $this->jsonldFax = true === empty($this->jsonldFax)
            ? $configuration->getJsonldFax()
            : $this->jsonldFax;

        $this->jsonldEmail = true === empty($this->jsonldEmail)
            ? $configuration->getJsonldEmail()
            : $this->jsonldEmail;

        $this->jsonldSameAsFacebook = true === empty($this->jsonldSameAsFacebook)
            ? $configuration->getJsonldSameAsFacebook()
            : $this->jsonldSameAsFacebook;

        $this->jsonldSameAsTwitter = true === empty($this->jsonldSameAsTwitter)
            ? $configuration->getJsonldSameAsTwitter()
            : $this->jsonldSameAsTwitter;

        $this->jsonldSameAsInstagram = true === empty($this->jsonldSameAsInstagram)
            ? $configuration->getJsonldSameAsInstagram()
            : $this->jsonldSameAsInstagram;

        $this->jsonldSameAsInstagram = true === empty($this->jsonldSameAsInstagram)
            ? $configuration->getJsonldSameAsInstagram()
            : $this->jsonldSameAsInstagram;

        $this->jsonldSameAsYoutube = true === empty($this->jsonldSameAsYoutube)
            ? $configuration->getJsonldSameAsYoutube()
            : $this->jsonldSameAsYoutube;

        $this->jsonldSameAsLinkedin = true === empty($this->jsonldSameAsLinkedin)
            ? $configuration->getJsonldSameAsLinkedin()
            : $this->jsonldSameAsLinkedin;

        $this->jsonldSameAsXing = true === empty($this->jsonldSameAsXing)
            ? $configuration->getJsonldSameAsXing()
            : $this->jsonldSameAsXing;

        $this->jsonldSameAsPrinterest = true === empty($this->jsonldSameAsPrinterest)
            ? $configuration->getJsonldSameAsPrinterest()
            : $this->jsonldSameAsPrinterest;

        $this->jsonldSameAsSoundcloud = true === empty($this->jsonldSameAsSoundcloud)
            ? $configuration->getJsonldSameAsSoundcloud()
            : $this->jsonldSameAsSoundcloud;

        $this->jsonldSameAsTumblr = true === empty($this->jsonldSameAsTumblr)
            ? $configuration->getJsonldSameAsTumblr()
            : $this->jsonldSameAsTumblr;

        $this->jsonldLogo = !$this->jsonldLogo instanceof FileReference
            ? $configuration->getJsonldLogo()
            : $this->jsonldLogo;

        $this->jsonldAddressLocality = true === empty($this->jsonldAddressLocality)
            ? $configuration->getJsonldAddressLocality()
            : $this->jsonldAddressLocality;

        $this->jsonldAddressPostalcode = true === empty($this->jsonldAddressPostalcode)
            ? $configuration->getJsonldAddressPostalcode()
            : $this->jsonldAddressPostalcode;

        $this->jsonldAddressStreet = true === empty($this->jsonldAddressStreet)
            ? $configuration->getJsonldAddressStreet()
            : $this->jsonldAddressStreet;
    }
}
