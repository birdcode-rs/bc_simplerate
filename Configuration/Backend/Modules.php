<?php

use BirdCode\BcSimplerate\Controller\AdministrationController;

/**
 * Definitions for the backend module provided by EXT:bc_simplerate
 */
return [
    'bc_simplerate' => [
        'parent' => 'web',
        'position' => ['after' => 'web_info'],
        'access' => 'user',
        'workspaces' => 'live',
        'path' => '/module/page/bc_simplerate',
		'iconIdentifier' => 'ext-bc-simplerate-module-administration',
        'labels' => 'LLL:EXT:bc_simplerate/Resources/Private/Language/locallang_modadministration.xlf',
        'extensionName' => 'BcSimplerate',
        'controllerActions' => [
            AdministrationController::class => [
                'index'
            ],
        ],
    ],
];
