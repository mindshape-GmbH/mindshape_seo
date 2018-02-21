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
class Redirect extends AbstractEntity
{
    /**
     * @var int
     */
    protected $sourceDomain = '0';

    /**
     * @var string
     */
    protected $sourcePath = '';

    /**
     * @var string
     */
    protected $target = '';

    /**
     * @var string
     */
    protected $httpStatuscode = '';

    /**
     * @var int
     */
    protected $hits = 0;

    /**
     * @var int
     */
    protected $lastHitOn = 0;

    /**
     * @var int
     */
    protected $hidden = 0;

    /**
     * @var int
     */
    protected $deleted = 0;

    /**
     * @var int
     */
    protected $edited = 0;

    /**
     * @var bool
     */
    protected $regex = false;


    /**
     * @return int
     */
    public function getSourceDomain()
    {
        return $this->sourceDomain;
    }

    /**
     * @param int $sourceDomain
     */
    public function setSourceDomain($sourceDomain)
    {
        $this->sourceDomain = $sourceDomain;
    }

    /**
     * @return string
     */
    public function getSourcePath()
    {
        return $this->sourcePath;
    }

    /**
     * @param string $sourcePath
     */
    public function setSourcePath($sourcePath)
    {
        $this->sourcePath = $sourcePath;
    }

    /**
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param string $target
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }

    /**
     * @return string
     */
    public function getHttpStatuscode()
    {
        return $this->httpStatuscode;
    }

    /**
     * @param string $httpStatuscode
     */
    public function setHttpStatuscode($httpStatuscode)
    {
        $this->httpStatuscode = $httpStatuscode;
    }

    /**
     * @return int
     */
    public function getHits()
    {
        return $this->hits;
    }

    /**
     * @param int$hits
     */
    public function setHits($hits)
    {
        $this->hits = $hits;
    }

    /**
     * @return int
     */
    public function getLastHitOn()
    {
        return $this->lastHitOn;
    }

    /**
     * @param int $lastHitOn
     */
    public function setLastHitOn($lastHitOn)
    {
        $this->lastHitOn = $lastHitOn;
    }

    /**
     * @return int
     */
    public function getHidden()
    {
        return $this->hidden;
    }

    /**
     * @param int $hidden
     */
    public function setHidden($hidden)
    {
        $this->hidden = $hidden;
    }

    /**
     * @return int
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param int $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @return int
     */
    public function getEdited()
    {
        return $this->edited;
    }

    /**
     * @param int $edited
     */
    public function setEdited($edited)
    {
        $this->edited = $edited;
    }

    /**
     * @return bool
     */
    public function getRegex()
    {
        return $this->regex;
    }

    /**
     * @param bool $regex
     */
    public function setRegex(bool $regex)
    {
        $this->regex = $regex;
    }
}
