<?php
 
$EM_CONF[$_EXTKEY] = [
    'title' => 'Simple Rate',
    'description' => 'Rate section of the website',
    'category' => 'fe',
    'author' => 'Bird Dev',
    'author_email' => 'bird.dev@birdcode.in.rs',
    'state' => 'stable',
    'clearCacheOnLoad' => true,
    'version' => '0.0.2',
    'constraints' => [
        'depends' => [
            'php' => '7.4.0-8.3.99',
            'typo3' => '12.4.2-13.4.4',
            'backend' => '12.4.2-13.4.4',
            'extbase' => '12.4.2-13.4.4',
            'fluid' => '12.4.2-13.4.4',
            'frontend' => '12.4.2-13.4.4',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
