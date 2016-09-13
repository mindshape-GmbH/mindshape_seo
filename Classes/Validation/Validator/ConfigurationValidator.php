<?php
namespace Mindshape\MindshapeSeo\Validation\Validator;

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
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

/**
 * @package mindshape_seo
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class ConfigurationValidator extends AbstractValidator
{
    const FACEBOOK_DEFAULT_IMAGE_MIN_HEIGHT = 200;
    const FACEBOOK_DEFAULT_IMAGE_MIN_WIDTH = 200;

    /**
     * @param \Mindshape\MindshapeSeo\Domain\Model\Configuration $value
     * @return void
     */
    public function isValid($value)
    {
        if ($value->getFacebookDefaultImage() instanceof FileReference) {
            $imageSize = getimagesize(PATH_site . '/' . $value->getFacebookDefaultImage()->getOriginalResource()->getPublicUrl());

            if (
                $imageSize[1] < self::FACEBOOK_DEFAULT_IMAGE_MIN_HEIGHT &&
                $imageSize[0] < self::FACEBOOK_DEFAULT_IMAGE_MIN_WIDTH
            ) {
                $this->addError(
                    LocalizationUtility::translate(
                        'tx_mindshapeseo_validation.facebook_default_image_too_small',
                        'mindshape_seo'
                    ),
                    1473748428
                );
            }
        }
    }
}
