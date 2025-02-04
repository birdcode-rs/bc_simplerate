<?php
 
$EM_CONF[$_EXTKEY] = [
    'title' => 'Simple Rate',
    'description' => 'Rate section of the website',
    'category' => 'fe',
    'author' => 'Bird Dev',
    'author_email' => 'bird.dev@birdcode.in.rs',
    'state' => 'stable',
    'clearCacheOnLoad' => true,
    'version' => '0.0.1',
    'constraints' => [
        'depends' => [
            'php' => '7.4.0-8.3.99',
            'typo3' => '12.4.2-12.9.99',
            'backend' => '12.4.2-12.9.99',
            'extbase' => '12.4.2-12.9.99',
            'fluid' => '12.4.2-12.9.99',
            'frontend' => '12.4.2-12.9.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
