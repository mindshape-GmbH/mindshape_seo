<?php

$EM_CONF[$_EXTKEY] = array(
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
    'version' => '1.0.11',
    'createDirs' => 'fileadmin/mindshape_seo',
    'constraints' => array(
        'depends' => array(
            'typo3' => '7.6.0-7.6.99',
            'php' => '5.5.0',
            'realurl' => '1.10.0-0.0.0',
        ),
        'conflicts' => array(),
        'suggests' => array(),
    ),
);
