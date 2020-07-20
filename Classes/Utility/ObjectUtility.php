<?php
declare(strict_types=1);

namespace Mindshape\MindshapeSeo\Utility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2020 Daniel Dorndorf <dorndorf@mindshape.de>, mindshape GmbH
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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * @package Mindshape\MindshapeSeo\Utility
 */
class ObjectUtility
{
    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     */
    protected static $objectManager;

    /**
     * @param string $className
     * @param array $arguments
     * @return object
     * @throws \TYPO3\CMS\Extbase\Object\Exception
     */
    public static function makeInstance(string $className, ...$arguments): object
    {
        if (!static::$objectManager instanceof ObjectManager) {
            /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
            static::$objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        }

        return static::$objectManager->get($className, ...$arguments);
    }
}
