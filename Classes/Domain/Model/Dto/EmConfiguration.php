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
namespace BirdCode\BcSimplerate\Domain\Model\Dto;

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Extension Manager configuration
 */
class EmConfiguration
{
    /**
     * Fill the properties properly
     *
     * @param array $configuration em configuration
     */
    public function __construct(array $configuration = [])
    {
        if (empty($configuration)) {
            try {
                $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);
                $configuration = $extensionConfiguration->get('bc_simplerate');
            } catch (\Exception) {
                // do nothing
            }
        }
        foreach ($configuration as $key => $value) {
            if (property_exists(self::class, $key)) {
                $this->$key = $value;
            }
        }
    }

    /** @var bool */
    protected $showAdminModule = true;

    /** @var int */
    protected $paginateItemsPerPage = 25;

    /** @var string */
    protected $featureGetRecordsField = '[{}]';
      
    /**
     * Method setShowAdminModule
     *
     * @param bool $showAdminModule
     *
     * @return void
     */
    public function setShowAdminModule(bool $showAdminModule): void
    {
        $this->showAdminModule = $showAdminModule;
    }
    
    /**
     * Method getShowAdminModule
     *
     * @return bool
     */
    public function getShowAdminModule(): bool
    {
        return (bool)$this->showAdminModule;
    }
     
    /**
     * Method setPaginateItemsPerPage
     *
     * @param string $paginateItemsPerPage
     *
     * @return void
     */
    public function setPaginateItemsPerPage(string $paginateItemsPerPage): void
    {
        $this->paginateItemsPerPage = $paginateItemsPerPage;
    }
    
    /**
     * Method getPaginateItemsPerPage
     *
     * @return int
     */
    public function getPaginateItemsPerPage(): int
    {
        return (int)$this->paginateItemsPerPage;
    }
 
    /**
     * Method setFeatureGetRecordsField
     *
     * @param string $featureGetRecordsField
     *
     * @return void
     */
    public function setFeatureGetRecordsField(string $featureGetRecordsField): void
    {
        $this->featureGetRecordsField = $featureGetRecordsField;
    }
    
    /**
     * Method getFeatureGetRecordsField
     *
     * @return string
     */
    public function getFeatureGetRecordsField(): string
    {
        return (string)$this->featureGetRecordsField;
    }
    
    /**
     * Method getFeatureGetRecordsFieldArray
     *
     * @return array
     */
    public function getFeatureGetRecordsFieldArray(): array
    {
        $featureGetRecordsField = $this->featureGetRecordsField;
        $decodedDataAsArray = json_decode($featureGetRecordsField, true);

        $result = [];

        if (!empty($featureGetRecordsField)) {
            foreach($decodedDataAsArray as $key => $row) {
                foreach($row as $tableName => $fieldName) {    
                    $result[$tableName] = $fieldName;
                }
            }
        }

        return $result;
    }
}
