<?php

return [
    'MindshapeSeoAjaxHandler::savePage' => [
        'path' => '/MindshapeSeoAjaxHandler/savePage',
        'target' => \Mindshape\MindshapeSeo\Handler\AjaxHandler::class . '::savePage',
    ],
    'MindshapeSeoAjaxHandler::savePageRobots' => [
        'path' => '/MindshapeSeoAjaxHandler/savePageRobots',
        'target' => \Mindshape\MindshapeSeo\Handler\AjaxHandler::class . '::savePageRobots',
    ],
    'MindshapeSeoAjaxHandler::deleteConfiguration' => [
        'path' => '/MindshapeSeoAjaxHandler/deleteConfiguration',
        'target' => \Mindshape\MindshapeSeo\Handler\AjaxHandler::class . '::deleteConfiguration',
    ],
    'MindshapeSeoAjaxHandler::deleteRedirect' => [
        'path' => '/MindshapeSeoAjaxHandler/deleteRedirect',
        'target' => \Mindshape\MindshapeSeo\Handler\AjaxHandler::class . '::deleteRedirect',
    ],
    'MindshapeSeoAjaxHandler::hideRedirect' => [
        'path' => '/MindshapeSeoAjaxHandler/hideRedirect',
        'target' => \Mindshape\MindshapeSeo\Handler\AjaxHandler::class . '::hideRedirect',
    ],
    'MindshapeSeoAjaxHandler::unhideRedirect' => [
        'path' => '/MindshapeSeoAjaxHandler/unhideRedirect',
        'target' => \Mindshape\MindshapeSeo\Handler\AjaxHandler::class . '::unhideRedirect',
    ],
    'MindshapeSeoAjaxHandler::facebookScrape' => [
        'path' => '/MindshapeSeoAjaxHandler/facebookScrape',
        'target' => \Mindshape\MindshapeSeo\Handler\AjaxHandler::class . '::facebookScrape',
    ],
];
