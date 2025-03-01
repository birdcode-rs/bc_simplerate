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
 
    public function setShowAdminModule($showAdminModule): void
    {
        $this->showAdminModule = $showAdminModule;
    }

    public function getShowAdminModule(): bool
    {
        return (bool)$this->showAdminModule;
    }

    public function setPaginateItemsPerPage($paginateItemsPerPage): void
    {
        $this->paginateItemsPerPage = $paginateItemsPerPage;
    }

    public function getPaginateItemsPerPage(): int
    {
        return (int)$this->paginateItemsPerPage;
    }
}
