<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'mindshape SEO',
    'description' => 'Extension to manage the SEO of your site',
    'category' => 'be',
    'author' => 'Daniel Dorndorf',
    'author_email' => 'dorndorf@míndshape.de',
    'author_company' => 'mindshape GmbH',
    'state' => 'stable',
    'version' => '4.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-13.4.99',
            'seo' => '12.4.0-13.4.99',
            'php' => '8.1.0-8.3.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
