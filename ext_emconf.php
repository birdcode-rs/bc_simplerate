<?php
 
$EM_CONF[$_EXTKEY] = [
    'title' => 'Simple Rate',
    'description' => 'Rate section of the website',
    'category' => 'fe',
    'author' => 'Bird Code',
    'author_email' => 'bird.dev@birdcode.in.rs',
    'state' => 'stable',
    'clearCacheOnLoad' => true,
    'version' => '1.1.0',
    'constraints' => [
        'depends' => [
            'php' => '8.1.0-8.4.99',
            'typo3' => '13.4.5-14.9.99',
            'backend' => '13.4.5-14.9.99',
            'extbase' => '13.4.5-14.9.99',
            'fluid' => '13.4.5-14.9.99',
            'frontend' => '13.4.5-14.9.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
