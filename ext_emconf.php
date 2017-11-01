<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'mindshape SEO',
    'description' => 'Extension to manage the SEO of your site',
    'category' => 'be',
    'author' => 'Daniel Dorndorf',
    'author_email' => 'dorndorf@míndshape.de',
    'author_company' => 'mindshape GmbH',
    'state' => 'stable',
    'internal' => '',
    'uploadfolder' => false,
    'clearCacheOnLoad' => true,
    'version' => '1.0.20',
    'createDirs' => 'fileadmin/mindshape_seo',
    'constraints' => [
        'depends' => [
            'typo3' => '7.6.0-7.6.99',
            'php' => '5.5.0',
            'realurl' => '2.2.0',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
