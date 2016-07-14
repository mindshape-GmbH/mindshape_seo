<?php
namespace Mindshape\MindshapeSeo\Domain\Model;

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

use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * @package mindshape_seo
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Configuration extends AbstractEntity
{
    const DEFAULT_DOMAIN = '*';

    const JSONLD_TYPE_ORGANIZATION = 'organization';
    const JSONLD_TYPE_PERSON = 'person';

    /**
     * domain
     *
     * @var string
     */
    protected $domain = '';

    /**
     * googleAnalytics
     *
     * @var string
     */
    protected $googleAnalytics = '';

    /**
     * piwikUrl
     *
     * @var string
     */
    protected $piwikUrl = '';

    /**
     * piwikIdsite
     *
     * @var string
     */
    protected $piwikIdsite = '';

    /**
     * titleAttachment
     *
     * @var string
     */
    protected $titleAttachment = '';

    /**
     * generateSitemap
     *
     * @var bool
     */
    protected $generateSitemap = false;

    /**
     * addHreflang
     *
     * @var bool
     */
    protected $addHreflang = false;

    /**
     * addJsonld
     *
     * @var bool
     */
    protected $addJsonld = false;

    /**
     * facebookDefaultImage
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
     */
    protected $facebookDefaultImage;

    /**
     * jsonldCustomUrl
     *
     * @var string
     */
    protected $jsonldCustomUrl = '';

    /**
     * jsonldType
     *
     * @var string
     */
    protected $jsonldType = '';

    /**
     * jsonldTelephone
     *
     * @var string
     */
    protected $jsonldTelephone = '';

    /**
     * jsonldFax
     *
     * @var string
     */
    protected $jsonldFax = '';

    /**
     * jsonldEmail
     *
     * @var string
     */
    protected $jsonldEmail = '';

    /**
     * jsonldSameAs
     *
     * @var string
     */
    protected $jsonldSameAs = '';

    /**
     * jsonldLogo
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
     */
    protected $jsonldLogo;

    /**
     * jsonldAddressLocality
     *
     * @var string
     */
    protected $jsonldAddressLocality = '';

    /**
     * jsonldAddressPostalcode
     *
     * @var string
     */
    protected $jsonldAddressPostalcode = '';

    /**
     * jsonldAddressStreet
     *
     * @var string
     */
    protected $jsonldAddressStreet = '';

    /**
     * Returns the domain
     *
     * @return string $domain
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Sets the domain
     *
     * @param string $domain
     * @return void
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * Returns the googleAnalytics
     *
     * @return string $googleAnalytics
     */
    public function getGoogleAnalytics()
    {
        return $this->googleAnalytics;
    }

    /**
     * Sets the googleAnalytics
     *
     * @param string $googleAnalytics
     * @return void
     */
    public function setGoogleAnalytics($googleAnalytics)
    {
        $this->googleAnalytics = $googleAnalytics;
    }

    /**
     * Returns the piwikUrl
     *
     * @return string $piwikUrl
     */
    public function getPiwikUrl()
    {
        return $this->piwikUrl;
    }

    /**
     * Sets the piwikUrl
     *
     * @param string $piwikUrl
     * @return void
     */
    public function setPiwikUrl($piwikUrl)
    {
        $this->piwikUrl = $piwikUrl;
    }

    /**
     * Returns the piwikIdsite
     *
     * @return string $piwikIdsite
     */
    public function getPiwikIdsite()
    {
        return $this->piwikIdsite;
    }

    /**
     * Sets the piwikIdsite
     *
     * @param string $piwikIdsite
     * @return void
     */
    public function setPiwikIdsite($piwikIdsite)
    {
        $this->piwikIdsite = $piwikIdsite;
    }

    /**
     * Returns the titleAttachment
     *
     * @return string $titleAttachment
     */
    public function getTitleAttachment()
    {
        return $this->titleAttachment;
    }

    /**
     * Sets the titleAttachment
     *
     * @param string $titleAttachment
     * @return void
     */
    public function setTitleAttachment($titleAttachment)
    {
        $this->titleAttachment = $titleAttachment;
    }

    /**
     * Returns the generateSitemap
     *
     * @return bool $generateSitemap
     */
    public function getGenerateSitemap()
    {
        return $this->generateSitemap;
    }

    /**
     * Sets the generateSitemap
     *
     * @param bool $generateSitemap
     * @return void
     */
    public function setGenerateSitemap($generateSitemap)
    {
        $this->generateSitemap = $generateSitemap;
    }

    /**
     * Returns the boolean state of generateSitemap
     *
     * @return bool
     */
    public function isGenerateSitemap()
    {
        return $this->generateSitemap;
    }

    /**
     * Returns the addHreflang
     *
     * @return bool $addHreflang
     */
    public function getAddHreflang()
    {
        return $this->addHreflang;
    }

    /**
     * Sets the addHreflang
     *
     * @param bool $addHreflang
     * @return void
     */
    public function setAddHreflang($addHreflang)
    {
        $this->addHreflang = $addHreflang;
    }

    /**
     * Returns the boolean state of addHreflang
     *
     * @return bool
     */
    public function isAddHreflang()
    {
        return $this->addHreflang;
    }

    /**
     * Returns the addJsonld
     *
     * @return bool $addJsonld
     */
    public function getAddJsonld()
    {
        return $this->addJsonld;
    }

    /**
     * Sets the addJsonld
     *
     * @param bool $addJsonld
     * @return void
     */
    public function setAddJsonld($addJsonld)
    {
        $this->addJsonld = $addJsonld;
    }

    /**
     * Returns the boolean state of addJsonld
     *
     * @return bool
     */
    public function isAddJsonld()
    {
        return $this->addJsonld;
    }

    /**
     * Returns the facebookDefaultImage
     *
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference $facebookDefaultImage
     */
    public function getFacebookDefaultImage()
    {
        return $this->facebookDefaultImage;
    }

    /**
     * Sets the facebookDefaultImage
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $facebookDefaultImage
     * @return void
     */
    public function setFacebookDefaultImage(FileReference $facebookDefaultImage = null)
    {
        $this->facebookDefaultImage = $facebookDefaultImage;
    }

    /**
     * Returns the jsonldCustomUrl
     *
     * @return string $jsonldCustomUrl
     */
    public function getJsonldCustomUrl()
    {
        return $this->jsonldCustomUrl;
    }

    /**
     * Sets the jsonldCustomUrl
     *
     * @param string $jsonldCustomUrl
     * @return void
     */
    public function setJsonldCustomUrl($jsonldCustomUrl)
    {
        $this->jsonldCustomUrl = $jsonldCustomUrl;
    }

    /**
     * Returns the jsonldType
     *
     * @return string $jsonldType
     */
    public function getJsonldType()
    {
        return $this->jsonldType;
    }

    /**
     * Sets the jsonldType
     *
     * @param string $jsonldType
     * @return void
     */
    public function setJsonldType($jsonldType)
    {
        $this->jsonldType = $jsonldType;
    }

    /**
     * Returns the jsonldTelephone
     *
     * @return string $jsonldTelephone
     */
    public function getJsonldTelephone()
    {
        return $this->jsonldTelephone;
    }

    /**
     * Sets the jsonldTelephone
     *
     * @param string $jsonldTelephone
     * @return void
     */
    public function setJsonldTelephone($jsonldTelephone)
    {
        $this->jsonldTelephone = $jsonldTelephone;
    }

    /**
     * Returns the jsonldFax
     *
     * @return string $jsonldFax
     */
    public function getJsonldFax()
    {
        return $this->jsonldFax;
    }

    /**
     * Sets the jsonldFax
     *
     * @param string $jsonldFax
     * @return void
     */
    public function setJsonldFax($jsonldFax)
    {
        $this->jsonldFax = $jsonldFax;
    }

    /**
     * Returns the jsonldEmail
     *
     * @return string $jsonldEmail
     */
    public function getJsonldEmail()
    {
        return $this->jsonldEmail;
    }

    /**
     * Sets the jsonldEmail
     *
     * @param string $jsonldEmail
     * @return void
     */
    public function setJsonldEmail($jsonldEmail)
    {
        $this->jsonldEmail = $jsonldEmail;
    }

    /**
     * Returns the jsonldSameAs
     *
     * @return string $jsonldSameAs
     */
    public function getJsonldSameAs()
    {
        return $this->jsonldSameAs;
    }

    /**
     * Sets the jsonldSameAs
     *
     * @param string $jsonldSameAs
     * @return void
     */
    public function setJsonldSameAs($jsonldSameAs)
    {
        $this->jsonldSameAs = $jsonldSameAs;
    }

    /**
     * Returns the jsonldLogo
     *
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference $jsonldLogo
     */
    public function getJsonldLogo()
    {
        return $this->jsonldLogo;
    }

    /**
     * Sets the jsonldLogo
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $jsonldLogo
     * @return void
     */
    public function setJsonldLogo(FileReference $jsonldLogo = null)
    {
        $this->jsonldLogo = $jsonldLogo;
    }

    /**
     * Returns the jsonldAddressLocality
     *
     * @return string $jsonldAddressLocality
     */
    public function getJsonldAddressLocality()
    {
        return $this->jsonldAddressLocality;
    }

    /**
     * Sets the jsonldAddressLocality
     *
     * @param string $jsonldAddressLocality
     * @return void
     */
    public function setJsonldAddressLocality($jsonldAddressLocality)
    {
        $this->jsonldAddressLocality = $jsonldAddressLocality;
    }

    /**
     * Returns the jsonldAddressPostalcode
     *
     * @return string $jsonldAddressPostalcode
     */
    public function getJsonldAddressPostalcode()
    {
        return $this->jsonldAddressPostalcode;
    }

    /**
     * Sets the jsonldAddressPostalcode
     *
     * @param string $jsonldAddressPostalcode
     * @return void
     */
    public function setJsonldAddressPostalcode($jsonldAddressPostalcode)
    {
        $this->jsonldAddressPostalcode = $jsonldAddressPostalcode;
    }

    /**
     * Returns the jsonldAddressStreet
     *
     * @return string $jsonldAddressStreet
     */
    public function getJsonldAddressStreet()
    {
        return $this->jsonldAddressStreet;
    }

    /**
     * Sets the jsonldAddressStreet
     *
     * @param string $jsonldAddressStreet
     * @return void
     */
    public function setJsonldAddressStreet($jsonldAddressStreet)
    {
        $this->jsonldAddressStreet = $jsonldAddressStreet;
    }
}
