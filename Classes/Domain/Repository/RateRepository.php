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

use BirdCode\BcSimplerate\Domain\Model\Rate;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\LanguageAspect;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
 
/**
 * RateRepository
 */
class RateRepository extends Repository
{
    /* main table of the simplerate plugin */
    protected string $tablename = 'tx_bcsimplerate_domain_model_rate';
  
    /**
     * Method Finds a record by UID and table name, optionally filtered by PID.
     *
     * @param int $uid 
     * @param string $tablename 
     * @param ?int $pid 
     * @param int $languageId 
     *
     * @return array
     */
    public function findRecordByIdAndTablename(int $uid, string $tablename, ?int $pid = null, int $languageId = 0): ?array
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->getQuerySettings()->setRespectSysLanguage(false);

        $constraints = [
            $query->greaterThan('rate', 0),
            $query->equals('recordid', $uid),
            $query->equals('recordlanguage', $languageId),
        ];
         
        if (isset($tablename)) {
            $constraints[] = $query->equals('tablename', $tablename);
        }
        if ($pid !== null) {
            $constraints[] = $query->equals('pid', $pid);
        }
 
        $query->matching($query->logicalAnd(...$constraints))->setOrderings(['rate' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING]);

        $results = $query->execute()->toArray();
 
        return $results;
    }
 
    /**
     * Method - Finds a record by UID and table name, optionally filtered by PID, for loggedin users.
     *
     * @param int $uid 
     * @param string $tablename 
     * @param ?int $pid 
     * @param int $languageId 
     *
     * @return array
     */
    public function findRecordByIdAndTablenameForLoggedUsers(int $uid, string $tablename, ?int $pid = null, int $languageId = 0): ?array
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->getQuerySettings()->setRespectSysLanguage(false);
        $constraints = [
            $query->greaterThan('rate', 0),
            $query->equals('recordid', $uid),
            $query->greaterThan('feuser', 0),
            $query->equals('recordlanguage', $languageId),
        ];
         
        if (isset($tablename)) {
            $constraints[] = $query->equals('tablename', $tablename);
        }
        if ($pid !== null) {
            $constraints[] = $query->equals('pid', $pid);
        }
 
        $query->matching($query->logicalAnd(...$constraints))->setOrderings(['rate' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING]);

        $results = $query->execute()->toArray();
        return $results;
    }

     
    /**
     * Method findByParams
     *
     * @param int $uid 
     * @param string $tablename 
     * @param int $ratedNumber 
     * @param int $pid 
     * @param ?int $feuser 
     * @param int $languageId 
     *
     * @return array
     */
    public function findByParams(int $uid, string $tablename, int $ratedNumber, int $pid, ?int $feuser = null, int $languageId = 0): ?array
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->getQuerySettings()->setRespectSysLanguage(false);

        $constraints[] = $query->equals('recordlanguage', $languageId);

        if (isset($uid)) {
            $constraints[] = $query->equals('recordid', $uid);
        }
        if (isset($tablename)) {
            $constraints[] = $query->equals('tablename', $tablename);
        }
        if (isset($ratedNumber)) {
            $constraints[] = $query->equals('rate', $ratedNumber);
        }
        if (isset($pid)) {
            $constraints[] = $query->equals('pid', $pid);
        }
        if ($feuser !== null) {
            $constraints[] = $query->equals('feuser', $feuser);
        }

        $query->matching($query->logicalAnd(...$constraints))->setOrderings(['rate' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING]);

        $results = $query->execute()->toArray();
        return $results;
    } 
     
    /**
     * Method findRecordByUser
     *
     * @param int $feuser 
     * @param int $languageId 
     *
     * @return array
     */
    public function findRecordByUser(int $feuser, int $languageId = 0): ?array
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->getQuerySettings()->setRespectSysLanguage(false);
 
        $constraints = [
            $query->equals('feuser', $feuser),
            $query->greaterThan('rate', 0),
            $query->equals('recordlanguage', $languageId)
        ];

        $query->matching($query->logicalAnd(...$constraints))->setOrderings(['rate' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING]);

        $results = $query->execute()->toArray();
        return $results;
    }

     
    /**
     * Method findRecordByUserAndRecordId
     *
     * @param int $feuser 
     * @param int $recordid 
     * @param int $languageId 
     *
     * @return array
     */
    public function findRecordByUserAndRecordId(int $feuser, int $recordid, int $languageId = 0): ?array
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->getQuerySettings()->setRespectSysLanguage(false);
        
        $constraints = [
            $query->equals('feuser', $feuser),
            $query->equals('recordid', $recordid),
            $query->greaterThan('rate', 0),
            $query->equals('recordlanguage', $languageId)
        ];

        $query->matching($query->logicalAnd(...$constraints))->setOrderings(['rate' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING]);

        $results = $query->execute()->toArray();
        return $results;
    }
  
     
    /**
     * Method findByType
     *
     * @param string $type 
     * @param string $pid 
     * @param ?int $feuser 
     * @param int $languageId 
     * @param string $topRated 
     *
     * @return QueryResultInterface
     */
    public function findByType(string $type, string $pid, ?int $feuser = null, int $languageId = 0, string $topRated = '', string $recordtable = ''): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->getQuerySettings()->setRespectSysLanguage(false);
 
        $constraints = [
            $query->greaterThan('rate', 0),
            $query->in('pid', explode(',', $pid)),
            $query->equals('recordlanguage', $languageId),
        ];

        if ($type == '1') {
            $constraints[] = $query->equals('feuser', 0);
        } elseif ($type == '2' && null !== $feuser) {
            $constraints[] = $query->equals('feuser', $feuser);
        } else {
            $constraints[] = $query->greaterThan('feuser', 0);
        }
 
        if (!empty($topRated)) {
            $constraints[] = $query->equals('rate', 5); 
        }
 
        if (!empty($recordtable)) {
            $constraints[] = $query->equals('tablename', $recordtable); 
        }
 
        $query->matching($query->logicalAnd(...$constraints))->setOrderings(['rate' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING]);
        $results = $query->execute();

        return $results;
    }
    
    /**
     * Method findByTypeWithDisplayMode
     *
     * @param string $type
     * @param string $pid
     * @param int $languageId
     * @param string $limit
     * @param string $topRated
     *
     * @return array
     */
    public function findByTypeWithDisplayMode(string $type, string $pid, int $languageId = 0, string $limit = '10', string $topRated = '', $recordtable = ''): ?array
    {
        $queryBuilder = $this->getQueryBuilder($this->tablename);

        $constraints = [];

        if (!empty($type) && $type == '1') {
            $constraints[] = $queryBuilder->expr()->gt(
                "feuser",
                $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)
            );
        } else {
            $constraints[] = $queryBuilder->expr()->eq(
                "feuser",
                $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)
            );
        }
 
        if (!empty($pid)) {
            $constraints[] = $queryBuilder->expr()->eq(
                "pid",
                $queryBuilder->createNamedParameter($pid, Connection::PARAM_INT)
            );
        }
 
        $constraints[] = $queryBuilder->expr()->eq(
            "recordlanguage",
            $queryBuilder->createNamedParameter($languageId, Connection::PARAM_INT)
        );
        
 
        if (!empty($recordtable)) {
            $constraints[] = $queryBuilder->expr()->eq(
                "tablename",
                $queryBuilder->createNamedParameter($recordtable, Connection::PARAM_STR)
            );
        }
        
        $result = $queryBuilder
            ->selectLiteral("(ROUND(SUM(`rate`) / count(`rate`) * 2 , 0) / 2) AS 'roundrate'") 
            ->addSelect("tablename", "recordid", "pid", "recordlanguage")
            ->where(...$constraints);

        $result->from($this->tablename)
            ->groupBy("tablename", "recordid", "pid", "recordlanguage");

        if (!empty($limit)) {
            $result->setMaxResults($limit);
        }

        $result->orderBy('roundrate','desc');
 
        if (!empty($topRated)) {
            $result->having(
                $queryBuilder->expr()->eq(
                    "roundrate",
                    $queryBuilder->createNamedParameter(5, Connection::PARAM_INT)
                )
            );
        }

        $result = $result->executeQuery()->fetchAllAssociative();

        return $result;
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
     
    /**
     * Method getLanguageId
     *
     * @return int
     */
    private function getLanguageId(): int
    {
        $context = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(Context::class);
  
        /** @var LanguageAspect $languageAspect */
        $languageAspect = $context->getAspect('language');
        return (int) $languageAspect->getId();
    }
}