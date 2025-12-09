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

namespace BirdCode\BcSimplerate\ViewHelpers;

use BirdCode\BcSimplerate\Domain\Model\Dto\EmConfiguration;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class GetRatedDataViewHelper.
 * @package BirdCode\BcSimplerate\ViewHelpers
 */
class GetRatedDataViewHelper extends AbstractViewHelper
{
    /** @var EmConfiguration */
    protected EmConfiguration $emConfiguration;
    protected Context $context;
 
    public function initialize(): void
    {
        $this->emConfiguration = GeneralUtility::makeInstance(EmConfiguration::class);
        $this->context = GeneralUtility::makeInstance(Context::class);
    }

    /**
     * Method initializeArguments
     *
     * @return void
     */
    public function initializeArguments(): void
    {
        // registerArgument($name, $type, $description, $required, $defaultValue, $escape)
        $this->registerArgument('recordid', 'int', 'ID of the record', true);
        $this->registerArgument('tablename', 'string', 'Name of the table', true);
    }
 
    /**
     * Method getRecordValue
     *
     * @param int $id
     * @param string $tablename
     * @param array $config
     *
     * @return array
     */
    public function getRecords(int $id, string $tablename): array
    {
        $languageAspect = $this->context->getAspect('language');
        $languageUid = (int) $languageAspect->getId();
        $select = '*';
 
        $isLocalizedTable = isset($GLOBALS['TCA'][$tablename]['ctrl']['languageField']);

        if (null !== $this->emConfiguration->getFeatureGetRecordsFieldArray() && isset($this->emConfiguration->getFeatureGetRecordsFieldArray()[$tablename])) {
            $select = $this->emConfiguration->getFeatureGetRecordsFieldArray()[$tablename];
        }
 
        $queryBuilder = $this->getQueryBuilder($tablename);
 
        $result = $queryBuilder
        ->select($select)  
        ->from($tablename);
 
        if ($languageUid && $isLocalizedTable) {
            $result
                ->where(
                    $queryBuilder->expr()->eq(
                        "l10n_parent",
                        $queryBuilder->createNamedParameter($id)
                    ),
                     $queryBuilder->expr()->eq(
                        "sys_language_uid",
                        $queryBuilder->createNamedParameter($languageUid)
                    )
                );
              
        } else {
            $result->where(
                $queryBuilder->expr()->eq(
                    "uid",
                    $queryBuilder->createNamedParameter($id)
                ) 
            );
        }
        
        $result = $result->executeQuery()->fetchAllAssociative();
 
        if (count($result)) {
            $result = reset($result);
        }
        return $result;
    }
     
    /**
     * Method render
     *
     * @return array
     */
    public function render(): array
    {   
        $recordid = (int)$this->arguments['recordid'];
        $tablename = (string)$this->arguments['tablename'];

        return $this->getRecords($recordid, $tablename);
    }
 
    /**
     * Method getConnection
     *
     * @param string $table
     *
     * @return Connection
     */
    private function getConnection(string $table): Connection
    {
        return GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($table);
    }
    
    /**
     * Method getQueryBuilder
     *
     * @param string $table
     *
     * @return QueryBuilder
     */
    private function getQueryBuilder(string $table): QueryBuilder
    {
        return $this->getConnection($table)->createQueryBuilder();
    }
}