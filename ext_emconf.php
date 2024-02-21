<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'mindshape SEO',
    'description' => 'Extension to manage the SEO of your site',
    'category' => 'be',
    'author' => 'Daniel Dorndorf',
    'author_email' => 'dorndorf@míndshape.de',
    'author_company' => 'mindshape GmbH',
    'state' => 'stable',
    'version' => '3.0.2',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-12.4.99',
            'seo' => '11.5.0-12.4.99',
            'php' => '8.0.0-8.3.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
