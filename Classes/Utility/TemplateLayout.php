<?php

/***
 *
 * This file is part of the "BC Simplerate" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2025 Bird Dev <bird.dev@birdcode.in.rs>, Bird Code
 *
 ***/

namespace BirdCode\BcSimplerate\Utility;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * TemplateLayout utility class
 */
class TemplateLayout implements SingletonInterface
{
    /**
     * Get available template layouts for a certain page
     *
     * @param int $pageUid
     */
    public function getAvailableTemplateLayouts($pageUid): array
    {
        $templateLayouts = [];

        // Check if the layouts are extended by ext_tables
        if (isset($GLOBALS['TYPO3_CONF_VARS']['EXT']['bc_simplerate']['templateLayouts'])
            && is_array($GLOBALS['TYPO3_CONF_VARS']['EXT']['bc_simplerate']['templateLayouts'])
        ) {
            $templateLayouts = $GLOBALS['TYPO3_CONF_VARS']['EXT']['bc_simplerate']['templateLayouts'];
        }
 
        // Add TsConfig values
        foreach ($this->getTemplateLayoutsFromTsConfig($pageUid) as $templateKey => $title) {
            if (is_string($title) && str_starts_with($title, '--div--')) {
                $optGroupParts = GeneralUtility::trimExplode(',', $title, true, 2);
                $title = $optGroupParts[1];
                $templateKey = $optGroupParts[0];
            }
            $templateLayouts[] = [$title, $templateKey];
        }

        return $templateLayouts;
    }

    /**
     * Get template layouts defined in TsConfig
     *
     * @param $pageUid
     */
    protected function getTemplateLayoutsFromTsConfig(int $pageUid): array
    {
        $templateLayouts = [];
        $pagesTsConfig = BackendUtility::getPagesTSconfig($pageUid);
        if (isset($pagesTsConfig['tx_bcsimplerate.']['templateLayouts.']) && is_array($pagesTsConfig['tx_bcsimplerate.']['templateLayouts.'])) {
            $templateLayouts = $pagesTsConfig['tx_bcsimplerate.']['templateLayouts.'];
        }
        return $templateLayouts;
    }
}
