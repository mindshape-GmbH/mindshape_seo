<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'mindshape SEO',
    'description' => 'Extension to manage the SEO of your site',
    'category' => 'be',
    'author' => 'Daniel Dorndorf',
    'author_email' => 'dorndorf@míndshape.de',
    'author_company' => 'mindshape GmbH',
    'state' => 'stable',
    'clearCacheOnLoad' => true,
    'version' => '2.0.2',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-11.4.99',
            'seo' => '10.4.0-11.4.99',
            'php' => '7.2.0-7.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
