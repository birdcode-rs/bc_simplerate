<?php
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') or exit();


 /***************
 * Plugin
 */
ExtensionManagementUtility::registerPlugin(
    'BcSimplerate',
    'Pi1',
    'Rate page section'
);

/*
$pluginSignature = str_replace('_', '', 'bc_simplerate').'_rate';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';

ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:bc_simplerate/Configuration/FlexForms/flexform_pi1.xml');
*/
