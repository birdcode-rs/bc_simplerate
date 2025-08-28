<?php
use BirdCode\BcSimplerate\Hooks\PluginPreviewRenderer;
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
        'LLL:EXT:bc_simplerate/Resources/Private/Language/locallang_be.xlf:tx_bcsimplerate_pi1.title',
        'ext-bc-simplerate-plugin-pi1'
    );
    ExtensionUtility::registerPlugin(
        'BcSimplerate',
        'Ratings',
        'LLL:EXT:bc_simplerate/Resources/Private/Language/locallang_be.xlf:bcsimplerate_ratings.title',
        'ext-bc-simplerate-plugin-ratings'
    );
})();



$pluginConfig = ['bcsimplerate_ratings' => 'bcSimplerateRatings'];
foreach ($pluginConfig as $contentTypeName => $flexformFileName) {
    $GLOBALS['TCA']['tt_content']['types'][$contentTypeName]['previewRenderer'] = PluginPreviewRenderer::class;
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