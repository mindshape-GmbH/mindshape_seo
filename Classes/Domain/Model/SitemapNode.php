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

use TYPO3\CMS\Extbase\DomainObject\AbstractValueObject;

/**
 * @package mindshape_seo
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class SitemapNode extends AbstractValueObject
{
    const CHANGE_FREQUENCY_ALWAYS = 'always';
    const CHANGE_FREQUENCY_HOULRY = 'hourly';
    const CHANGE_FREQUENCY_DAILY = 'daily';
    const CHANGE_FREQUENCY_WEEKLY = 'weekly';
    const CHANGE_FREQUENCY_MONTHLY = 'monthly';
    const CHANGE_FREQUENCY_YEARLY = 'yearly';
    const CHANGE_FREQUENCY_NEVER = 'never';

    const DEFAULT_PRIORITY = 0.5;
    const DEFAULT_CHANGE_FREQUENCY = self::CHANGE_FREQUENCY_WEEKLY;

    /**
     * @var string
     */
    protected $url = '';

    /**
     * @var \DateTime
     */
    protected $lastModification;

    /**
     * @var string
     */
    protected $changeFrequency = '';

    /**
     * @var double
     */
    protected $priority = 0.0;

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return void
     */
    public function setUrl(string $url)
    {
        $this->url = $url;
    }

    /**
     * @return \DateTime
     */
    public function getLastModification(): \DateTime
    {
        return $this->lastModification;
    }

    /**
     * @param \DateTime $lastModification
     * @return void
     */
    public function setLastModification(\DateTime $lastModification)
    {
        $this->lastModification = $lastModification;
    }

    /**
     * @return string
     */
    public function getChangeFrequency(): string
    {
        return $this->changeFrequency;
    }

    /**
     * @param string $changeFrequency
     * @return void
     */
    public function setChangeFrequency(string $changeFrequency)
    {
        $this->changeFrequency = $changeFrequency;
    }

    /**
     * @return float
     */
    public function getPriority(): float
    {
        return $this->priority;
    }

    /**
     * @param float $priority
     * @return void
     */
    public function setPriority(float $priority)
    {
        $this->priority = $priority;
    }
}
