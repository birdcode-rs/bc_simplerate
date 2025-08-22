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


namespace BirdCode\BcSimplerate\Hooks;

use BirdCode\BcSimplerate\Domain\Model\Dto\EmConfiguration;
use TYPO3\CMS\Backend\Utility\BackendUtility as BackendUtilityCore;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Userfunc to render alternative label for media elements
 */
class ItemsProcFunc
{
 
    /**
     * Itemsproc function to extend the selection of templateLayouts in the plugin
     *
     * @param array &$config configuration array
     */
    public function user_recordsFromTable(array &$config): void
    {   
        $emConf = GeneralUtility::makeInstance(EmConfiguration::class);
        $fromTable = $emConf->getFeatureGetRecordsFieldArray();
        $fieldname = 'title';

        if (!empty($fromTable)) {
            foreach ($fromTable as $tableName => $fieldName) {
                $tcaConfig = $GLOBALS['TCA'][$tableName];
                if (!empty($tcaConfig)) {
                    // Access the 'ctrl' section of the TCA configuration
                    $ctrlData = $tcaConfig['ctrl'];

                    if (isset($ctrlData[$fieldname])) {
                        $additionalFromTable = [
                            'label' => $ctrlData[$fieldname],
                            'value' => $tableName
                        ];
                        array_push($config['items'], $additionalFromTable);
                    }
                }  
            }
        }
    }
 
    /**
     * Get all languages
     */
    protected function getAllLanguages(): array
    {
        $siteLanguages = [];
        foreach (GeneralUtility::makeInstance(SiteFinder::class)->getAllSites() as $site) {
            foreach ($site->getAllLanguages() as $languageId => $language) {
                if (!isset($siteLanguages[$languageId])) {
                    $siteLanguages[$languageId] = [
                        'uid' => $languageId,
                        'title' => $language->getTitle(),
                    ];
                }
            }
        }
        return $siteLanguages;
    }

    /**
     * Get tt_content record
     *
     * @param int $uid
     */
    protected function getContentElementRow($uid): ?array
    {
        return BackendUtilityCore::getRecord('tt_content', $uid);
    }

    /**
     * Get page id, if negative, then it is a "after record"
     *
     * @param int $pid
     */
    protected function getPageId($pid): int
    {
        $pid = (int)$pid;

        if ($pid > 0) {
            return $pid;
        }

        $row = BackendUtilityCore::getRecord('tt_content', abs($pid), 'uid,pid');
        return $row['pid'];
    }

    /**
     * Returns LanguageService
     */
    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
