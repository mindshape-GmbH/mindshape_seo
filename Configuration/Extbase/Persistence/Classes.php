<?php
declare(strict_types = 1);


return [
    Mindshape\MindshapeSeo\Domain\Model\FileReference::class => [
        'tableName' => 'sys_file_reference',
        'properties' => [
            'originalFileIdentifier' => [
                'fieldName' => 'uid_local'
            ],
        ],
    ],
];