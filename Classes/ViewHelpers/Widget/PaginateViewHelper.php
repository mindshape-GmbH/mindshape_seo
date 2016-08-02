<?php
namespace Mindshape\MindshapeSeo\ViewHelpers\Widget;

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

use Mindshape\MindshapeSeo\ViewHelpers\Widget\Controller\PaginateController;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Fluid\Core\Widget\AbstractWidgetViewHelper;

/**
 * @package mindshape_seo
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class PaginateViewHelper extends AbstractWidgetViewHelper
{
    /**
     * @var \Mindshape\MindshapeSeo\ViewHelpers\Widget\Controller\PaginateController
     */
    protected $controller;

    /**
     * @param \Mindshape\MindshapeSeo\ViewHelpers\Widget\Controller\PaginateController $controller
     */
    public function injectPaginateController(PaginateController $controller)
    {
        $this->controller = $controller;
    }

    /**
     * @param QueryResultInterface|ObjectStorage|array $objects
     * @param string $as
     * @param array $configuration
     * @return string
     */
    public function render($objects, $as, array $configuration = array('itemsPerPage' => 10, 'insertAbove' => false, 'insertBelow' => true, 'maximumNumberOfLinks' => 99))
    {
        return $this->initiateSubRequest();
    }
}
