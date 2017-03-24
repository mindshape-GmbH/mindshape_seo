<?php
namespace Mindshape\MindshapeSeo\Domain\Model;

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

use TYPO3\CMS\Extbase\Domain\Model\FileReference as ExtbaseFileReference;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * @package mindshape_seo
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Configuration extends AbstractEntity
{
    const DEFAULT_DOMAIN = '*';
    const DEFAULT_TITLE_ATTACHMENT_SEPERATOR = '|';

    const JSONLD_TYPE_ORGANIZATION = 'Organization';
    const JSONLD_TYPE_PERSON = 'Person';

    const TITLE_ATTACHMENT_POSITION_PREFIX = 'prefix';
    const TITLE_ATTACHMENT_POSITION_SUFFIX = 'suffix';

    /**
     * @var string
     */
    protected $domain = '';

    /**
     * @var string
     */
    protected $sitename = '';

    /**
     * @var string
     */
    protected $googleAnalytics = '';

    /**
     * @var string
     */
    protected $googleTagmanager = '';

    /**
     * @var string
     */
    protected $piwikUrl = '';

    /**
     * @var string
     */
    protected $piwikIdsite = '';

    /**
     * @var string
     */
    protected $titleAttachment = '';

    /**
     * @var string
     */
    protected $titleAttachmentSeperator = '';

    /**
     * @var string
     */
    protected $titleAttachmentPosition = '';

    /**
     * @var bool
     */
    protected $addAnalytics = false;

    /**
     * @var bool
     */
    protected $addHreflang = false;

    /**
     * @var bool
     */
    protected $addJsonld = false;

    /**
     * @var bool
     */
    protected $addJsonldBreadcrumb = false;

    /**
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
     */
    protected $facebookDefaultImage;

    /**
     * @var int
     */
    protected $imageSitemapMinHeight = 0;

    /**
     * @var int
     */
    protected $imageSitemapMinWidth = 0;

    /**
     * @var string
     */
    protected $jsonldCustomUrl = '';

    /**
     * @var string
     */
    protected $jsonldType = '';

    /**
     * @var string
     */
    protected $jsonldName = '';

    /**
     * @var string
     */
    protected $jsonldTelephone = '';

    /**
     * @var string
     */
    protected $jsonldFax = '';

    /**
     * @var string
     */
    protected $jsonldEmail = '';

    /**
     * @var string
     */
    protected $jsonldSameAsFacebook = '';

    /**
     * @var string
     */
    protected $jsonldSameAsTwitter = '';

    /**
     * @var string
     */
    protected $jsonldSameAsGoogleplus = '';

    /**
     * @var string
     */
    protected $jsonldSameAsInstagram = '';

    /**
     * @var string
     */
    protected $jsonldSameAsYoutube = '';

    /**
     * @var string
     */
    protected $jsonldSameAsLinkedin = '';

    /**
     * @var string
     */
    protected $jsonldSameAsXing = '';

    /**
     * @var string
     */
    protected $jsonldSameAsPrinterest = '';

    /**
     * @var string
     */
    protected $jsonldSameAsSoundcloud = '';

    /**
     * @var string
     */
    protected $jsonldSameAsTumblr = '';

    /**
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
     */
    protected $jsonldLogo;

    /**
     * @var string
     */
    protected $jsonldAddressLocality = '';

    /**
     * @var string
     */
    protected $jsonldAddressPostalcode = '';

    /**
     * @var string
     */
    protected $jsonldAddressStreet = '';

    /**
     * @return string $domain
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     * @return void
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * @return string
     */
    public function getSitename()
    {
        return $this->sitename;
    }

    /**
     * @param string $sitename
     * @return void
     */
    public function setSitename($sitename)
    {
        $this->sitename = $sitename;
    }

    /**
     * @return string $googleAnalytics
     */
    public function getGoogleAnalytics()
    {
        return $this->googleAnalytics;
    }

    /**
     * @param string $googleAnalytics
     * @return void
     */
    public function setGoogleAnalytics($googleAnalytics)
    {
        $this->googleAnalytics = $googleAnalytics;
    }

    /**
     * @return string
     */
    public function getGoogleTagmanager()
    {
        return $this->googleTagmanager;
    }

    /**
     * @param string $googleTagmanager
     * @return void
     */
    public function setGoogleTagmanager($googleTagmanager)
    {
        $this->googleTagmanager = $googleTagmanager;
    }

    /**
     * @return string $piwikUrl
     */
    public function getPiwikUrl()
    {
        return $this->piwikUrl;
    }

    /**
     * @param string $piwikUrl
     * @return void
     */
    public function setPiwikUrl($piwikUrl)
    {
        $this->piwikUrl = $piwikUrl;
    }

    /**
     * @return string $piwikIdsite
     */
    public function getPiwikIdsite()
    {
        return $this->piwikIdsite;
    }

    /**
     * @param string $piwikIdsite
     * @return void
     */
    public function setPiwikIdsite($piwikIdsite)
    {
        $this->piwikIdsite = $piwikIdsite;
    }

    /**
     * @return string $titleAttachment
     */
    public function getTitleAttachment()
    {
        return $this->titleAttachment;
    }

    /**
     * @param string $titleAttachment
     * @return void
     */
    public function setTitleAttachment($titleAttachment)
    {
        $this->titleAttachment = $titleAttachment;
    }

    /**
     * @return string $titleAttachmentSeperator
     */
    public function getTitleAttachmentSeperator()
    {
        return $this->titleAttachmentSeperator;
    }

    /**
     * @param string $titleAttachmentSeperator
     * @return void
     */
    public function setTitleAttachmentSeperator($titleAttachmentSeperator)
    {
        $this->titleAttachmentSeperator = $titleAttachmentSeperator;
    }

    /**
     * @return string $titleAttachmentPosition
     */
    public function getTitleAttachmentPosition()
    {
        return $this->titleAttachmentPosition;
    }

    /**
     * @param string $titleAttachmentPosition
     * @return void
     */
    public function setTitleAttachmentPosition($titleAttachmentPosition)
    {
        $this->titleAttachmentPosition = $titleAttachmentPosition;
    }

    /**
     * @return bool $addAnalytics
     */
    public function getAddAnalytics()
    {
        return $this->addAnalytics;
    }

    /**
     * @param bool $addAnalytics
     * @return void
     */
    public function setAddAnalytics($addAnalytics)
    {
        $this->addAnalytics = $addAnalytics;
    }

    /**
     * @return bool
     */
    public function isAddAnalytics()
    {
        return $this->addAnalytics;
    }

    /**
     * @return bool $addHreflang
     */
    public function getAddHreflang()
    {
        return $this->addHreflang;
    }

    /**
     * @param bool $addHreflang
     * @return void
     */
    public function setAddHreflang($addHreflang)
    {
        $this->addHreflang = $addHreflang;
    }

    /**
     * @return bool
     */
    public function isAddHreflang()
    {
        return $this->addHreflang;
    }

    /**
     * @return bool $addJsonld
     */
    public function getAddJsonld()
    {
        return $this->addJsonld;
    }

    /**
     * @param bool $addJsonld
     * @return void
     */
    public function setAddJsonld($addJsonld)
    {
        $this->addJsonld = $addJsonld;
    }

    /**
     * @return bool
     */
    public function isAddJsonld()
    {
        return $this->addJsonld;
    }

    /**
     * @return bool $addJsonldBreadcrumb
     */
    public function getAddJsonldBreadcrumb()
    {
        return $this->addJsonldBreadcrumb;
    }

    /**
     * @param bool $addJsonldBreadcrumb
     * @return void
     */
    public function setAddJsonldBreadcrumb($addJsonldBreadcrumb)
    {
        $this->addJsonldBreadcrumb = $addJsonldBreadcrumb;
    }

    /**
     * @return bool
     */
    public function isAddJsonldBreadcrumb()
    {
        return $this->addJsonldBreadcrumb;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference $facebookDefaultImage
     */
    public function getFacebookDefaultImage()
    {
        return $this->facebookDefaultImage;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $facebookDefaultImage
     * @return void
     */
    public function setFacebookDefaultImage(ExtbaseFileReference $facebookDefaultImage = null)
    {
        $this->facebookDefaultImage = $facebookDefaultImage;
    }

    /**
     * @return string $imageSitemapMinHeight
     */
    public function getImageSitemapMinHeight()
    {
        return $this->imageSitemapMinHeight;
    }

    /**
     * @param string $imageSitemapMinHeight
     * @return void
     */
    public function setImageSitemapMinHeight($imageSitemapMinHeight)
    {
        $this->imageSitemapMinHeight = $imageSitemapMinHeight;
    }

    /**
     * @return string $imageSitemapMinWidth
     */
    public function getImageSitemapMinWidth()
    {
        return $this->imageSitemapMinWidth;
    }

    /**
     * @param string $imageSitemapMinWidth
     * @return void
     */
    public function setImageSitemapMinWidth($imageSitemapMinWidth)
    {
        $this->imageSitemapMinWidth = $imageSitemapMinWidth;
    }

    /**
     * @return string $jsonldCustomUrl
     */
    public function getJsonldCustomUrl()
    {
        return $this->jsonldCustomUrl;
    }

    /**
     * @param string $jsonldCustomUrl
     * @return void
     */
    public function setJsonldCustomUrl($jsonldCustomUrl)
    {
        $this->jsonldCustomUrl = $jsonldCustomUrl;
    }

    /**
     * @return string $jsonldType
     */
    public function getJsonldType()
    {
        return $this->jsonldType;
    }

    /**
     * @param string $jsonldType
     * @return void
     */
    public function setJsonldType($jsonldType)
    {
        $this->jsonldType = $jsonldType;
    }

    /**
     * @return string $jsonldName
     */
    public function getJsonldName()
    {
        return $this->jsonldName;
    }

    /**
     * @param string $jsonldName
     * @return void
     */
    public function setJsonldName($jsonldName)
    {
        $this->jsonldName = $jsonldName;
    }

    /**
     * @return string $jsonldTelephone
     */
    public function getJsonldTelephone()
    {
        return $this->jsonldTelephone;
    }

    /**
     * @param string $jsonldTelephone
     * @return void
     */
    public function setJsonldTelephone($jsonldTelephone)
    {
        $this->jsonldTelephone = $jsonldTelephone;
    }

    /**
     * @return string $jsonldFax
     */
    public function getJsonldFax()
    {
        return $this->jsonldFax;
    }

    /**
     * @param string $jsonldFax
     * @return void
     */
    public function setJsonldFax($jsonldFax)
    {
        $this->jsonldFax = $jsonldFax;
    }

    /**
     * @return string $jsonldEmail
     */
    public function getJsonldEmail()
    {
        return $this->jsonldEmail;
    }

    /**
     * @param string $jsonldEmail
     * @return void
     */
    public function setJsonldEmail($jsonldEmail)
    {
        $this->jsonldEmail = $jsonldEmail;
    }

    /**
     * @return string $jsonldSameAs
     */
    public function getJsonldSameAsFacebook()
    {
        return $this->jsonldSameAsFacebook;
    }

    /**
     * @param string $jsonldSameAsFacebook
     * @return void
     */
    public function setJsonldSameAsFacebook($jsonldSameAsFacebook)
    {
        $this->jsonldSameAsFacebook = $jsonldSameAsFacebook;
    }

    /**
     * @return string $jsonldSameAs
     */
    public function getJsonldSameAsTwitter()
    {
        return $this->jsonldSameAsTwitter;
    }

    /**
     * @param string $jsonldSameAsTwitter
     * @return void
     */
    public function setJsonldSameAsTwitter($jsonldSameAsTwitter)
    {
        $this->jsonldSameAsTwitter = $jsonldSameAsTwitter;
    }

    /**
     * @return string $jsonldSameAs
     */
    public function getJsonldSameAsGoogleplus()
    {
        return $this->jsonldSameAsGoogleplus;
    }

    /**
     * @param string $jsonldSameAsGoogleplus
     * @return void
     */
    public function setJsonldSameAsGoogleplus($jsonldSameAsGoogleplus)
    {
        $this->jsonldSameAsGoogleplus = $jsonldSameAsGoogleplus;
    }

    /**
     * @return string $jsonldSameAs
     */
    public function getJsonldSameAsInstagram()
    {
        return $this->jsonldSameAsInstagram;
    }

    /**
     * @param string $jsonldSameAsInstagram
     * @return void
     */
    public function setJsonldSameAsInstagram($jsonldSameAsInstagram)
    {
        $this->jsonldSameAsInstagram = $jsonldSameAsInstagram;
    }

    /**
     * @return string $jsonldSameAs
     */
    public function getJsonldSameAsYoutube()
    {
        return $this->jsonldSameAsYoutube;
    }

    /**
     * @param string $jsonldSameAsYoutube
     * @return void
     */
    public function setJsonldSameAsYoutube($jsonldSameAsYoutube)
    {
        $this->jsonldSameAsYoutube = $jsonldSameAsYoutube;
    }

    /**
     * @return string $jsonldSameAs
     */
    public function getJsonldSameAsLinkedin()
    {
        return $this->jsonldSameAsLinkedin;
    }

    /**
     * @param string $jsonldSameAsLinkedin
     * @return void
     */
    public function setJsonldSameAsLinkedin($jsonldSameAsLinkedin)
    {
        $this->jsonldSameAsLinkedin = $jsonldSameAsLinkedin;
    }

    /**
     * @return string
     */
    public function getJsonldSameAsXing()
    {
        return $this->jsonldSameAsXing;
    }

    /**
     * @param string $jsonldSameAsXing
     * @return void
     */
    public function setJsonldSameAsXing($jsonldSameAsXing)
    {
        $this->jsonldSameAsXing = $jsonldSameAsXing;
    }

    /**
     * @return string $jsonldSameAs
     */
    public function getJsonldSameAsPrinterest()
    {
        return $this->jsonldSameAsPrinterest;
    }

    /**
     * @param string $jsonldSameAsPrinterest
     * @return void
     */
    public function setJsonldSameAsPrinterest($jsonldSameAsPrinterest)
    {
        $this->jsonldSameAsPrinterest = $jsonldSameAsPrinterest;
    }

    /**
     * @return string $jsonldSameAs
     */
    public function getJsonldSameAsSoundcloud()
    {
        return $this->jsonldSameAsSoundcloud;
    }

    /**
     * @param string $jsonldSameAsSoundcloud
     * @return void
     */
    public function setJsonldSameAsSoundcloud($jsonldSameAsSoundcloud)
    {
        $this->jsonldSameAsSoundcloud = $jsonldSameAsSoundcloud;
    }

    /**
     * @return string $jsonldSameAs
     */
    public function getJsonldSameAsTumblr()
    {
        return $this->jsonldSameAsTumblr;
    }

    /**
     * @param string $jsonldSameAsTumblr
     * @return void
     */
    public function setJsonldSameAsTumblr($jsonldSameAsTumblr)
    {
        $this->jsonldSameAsTumblr = $jsonldSameAsTumblr;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference $jsonldLogo
     */
    public function getJsonldLogo()
    {
        return $this->jsonldLogo;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $jsonldLogo
     * @return void
     */
    public function setJsonldLogo(ExtbaseFileReference $jsonldLogo = null)
    {
        $this->jsonldLogo = $jsonldLogo;
    }

    /**
     * @return string $jsonldAddressLocality
     */
    public function getJsonldAddressLocality()
    {
        return $this->jsonldAddressLocality;
    }

    /**
     * @param string $jsonldAddressLocality
     * @return void
     */
    public function setJsonldAddressLocality($jsonldAddressLocality)
    {
        $this->jsonldAddressLocality = $jsonldAddressLocality;
    }

    /**
     * @return string $jsonldAddressPostalcode
     */
    public function getJsonldAddressPostalcode()
    {
        return $this->jsonldAddressPostalcode;
    }

    /**
     * @param string $jsonldAddressPostalcode
     * @return void
     */
    public function setJsonldAddressPostalcode($jsonldAddressPostalcode)
    {
        $this->jsonldAddressPostalcode = $jsonldAddressPostalcode;
    }

    /**
     * @return string $jsonldAddressStreet
     */
    public function getJsonldAddressStreet()
    {
        return $this->jsonldAddressStreet;
    }

    /**
     * @param string $jsonldAddressStreet
     * @return void
     */
    public function setJsonldAddressStreet($jsonldAddressStreet)
    {
        $this->jsonldAddressStreet = $jsonldAddressStreet;
    }
}
