<?php
namespace Mindshape\MindshapeSeo\ViewHelpers\Be\Menus;

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

use Mindshape\MindshapeSeo\Domain\Model\Configuration;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * @package mindshape_seo
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class DomainActionMenuItemViewHelper extends AbstractTagBasedViewHelper
{
    /**
     * @var string
     */
    protected $tagName = 'option';

    /**
     * Renders an ActionMenu option tag
     *
     * @param string $label
     * @param string $controller
     * @param string $action
     * @param string $domain
     * @return string the rendered option tag
     */
    public function render($label, $controller, $action, $domain = Configuration::DEFAULT_DOMAIN)
    {
        $uriBuilder = $this->controllerContext->getUriBuilder();
        $uri = $uriBuilder->reset()->uriFor($action, array('domain' => $domain), $controller);
        $this->tag->addAttribute('value', $uri);
        $currentRequest = $this->controllerContext->getRequest();
        $currentController = $currentRequest->getControllerName();
        $currentAction = $currentRequest->getControllerActionName();
        $currentArguments = $currentRequest->getArguments();

        if (
            $action === $currentAction &&
            $controller === $currentController &&
            $domain === $currentArguments['domain']
        ) {
            $this->tag->addAttribute('selected', 'selected');
        }

        $this->tag->setContent($label);

        return $this->tag->render();
    }
}
