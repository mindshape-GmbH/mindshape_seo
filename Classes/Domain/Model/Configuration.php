<?php

namespace Mindshape\MindshapeSeo\Domain\Model;

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

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Configuration extends AbstractEntity
{
    public const TABLE = 'tx_mindshapeseo_domain_model_configuration';

    public const DEFAULT_DOMAIN = '*';
    public const DEFAULT_TITLE_ATTACHMENT_SEPARATOR = '|';

    public const JSONLD_TYPE_ORGANIZATION = 'Organization';
    public const JSONLD_TYPE_PERSON = 'Person';

    public const TITLE_ATTACHMENT_POSITION_PREFIX = 'prefix';
    public const TITLE_ATTACHMENT_POSITION_SUFFIX = 'suffix';


    protected string $domain = '';
    protected bool $mergeWithDefault = true;
    protected string $googleAnalytics = '';
    protected string $googleAnalyticsV4 = '';
    protected string $googleTagmanager = '';
    protected string $titleAttachment = '';
    protected string $titleAttachmentSeparator = '';
    protected string $titleAttachmentPosition = '';
    protected bool $addAnalytics = false;
    protected bool $googleAnalyticsUseCookieConsent = false;
    protected bool $googleAnalyticsV4UseCookieConsent = false;
    protected bool $tagmanagerUseCookieConsent = false;
    protected bool $matomoUseCookieConsent = false;
    protected bool $addJsonld = false;
    protected bool $addJsonldBreadcrumb = false;
    protected string $jsonldCustomUrl = '';
    protected string $jsonldType = '';
    protected string $jsonldName = '';
    protected string $jsonldTelephone = '';
    protected string $jsonldFax = '';
    protected string $jsonldEmail = '';
    protected string $jsonldSameAsFacebook = '';
    protected string $jsonldSameAsTwitter = '';
    protected string $jsonldSameAsInstagram = '';
    protected string $jsonldSameAsYoutube = '';
    protected string $jsonldSameAsLinkedin = '';
    protected string $jsonldSameAsXing = '';
    protected string $jsonldSameAsPrinterest = '';
    protected string $jsonldSameAsSoundcloud = '';
    protected string $jsonldSameAsTumblr = '';
    protected string $jsonldAddressLocality = '';
    protected string $jsonldAddressPostalcode = '';
    protected string $jsonldAddressStreet = '';
    protected string $matomoUrl = '';
    protected string $matomoIdsite = '';

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function setDomain(string $domain): void
    {
        $this->domain = $domain;
    }

    public function isMergeWithDefault(): bool
    {
        return $this->mergeWithDefault;
    }

    public function setMergeWithDefault(bool $mergeWithDefault): void
    {
        $this->mergeWithDefault = $mergeWithDefault;
    }

    public function getGoogleAnalytics(): string
    {
        return $this->googleAnalytics;
    }

    public function setGoogleAnalytics(string $googleAnalytics): void
    {
        $this->googleAnalytics = $googleAnalytics;
    }

    public function getGoogleAnalyticsV4(): string
    {
        return $this->googleAnalyticsV4;
    }

    public function setGoogleAnalyticsV4(string $googleAnalyticsV4): void
    {
        $this->googleAnalyticsV4 = $googleAnalyticsV4;
    }

    public function getGoogleTagmanager(): string
    {
        return $this->googleTagmanager;
    }

    public function setGoogleTagmanager(string $googleTagmanager): void
    {
        $this->googleTagmanager = $googleTagmanager;
    }

    public function getTitleAttachment(): string
    {
        return $this->titleAttachment;
    }

    public function setTitleAttachment(string $titleAttachment): void
    {
        $this->titleAttachment = $titleAttachment;
    }

    public function getTitleAttachmentSeparator(): string
    {
        return $this->titleAttachmentSeparator;
    }

    public function setTitleAttachmentSeparator(string $titleAttachmentSeparator): void
    {
        $this->titleAttachmentSeparator = $titleAttachmentSeparator;
    }

    public function getTitleAttachmentPosition(): string
    {
        return $this->titleAttachmentPosition;
    }

    public function setTitleAttachmentPosition(string $titleAttachmentPosition): void
    {
        $this->titleAttachmentPosition = $titleAttachmentPosition;
    }

    public function getAddAnalytics(): bool
    {
        return $this->addAnalytics;
    }

    public function setAddAnalytics(bool $addAnalytics): void
    {
        $this->addAnalytics = $addAnalytics;
    }

    public function isAddAnalytics(): bool
    {
        return $this->addAnalytics;
    }

    public function getGoogleAnalyticsUseCookieConsent(): bool
    {
        return $this->googleAnalyticsUseCookieConsent;
    }

    public function setGoogleAnalyticsUseCookieConsent(bool $googleAnalyticsUseCookieConsent): void
    {
        $this->googleAnalyticsUseCookieConsent = $googleAnalyticsUseCookieConsent;
    }

    public function getGoogleAnalyticsV4UseCookieConsent(): bool
    {
        return $this->googleAnalyticsV4UseCookieConsent;
    }

    public function setGoogleAnalyticsV4UseCookieConsent(bool $googleAnalyticsV4UseCookieConsent): void
    {
        $this->googleAnalyticsV4UseCookieConsent = $googleAnalyticsV4UseCookieConsent;
    }

    public function getTagmanagerUseCookieConsent(): bool
    {
        return $this->tagmanagerUseCookieConsent;
    }

    public function setTagmanagerUseCookieConsent(bool $tagmanagerUseCookieConsent): void
    {
        $this->tagmanagerUseCookieConsent = $tagmanagerUseCookieConsent;
    }

    public function getMatomoUseCookieConsent(): bool
    {
        return $this->matomoUseCookieConsent;
    }

    public function setMatomoUseCookieConsent(bool $matomoUseCookieConsent): void
    {
        $this->matomoUseCookieConsent = $matomoUseCookieConsent;
    }

    public function getAddJsonld(): bool
    {
        return $this->addJsonld;
    }

    public function setAddJsonld(bool $addJsonld): void
    {
        $this->addJsonld = $addJsonld;
    }

    public function isAddJsonld(): bool
    {
        return $this->addJsonld;
    }

    public function getAddJsonldBreadcrumb(): bool
    {
        return $this->addJsonldBreadcrumb;
    }

    public function setAddJsonldBreadcrumb(bool $addJsonldBreadcrumb): void
    {
        $this->addJsonldBreadcrumb = $addJsonldBreadcrumb;
    }

    public function isAddJsonldBreadcrumb(): bool
    {
        return $this->addJsonldBreadcrumb;
    }

    public function getJsonldCustomUrl(): string
    {
        return $this->jsonldCustomUrl;
    }

    public function setJsonldCustomUrl(string $jsonldCustomUrl): void
    {
        $this->jsonldCustomUrl = $jsonldCustomUrl;
    }

    public function getJsonldType(): string
    {
        return $this->jsonldType;
    }

    public function setJsonldType(string $jsonldType): void
    {
        $this->jsonldType = $jsonldType;
    }

    public function getJsonldName(): string
    {
        return $this->jsonldName;
    }

    public function setJsonldName(string $jsonldName): void
    {
        $this->jsonldName = $jsonldName;
    }

    public function getJsonldTelephone(): string
    {
        return $this->jsonldTelephone;
    }

    public function setJsonldTelephone(string $jsonldTelephone): void
    {
        $this->jsonldTelephone = $jsonldTelephone;
    }

    public function getJsonldFax(): string
    {
        return $this->jsonldFax;
    }

    public function setJsonldFax(string $jsonldFax): void
    {
        $this->jsonldFax = $jsonldFax;
    }

    public function getJsonldEmail(): string
    {
        return $this->jsonldEmail;
    }

    public function setJsonldEmail(string $jsonldEmail): void
    {
        $this->jsonldEmail = $jsonldEmail;
    }

    public function getJsonldSameAsFacebook(): string
    {
        return $this->jsonldSameAsFacebook;
    }

    public function setJsonldSameAsFacebook(string $jsonldSameAsFacebook): void
    {
        $this->jsonldSameAsFacebook = $jsonldSameAsFacebook;
    }

    public function getJsonldSameAsTwitter(): string
    {
        return $this->jsonldSameAsTwitter;
    }

    public function setJsonldSameAsTwitter(string $jsonldSameAsTwitter): void
    {
        $this->jsonldSameAsTwitter = $jsonldSameAsTwitter;
    }

    public function getJsonldSameAsInstagram(): string
    {
        return $this->jsonldSameAsInstagram;
    }

    public function setJsonldSameAsInstagram(string $jsonldSameAsInstagram): void
    {
        $this->jsonldSameAsInstagram = $jsonldSameAsInstagram;
    }

    public function getJsonldSameAsYoutube(): string
    {
        return $this->jsonldSameAsYoutube;
    }

    public function setJsonldSameAsYoutube(string $jsonldSameAsYoutube): void
    {
        $this->jsonldSameAsYoutube = $jsonldSameAsYoutube;
    }

    public function getJsonldSameAsLinkedin(): string
    {
        return $this->jsonldSameAsLinkedin;
    }

    public function setJsonldSameAsLinkedin(string $jsonldSameAsLinkedin): void
    {
        $this->jsonldSameAsLinkedin = $jsonldSameAsLinkedin;
    }

    public function getJsonldSameAsXing(): string
    {
        return $this->jsonldSameAsXing;
    }

    public function setJsonldSameAsXing(string $jsonldSameAsXing): void
    {
        $this->jsonldSameAsXing = $jsonldSameAsXing;
    }

    public function getJsonldSameAsPrinterest(): string
    {
        return $this->jsonldSameAsPrinterest;
    }

    public function setJsonldSameAsPrinterest(string $jsonldSameAsPrinterest): void
    {
        $this->jsonldSameAsPrinterest = $jsonldSameAsPrinterest;
    }

    public function getJsonldSameAsSoundcloud(): string
    {
        return $this->jsonldSameAsSoundcloud;
    }

    public function setJsonldSameAsSoundcloud(string $jsonldSameAsSoundcloud): void
    {
        $this->jsonldSameAsSoundcloud = $jsonldSameAsSoundcloud;
    }

    public function getJsonldSameAsTumblr(): string
    {
        return $this->jsonldSameAsTumblr;
    }

    public function setJsonldSameAsTumblr(string $jsonldSameAsTumblr): void
    {
        $this->jsonldSameAsTumblr = $jsonldSameAsTumblr;
    }

    public function getJsonldAddressLocality(): string
    {
        return $this->jsonldAddressLocality;
    }

    public function setJsonldAddressLocality(string $jsonldAddressLocality): void
    {
        $this->jsonldAddressLocality = $jsonldAddressLocality;
    }

    public function getJsonldAddressPostalcode(): string
    {
        return $this->jsonldAddressPostalcode;
    }

    public function setJsonldAddressPostalcode(string $jsonldAddressPostalcode): void
    {
        $this->jsonldAddressPostalcode = $jsonldAddressPostalcode;
    }

    public function getJsonldAddressStreet(): string
    {
        return $this->jsonldAddressStreet;
    }

    public function setJsonldAddressStreet(string $jsonldAddressStreet): void
    {
        $this->jsonldAddressStreet = $jsonldAddressStreet;
    }

    public function getMatomoIdsite(): string
    {
        return $this->matomoIdsite;
    }

    public function setMatomoIdsite(string $matomoIdsite): void
    {
        $this->matomoIdsite = $matomoIdsite;
    }

    public function getMatomoUrl(): string
    {
        return $this->matomoUrl;
    }

    public function setMatomoUrl(string $matomoUrl): void
    {
        $this->matomoUrl = $matomoUrl;
    }

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
