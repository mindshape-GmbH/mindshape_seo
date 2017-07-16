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
];
