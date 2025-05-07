<?php

defined('TYPO3') or die;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use BirdCode\BcSimplerate\Controller\RateController;

$boot = static function (): void {
    ExtensionUtility::configurePlugin(
        'BcSimplerate',
        'Pi1',
        [
            RateController::class => 'rateIt',
        ], 
        [
            RateController::class => 'rateIt',
        ],
        ExtensionUtility::PLUGIN_TYPE_PLUGIN
    );
};

$boot();
unset($boot);

$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['bcsr'] = ['BirdCode\BcSimplerate\ViewHelpers']; 
