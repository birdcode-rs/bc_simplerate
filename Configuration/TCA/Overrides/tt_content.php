<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') or exit();


 /***************
 * Plugins
 */
(static function (): void {
    ExtensionUtility::registerPlugin(
        'BcSimplerate',
        'Pi1',
        'Rate it',
        'content-extension'
    );
    ExtensionUtility::registerPlugin(
        'BcSimplerate',
        'Ratings',
        'Show rating results',
        'content-extension'
    );
})();


$pluginConfig = ['bcsimplerate_ratings' => 'bcSimplerateRatings'];
foreach ($pluginConfig as $contentTypeName => $flexformFileName) {
    ExtensionManagementUtility::addPiFlexFormValue(
        '*',
        'FILE:EXT:bc_simplerate/Configuration/FlexForms/flexform_' . $flexformFileName . '.xml',
        $contentTypeName
    );
    $GLOBALS['TCA']['tt_content']['types'][$contentTypeName]['showitem'] = '
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
        --palette--;;general,
        --palette--;;headers,
    --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.plugin,
        pi_flexform,
    --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,
        --palette--;;frames,
        --palette--;;appearanceLinks,
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
        --palette--;;language,
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
        --palette--;;hidden,
        --palette--;;access,
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,
        categories,
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,
        rowDescription,
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended,
    ';
}