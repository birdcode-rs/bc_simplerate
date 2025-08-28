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

use BirdCode\BcSimplerate\Utility\TemplateLayout;
use TYPO3\CMS\Backend\Preview\StandardContentPreviewRenderer;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Utility\BackendUtility as BackendUtilityCore;
use TYPO3\CMS\Backend\View\BackendLayout\Grid\GridColumnItem;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Imaging\IconSize;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Type\Bitmask\Permission;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Render selected options of plugin in Web>Page module
 */
class PluginPreviewRenderer extends StandardContentPreviewRenderer
{
    protected const LLPATH = 'LLL:EXT:bc_simplerate/Resources/Private/Language/locallang_be.xlf:';
    protected const SETTINGS_IN_PREVIEW = 7;

    /**
     * Table information
     */
    public array $tableData = [];

    /**
     * Flexform information
     */
    public array $flexformData = [];
    protected IconFactory $iconFactory;
    protected TemplateLayout $templateLayoutsUtility;
    private int $pageId = 0;

    public function __construct()
    {
        $this->templateLayoutsUtility = GeneralUtility::makeInstance(TemplateLayout::class);
        $this->iconFactory = GeneralUtility::makeInstance(IconFactory::class);
    }
    
    /**
     * Method renderPageModulePreviewContent
     *
     * @param GridColumnItem $item
     *
     * @return string
     */
    public function renderPageModulePreviewContent(GridColumnItem $item): string
    {
        $this->pageId = $item->getContext()->getPageId();
        $row = $item->getRecord();
        $actionTranslationKey = $result = '';
        $header = '<strong>' . htmlspecialchars($this->getLanguageService()->sL(self::LLPATH . 'bcsimplerate_ratings_title')) . '</strong>';
        $this->tableData = [];
        $flexforms = GeneralUtility::xml2array((string)$row['pi_flexform']);
        if (is_string($flexforms)) {
            return 'ERROR: ' . htmlspecialchars($flexforms);
        }
        $this->flexformData = (array)$flexforms;
 
        if (!empty($this->flexformData)) {
            switch ($row['CType']) {
                case 'bcsimplerate_ratings':
                    $this->getDisplayMode();
                    $this->getResultType();
                    $this->getStartingPoint();
                    $this->getTemplateLayoutSettings($row['pid']);
                    $this->getTopRatedSetting();
                    $this->getOrderSettings();
                    $this->getOffsetLimitSettings();
                    break;

                default:
                    $this->getStartingPoint();
                    $this->getTemplateLayoutSettings($row['pid']);
            }

            if ($hooks = $GLOBALS['TYPO3_CONF_VARS']['EXT']['bc_simplerate'][\BirdCode\BcSimplerate\Hooks\PluginPreviewRenderer::class]['extensionSummary'] ?? []) {
                $params['action'] = $actionTranslationKey;
                $params['item'] = $item;
                foreach ($hooks as $reference) {
                    GeneralUtility::callUserFunction($reference, $params, $this);
                }
            }
 
            $result = $this->renderSettingsAsTable($header, $row['uid'] ?? 0);
        }

        return $result;
    }
     
    /**
     * Method getRecordData
     *
     * @param int $id 
     * @param string $table
     *
     * @return string
     */
    public function getRecordData(int $id, string $table = 'pages'): string
    {
        $record = BackendUtilityCore::getRecord($table, $id);

        if (is_array($record)) {
            $iconSize = (new Typo3Version())->getMajorVersion() >= 13 ? IconSize::SMALL : 'small';
            $data = '<span data-toggle="tooltip" data-placement="top" data-title="id=' . $record['uid'] . '">'
                . $this->iconFactory->getIconForRecord($table, $record, $iconSize)->render()
                . '</span> ';
            $content = BackendUtilityCore::wrapClickMenuOnIcon(
                $data,
                $table,
                $record['uid'],
                true,
                $record
            );

            $linkTitle = htmlspecialchars(BackendUtilityCore::getRecordTitle($table, $record));

            if ($table === 'pages') {
                $id = $record['uid'];
                $link = htmlspecialchars($this->getEditLink($record, $this->pageId));
                $switchLabel = $this->getLanguageService()->sL(self::LLPATH . 'pagemodule.switchToPage');
                $content .= ' <a href="#" data-toggle="tooltip" data-placement="top" data-title="' . $switchLabel . '" onclick=\'top.jump("' . $link . '", "web_layout", "web", ' . $id . ');return false\'>' . $linkTitle . '</a>';
            } else {
                $content .= $linkTitle;
            }
        } else {
            $text = sprintf(
                $this->getLanguageService()->sL(self::LLPATH . 'pagemodule.recordNotAvailable'),
                $id
            );
            $content = $this->generateCallout($text);
        }

        return $content;
    }
    
    /**
     * Method getOrderSettings
     *
     * @return void
     */
    public function getOrderSettings(): void
    {
        $orderField = $this->getFieldFromFlexform('settings.orderBy');
        if (!empty($orderField)) {
            $text = $this->getLanguageService()->sL(self::LLPATH . 'flexforms_general.orderBy.' . $orderField);

            // Order direction (asc, desc)
            $orderDirection = $this->getOrderDirectionSetting();
            if ($orderDirection) {
                $text .= ', ' . strtolower($orderDirection);
            }

            // Top news first
            $topRated = $this->getTopRatedSetting();
            if ($topRated) {
                $text .= '<br />' . $topRated;
            }

            $this->tableData[] = [
                $this->getLanguageService()->sL(self::LLPATH . 'flexforms_general.orderBy'),
                $text,
            ];
        }
    }
    
    /**
     * Method getOrderDirectionSetting
     *
     * @return string
     */
    public function getOrderDirectionSetting(): string
    {
        $text = '';

        $orderDirection = $this->getFieldFromFlexform('settings.orderDirection');
        if (!empty($orderDirection)) {
            $text = $this->getLanguageService()->sL(self::LLPATH . 'flexforms_general.orderDirection.' . $orderDirection);
        }

        return $text;
    }

     /**
     * Method getDisplayMode
     *
     * @return string
     */
    public function getDisplayMode()
    {
        
        $text = $this->getLanguageService()->sL(self::LLPATH . 'flexforms_general.displayMode.0');

        $displayMode = $this->getFieldFromFlexform('settings.displayMode', 'sDEF');
        if (!empty($displayMode)) {
            $text = $this->getLanguageService()->sL(self::LLPATH . 'flexforms_general.displayMode.' . $displayMode);
        }

        $this->tableData[] = [
            $this->getLanguageService()->sL(self::LLPATH . 'flexforms_general.displayMode'),
            $text,
        ];
    }

     /**
     * Method getResultType
     *
     * @return string
     */
    public function getResultType()
    {
       $text = $this->getLanguageService()->sL(self::LLPATH . 'flexforms_general.resultType.0');

        $resultType = $this->getFieldFromFlexform('settings.resultType', 'sDEF');
        if (!empty($resultType)) {
            $text = $this->getLanguageService()->sL(self::LLPATH . 'flexforms_general.resultType.' . $resultType);
        }

        $this->tableData[] = [
            $this->getLanguageService()->sL(self::LLPATH . 'flexforms_general.resultType'),
            $text,
        ];
    }
    
    /**
     * Method getTopRatedSetting
     *
     * @return string
     */
    public function getTopRatedSetting(): string
    {
        $text = '';

        $topNewsSetting = (int)$this->getFieldFromFlexform('settings.topRated', 'sDEF');
        if ($topNewsSetting === 1) {
            $text = $this->getLanguageService()->sL(self::LLPATH . 'flexforms_general.topRated');
        }
 
        return $text;
    }
     
    /**
     * Method getOffsetLimitSettings
     *
     * @return void
     */
    public function getOffsetLimitSettings(): void
    { 
        $limit = $this->getFieldFromFlexform('settings.limit', 'sDEF');
        $hidePagination = $this->getFieldFromFlexform('settings.hidePagination', 'sDEF');
 
        if ($limit) {
            $this->tableData[] = [
                $this->getLanguageService()->sL(self::LLPATH . 'flexforms_general.limit'),
                $limit,
            ];
        }
        if ($hidePagination) {
            $this->tableData[] = [
                $this->getLanguageService()->sL(self::LLPATH . 'flexforms_general.hidePagination'),
                '<i class="fa fa-check"></i>',
            ];
        }
    }
     
    /**
     * Method getTemplateLayoutSettings
     *
     * @param int $pageUid
     *
     * @return void
     */
    public function getTemplateLayoutSettings(int $pageUid): void
    {
        $title = '';
        $field = $this->getFieldFromFlexform('settings.templateLayout', 'template');

        // Find correct title by looping over all options
        if (!empty($field)) {
            $layouts = $this->templateLayoutsUtility->getAvailableTemplateLayouts($pageUid);
            foreach ($layouts as $layout) {
                if ((string)$layout[1] === $field) {
                    $title = $layout[0];
                }
            }
        }

        if (!empty($title)) {
            $this->tableData[] = [
                $this->getLanguageService()->sL(self::LLPATH . 'flexforms_template.templateLayout'),
                $this->getLanguageService()->sL($title),
            ];
        }
    }
     
    /**
     * Method getStartingPoint
     *
     * @return void
     */
    public function getStartingPoint(): void
    {
        $value = $this->getFieldFromFlexform('settings.storage');

        if (!empty($value)) {
            $pageIds = GeneralUtility::intExplode(',', $value, true);
            $pagesOut = [];

            foreach ($pageIds as $id) {
                $pagesOut[] = $this->getRecordData($id, 'pages');
            }

            $this->tableData[] = [
                $this->getLanguageService()->sL('LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.startingpoint'),
                implode(', ', $pagesOut),
            ];
        }
    }
     
    /**
     * Method generateCallout
     *
     * @param string $text 
     *
     * @return string
     */
    protected function generateCallout(string $text): string
    {
        return '<div class="alert alert-warning">' . htmlspecialchars($text) . '</div>';
    }
    
    /**
     * Method renderSettingsAsTable
     *
     * @param string $header
     * @param int $recordUid
     *
     * @return string
     */
    protected function renderSettingsAsTable(string $header = '', int $recordUid = 0): string
    {
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class); 
        $pageRenderer->addCssFile('EXT:bc_simplerate/Resources/Public/Css/Backend/PageLayoutView.css');
  
        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName('EXT:bc_simplerate/Resources/Private/Backend/PageLayoutView.html'));
        $view->assignMultiple([
            'header' => $header,
            'rows' => [
                'above' => array_slice($this->tableData, 0, self::SETTINGS_IN_PREVIEW),
                'below' => array_slice($this->tableData, self::SETTINGS_IN_PREVIEW),
            ],
            'id' => $recordUid,
        ]);

        return $view->render();
    }
    
    /**
     * Method getFieldFromFlexform
     *
     * @param string $key
     * @param string $sheet
     *
     * @return string
     */
    public function getFieldFromFlexform(string $key, string $sheet = 'sDEF'): ?string
    {
        $flexform = $this->flexformData;
        if (isset($flexform['data'])) {
            $flexform = $flexform['data'];
            if (isset($flexform[$sheet]['lDEF'][$key]['vDEF'])
            ) {
                return $flexform[$sheet]['lDEF'][$key]['vDEF'];
            }
        }

        return null;
    }
    
    /**
     * Method getEditLink
     *
     * @param array $row
     * @param int $currentPageUid
     *
     * @return string
     */
    protected function getEditLink(array $row, int $currentPageUid): string
    {
        $editLink = '';
        $localCalcPerms = $GLOBALS['BE_USER']->calcPerms(BackendUtilityCore::getRecord('pages', $row['uid']));
        $permsEdit = $localCalcPerms & Permission::PAGE_EDIT;
        if ($permsEdit) {
            $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
            $returnUrl = $uriBuilder->buildUriFromRoute('web_layout', ['id' => $currentPageUid]);
            $editLink = $uriBuilder->buildUriFromRoute('web_layout', [
                'id' => $row['uid'],
                'returnUrl' => $returnUrl,
            ]);
        }
        return (string)$editLink;
    }
}
