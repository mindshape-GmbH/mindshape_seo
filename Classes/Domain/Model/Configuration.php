<?php
namespace Mindshape\MindshapeSeo\Domain\Model;

/***************************************************************
 *
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

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * @package mindshape_seo
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Configuration extends AbstractEntity
{
    const DEFAULT_DOMAIN = '*';
    
    /**
     * @var string
     */
    protected $domain = '';

    /**
     * @var string
     */
    protected $customUrl = '';

    /**
     * @var string
     */
    protected $googleAnalytics = '';

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
    protected $generateSitemap = '';

    /**
     * @var bool
     */
    protected $addHreflang = false;

    /**
     * @var string
     */
    protected $facebookDefaultImage = '';

    /**
     * @var bool
     */
    protected $addJsonld = false;

    /**
     * @var string
     */
    protected $jsonldType = '';

    /**
     * @var string
     */
    protected $jsonldTelephone = '';

    /**
     * @var string
     */
    protected $jsonldTFax = '';

    /**
     * @var string
     */
    protected $jsonldEmail = '';

    /**
     * @var string
     */
    protected $jsonldSameAs = '';

    /**
     * @var string
     */
    protected $jsonldLogo = '';

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
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * @return string
     */
    public function getCustomUrl()
    {
        return $this->customUrl;
    }

    /**
     * @param string $customUrl
     */
    public function setCustomUrl($customUrl)
    {
        $this->customUrl = $customUrl;
    }

    /**
     * @return string
     */
    public function getGoogleAnalytics()
    {
        return $this->googleAnalytics;
    }

    /**
     * @param string $googleAnalytics
     */
    public function setGoogleAnalytics($googleAnalytics)
    {
        $this->googleAnalytics = $googleAnalytics;
    }

    /**
     * @return string
     */
    public function getPiwikUrl()
    {
        return $this->piwikUrl;
    }

    /**
     * @param string $piwikUrl
     */
    public function setPiwikUrl($piwikUrl)
    {
        $this->piwikUrl = $piwikUrl;
    }

    /**
     * @return string
     */
    public function getPiwikIdsite()
    {
        return $this->piwikIdsite;
    }

    /**
     * @param string $piwikIdsite
     */
    public function setPiwikIdsite($piwikIdsite)
    {
        $this->piwikIdsite = $piwikIdsite;
    }

    /**
     * @return string
     */
    public function getTitleAttachment()
    {
        return $this->titleAttachment;
    }

    /**
     * @param string $titleAttachment
     */
    public function setTitleAttachment($titleAttachment)
    {
        $this->titleAttachment = $titleAttachment;
    }

    /**
     * @return string
     */
    public function getGenerateSitemap()
    {
        return $this->generateSitemap;
    }

    /**
     * @param string $generateSitemap
     */
    public function setGenerateSitemap($generateSitemap)
    {
        $this->generateSitemap = $generateSitemap;
    }

    /**
     * @return bool
     */
    public function getAddHreflang()
    {
        return $this->addHreflang;
    }

    /**
     * @param bool $addHreflang
     */
    public function setAddHreflang($addHreflang)
    {
        $this->addHreflang = $addHreflang;
    }

    /**
     * @return string
     */
    public function getFacebookDefaultImage()
    {
        return $this->facebookDefaultImage;
    }

    /**
     * @param string $facebookDefaultImage
     */
    public function setFacebookDefaultImage($facebookDefaultImage)
    {
        $this->facebookDefaultImage = $facebookDefaultImage;
    }

    /**
     * @return bool
     */
    public function getAddJsonld()
    {
        return $this->addJsonld;
    }

    /**
     * @param bool $addJsonld
     */
    public function setAddJsonld($addJsonld)
    {
        $this->addJsonld = $addJsonld;
    }

    /**
     * @return string
     */
    public function getJsonldType()
    {
        return $this->jsonldType;
    }

    /**
     * @param string $jsonldType
     */
    public function setJsonldType($jsonldType)
    {
        $this->jsonldType = $jsonldType;
    }

    /**
     * @return string
     */
    public function getJsonldTelephone()
    {
        return $this->jsonldTelephone;
    }

    /**
     * @param string $jsonldTelephone
     */
    public function setJsonldTelephone($jsonldTelephone)
    {
        $this->jsonldTelephone = $jsonldTelephone;
    }

    /**
     * @return string
     */
    public function getJsonldTFax()
    {
        return $this->jsonldTFax;
    }

    /**
     * @param string $jsonldTFax
     */
    public function setJsonldTFax($jsonldTFax)
    {
        $this->jsonldTFax = $jsonldTFax;
    }

    /**
     * @return string
     */
    public function getJsonldEmail()
    {
        return $this->jsonldEmail;
    }

    /**
     * @param string $jsonldEmail
     */
    public function setJsonldEmail($jsonldEmail)
    {
        $this->jsonldEmail = $jsonldEmail;
    }

    /**
     * @return string
     */
    public function getJsonldSameAs()
    {
        return $this->jsonldSameAs;
    }

    /**
     * @param string $jsonldSameAs
     */
    public function setJsonldSameAs($jsonldSameAs)
    {
        $this->jsonldSameAs = $jsonldSameAs;
    }

    /**
     * @return string
     */
    public function getJsonldLogo()
    {
        return $this->jsonldLogo;
    }

    /**
     * @param string $jsonldLogo
     */
    public function setJsonldLogo($jsonldLogo)
    {
        $this->jsonldLogo = $jsonldLogo;
    }

    /**
     * @return string
     */
    public function getJsonldAddressLocality()
    {
        return $this->jsonldAddressLocality;
    }

    /**
     * @param string $jsonldAddressLocality
     */
    public function setJsonldAddressLocality($jsonldAddressLocality)
    {
        $this->jsonldAddressLocality = $jsonldAddressLocality;
    }

    /**
     * @return string
     */
    public function getJsonldAddressPostalcode()
    {
        return $this->jsonldAddressPostalcode;
    }

    /**
     * @param string $jsonldAddressPostalcode
     */
    public function setJsonldAddressPostalcode($jsonldAddressPostalcode)
    {
        $this->jsonldAddressPostalcode = $jsonldAddressPostalcode;
    }

    /**
     * @return string
     */
    public function getJsonldAddressStreet()
    {
        return $this->jsonldAddressStreet;
    }

    /**
     * @param string $jsonldAddressStreet
     */
    public function setJsonldAddressStreet($jsonldAddressStreet)
    {
        $this->jsonldAddressStreet = $jsonldAddressStreet;
    }
}
