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
    'version' => '1.1.4',
    'createDirs' => 'fileadmin/mindshape_seo',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.0-8.7.99',
            'php' => '7.0.0',
            'realurl' => '2.2.0',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
