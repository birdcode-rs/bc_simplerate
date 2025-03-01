<?php

declare(strict_types=1);

use BirdCode\BcSimplerate\Controller\AdministrationController;
use BirdCode\BcSimplerate\Domain\Model\Dto\EmConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

$bcSimplerateConfig = GeneralUtility::makeInstance(
    EmConfiguration::class
);

/**
 * Definitions for the backend module provided by EXT:bc_simplerate
 */
if ($bcSimplerateConfig->getshowAdminModule()) {
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
                    'index',
                    'ratingResults'
                ],
            ],
        ],
    ];
}
