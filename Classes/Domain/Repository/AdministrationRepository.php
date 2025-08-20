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

namespace BirdCode\BcSimplerate\Domain\Repository;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Get data used in the administration view
 */
class AdministrationRepository
{
    /* main table of the simplerate plugin */
    protected string $tablename = 'tx_bcsimplerate_domain_model_rate';
     
    /**
     * Method generateFilter
     *
     * @param int $pid
     *
     * @return array
     */
    public function generateFilter(int $pid = 0): ?array
    {
        $queryBuilder = $this->getQueryBuilder($this->tablename);

        return $queryBuilder
            ->select('tablename')
            ->where(
                $queryBuilder->expr()->eq(
                    "pid",
                    $queryBuilder->createNamedParameter($pid, Connection::PARAM_INT)
                )
            )
            ->from($this->tablename)
            ->executeQuery()->fetchAllAssociativeIndexed();
    }
      
    /**
     * Method roundedResults
     *
     * @param int $pid
     * @param string $criteria
     * @param array $orderBy
     *
     * @return array
     */
    public function roundedResults(int $pid = 0, string $criteria = '', array $orderBy = []): ?array
    {
        $queryBuilder = $this->getQueryBuilder($this->tablename);

        $result = $queryBuilder
        ->selectLiteral("(ROUND(SUM(`rate`) / count(`rate`) * 2 , 0) / 2) AS 'roundrate'") 
        ->addSelect("tablename", "recordid", "pid", "feuser")
        ->where(
            $queryBuilder->expr()->eq(
                "pid",
                $queryBuilder->createNamedParameter($pid, Connection::PARAM_INT)
            )
        );

        if (!empty($criteria)) {
            $result->andWhere($criteria);
        }
 
        if (is_array($orderBy) && !empty($orderBy)) {
            $result->orderBy(...$orderBy);
        }
  
        $result->from($this->tablename)
        ->groupBy("tablename", "recordid", "pid", "feuser");

        return $result->executeQuery()->fetchAllAssociative();
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
