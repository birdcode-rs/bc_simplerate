<?php
declare(strict_types=1);

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
use BirdCode\BcSimplerate\Utility\TemplateLayout;
use TYPO3\CMS\Backend\Utility\BackendUtility as BackendUtilityCore;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Userfunc to render alternative label for media elements
 */
class ItemsProcFunc
{
    protected TemplateLayout $templateLayoutsUtility;

    public function __construct(
        TemplateLayout $templateLayout
    ) {
        $this->templateLayoutsUtility = $templateLayout;
    }

    /**
     * Itemsproc function to extend the selection of templateLayouts in the plugin
     *
     * @param array &$config configuration array
     */
    public function user_templateLayout(array &$config): void
    {
        $pageId = 0;

        $currentColPos = $config['flexParentDatabaseRow']['colPos'] ?? null;
        if ($currentColPos === null) {
            return;
        }
        $pageId = $this->getPageId($config['flexParentDatabaseRow']['pid']);
 
        if ($pageId > 0) {
            $templateLayouts = $this->templateLayoutsUtility->getAvailableTemplateLayouts($pageId);

            $templateLayouts = $this->reduceTemplateLayouts($templateLayouts, $currentColPos);
 
            foreach ($templateLayouts as $layout) {
                $additionalLayout = [
                    htmlspecialchars($this->getLanguageService()->sL($layout[0])),
                    $layout[1],
                ];
                array_push($config['items'], $additionalLayout);
            }
        }
    }

    /**
     * Reduce the template layouts by the ones that are not allowed in given colPos
     *
     * @param array $templateLayouts
     * @param int $currentColPos
     */
    protected function reduceTemplateLayouts($templateLayouts, $currentColPos): array
    {
        $currentColPos = (int)$currentColPos;
        $restrictions = [];
        $allLayouts = [];
        foreach ($templateLayouts as $key => $layout) {
            if (is_array($layout[0])) {
                if (isset($layout[0]['allowedColPos']) && str_ends_with((string)$layout[1], '.')) {
                    $layoutKey = substr($layout[1], 0, -1);
                    $restrictions[$layoutKey] = GeneralUtility::intExplode(',', $layout[0]['allowedColPos'], true);
                }
            } else {
                $allLayouts[$key] = $layout;
            }
        }
        foreach ($restrictions as $restrictedIdentifier => $restrictedColPosList) {
            if (!in_array($currentColPos, $restrictedColPosList, true)) {
                unset($allLayouts[$restrictedIdentifier]);
            }
        }

        return $allLayouts;
    }

    /**
     * Itemsproc function to extend the selection of recordsFromTable in the plugin
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
