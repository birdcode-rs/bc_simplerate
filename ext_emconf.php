<?php
 
$EM_CONF[$_EXTKEY] = [
    'title' => 'Simple Rate',
    'description' => 'Rate section of the website',
    'category' => 'fe',
    'author' => 'Bird Code',
    'author_email' => 'bird.dev@birdcode.in.rs',
    'state' => 'stable',
    'clearCacheOnLoad' => true,
    'version' => '0.0.5',
    'constraints' => [
        'depends' => [
            'php' => '7.4.0-8.3.99',
            'typo3' => '12.4.2-13.4.5',
            'backend' => '12.4.2-13.4.5',
            'extbase' => '12.4.2-13.4.5',
            'fluid' => '12.4.2-13.4.5',
            'frontend' => '12.4.2-13.4.5',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
