<?php
namespace Mindshape\MindshapeSeo\Hook;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Daniel Dorndorf <dorndorf@mindshape.de>
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

use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * @package mindshape_seo
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class TypoLinkHook
{
    /**
     * @var \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected $databaseConnection;

    /**
     * @return \Mindshape\MindshapeSeo\Hook\TypoLinkHook
     */
    public function __construct()
    {
        $this->databaseConnection = $GLOBALS['TYPO3_DB'];
    }

    /**
     * @param array $parameters
     * @param \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $contentObjectRenderer
     * @return void
     */
    public function postProcessEncodedUrl(array &$parameters, ContentObjectRenderer $contentObjectRenderer) {
        $currentSysLanguageUid = (int) $GLOBALS['TSFE']->sys_language_uid;

        if (array_key_exists('additionalParams', $parameters['conf'])) {
            preg_match('#L=(\d+)#i', $parameters['conf']['additionalParams'], $matches);

            if (0 < count($matches)) {
                $linkLanguageParameter = (int) $matches[1];

                if ($currentSysLanguageUid !== $linkLanguageParameter) {
                    $linkLanguage = $this->databaseConnection->exec_SELECTgetSingleRow(
                        '*',
                        'sys_language',
                        'uid = ' . $linkLanguageParameter
                    );

                    if (true === is_array($linkLanguage)) {
                        $hreflangTag = 'hreflang="' . $linkLanguage['language_isocode'] . '"';

                        $parameters['finalTagParts']['aTagParams'] .= '' !== $parameters['finalTagParts']['aTagParams']
                            ? ' ' . $hreflangTag
                            : $hreflangTag;

                        $parameters['finalTag'] = preg_replace('#(<a\b[^><]*)>#i', '$1 ' . $hreflangTag . '>', $parameters['finalTag']);
                    }
                }
            }
        }
    }
}
