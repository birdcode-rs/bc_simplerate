<?php

defined('TYPO3') or die;

$boot = static function (): void {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'BcSimplerate',
        'Pi1',
        [
            \Bc\BcSimplerate\Controller\RateController::class => 'rateIt',
        ], 
        [
            \Bc\BcSimplerate\Controller\RateController::class => 'rateIt',
        ],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_PLUGIN
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('
        @import \'EXT:EXT:bc_simplerate/Configuration/TSconfig/includePageTSconfig.tsconfig\'
    ');
};

$boot();
unset($boot);

$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['bcsr'] = ['Bc\BcSimplerate\ViewHelpers'];
