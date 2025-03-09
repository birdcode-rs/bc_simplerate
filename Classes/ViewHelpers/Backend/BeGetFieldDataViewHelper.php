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

namespace BirdCode\BcSimplerate\ViewHelpers\Backend;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;


/**
 * Class BeGetFieldDataViewHelper.
 * @package BirdCode\BcSimplerate\ViewHelpers\Backend
 */
class BeGetFieldDataViewHelper extends AbstractViewHelper
{
     
    /**
     * Method initializeArguments
     *
     * @return void
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments(); 

        // registerArgument($name, $type, $description, $required, $defaultValue, $escape)
        $this->registerArgument('recordid', 'integer', 'ID of the record', true, 0 );
        $this->registerArgument('tablename', 'string', 'Name of the table', true, '');
        $this->registerArgument('config','array', 'Config from Dto Em Config', true, []);
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
    public function getRecordValue(int $id, string $tablename, array $config): ?array
    {
        $select = '*';
        $queryBuilder = $this->getQueryBuilder($tablename);
     
        if (isset($config[$tablename])) {
            $select = $config[$tablename];
        }

        $result = $queryBuilder
        ->select($select)  
        ->from($tablename)
        ->where(
            $queryBuilder->expr()->eq(
                "uid",
                $queryBuilder->createNamedParameter($id)
            )
        );
 
        return $result->executeQuery()->fetchFirstColumn();
    }
    
    /**
     * Method render
     *
     * @return string
     */
    public function render(): string
    {   
        $recordid = $this->arguments['recordid'];
        $tablename = $this->arguments['tablename'];
        $config = $this->arguments['config'];
 
        if (!empty($config)) {
            $recordValue = $this->getRecordValue($recordid, $tablename, $config);
            if (!empty($recordValue)) {
                return $recordValue[0];
            }
        }

        return '-';
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