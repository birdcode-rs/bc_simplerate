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
        ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );
    ExtensionUtility::configurePlugin(
        'BcSimplerate',
        'Ratings',
        [
            RateController::class => 'ratings',
        ],
        [
            RateController::class => 'ratings',
        ],
        ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );
};

$boot();
unset($boot);


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup(trim('
    plugin {
        tx_bcsimplerate_ratings.view.pluginNamespace = tx_bcsimplerate_pi1
    }
'));
$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['bcsr'] = ['BirdCode\BcSimplerate\ViewHelpers']; 