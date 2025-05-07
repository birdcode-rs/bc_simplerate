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

use TYPO3\CMS\Extbase\Persistence\Repository;
 
/**
 * RateRepository
 */
class RateRepository extends Repository
{
    /**
     * @var array
     */
    protected $defaultOrderings = [
        'sorting' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
    ];

    /**
     * Finds a record by UID and table name, optionally filtered by PID.
     *
     * @param int uid
     * @param string tablename
     * @param ?int pid
     *
     * @return ?array
     */
    public function findRecordByIdAndTablename(int $uid, string $tablename, ?int $pid = null): ?array
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);

        $constraints = [
            $query->greaterThan('rate', 0),
            $query->equals('recordid', $uid)
        ];
        
     
        if (isset($tablename)) {
            $constraints[] = $query->equals('tablename', $tablename);
        }
        if ($pid !== null) {
            $constraints[] = $query->equals('pid', $pid);
        }
 
        $query->matching($query->logicalAnd(...$constraints));

        $results = $query->execute()->toArray();
 
        return $results;
    }

    /**
     * Finds a record by UID and table name, optionally filtered by PID, for loggedin users.
     *
     * @param int uid
     * @param string tablename
     * @param ?int pid
     *
     * @return ?array
     */
    public function findRecordByIdAndTablenameForLoggedUsers(int $uid, string $tablename, ?int $pid = null): ?array
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);

        $constraints = [
            $query->greaterThan('rate', 0),
            $query->equals('recordid', $uid),
            $query->greaterThan('feuser', 0)
        ];
         
        if (isset($tablename)) {
            $constraints[] = $query->equals('tablename', $tablename);
        }
        if ($pid !== null) {
            $constraints[] = $query->equals('pid', $pid);
        }
 
        $query->matching($query->logicalAnd(...$constraints));

        $results = $query->execute()->toArray();
 
        return $results;
    }

    /**
     * Finds a rating by full set of parameters.
     *
     * @param int uid
     * @param string tablename
     * @param int ratedNumber
     * @param int pid
     * @param int feuser 
     *
     * @return ?array
     */
    public function findByParams(int $uid, string $tablename, int $ratedNumber, int $pid, ?int $feuser = null): ?array
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);

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

        $query->matching($query->logicalAnd(...$constraints));

        $results = $query->execute()->toArray();

        return $results;
    }


    /**
     *  Finds rated records by frontend user.
     *
     * @param int user
     *
     * @return ?array
     */
    public function findRecordByUser(int $feuser): ?array
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
 
        $constraints = [
            $query->equals('feuser', $feuser),
            $query->greaterThan('rate', 0)
        ];

        $query->matching($query->logicalAnd(...$constraints));

        $results = $query->execute()->toArray();
        return $results;
    }

    /**
     * Finds a rated record by user and record ID.
     *
     * @param int user
     * @param int recordid
     * @return ?array
     */
    public function findRecordByUserAndRecordId(int $feuser, int $recordid): ?array
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        
        $constraints = [
            $query->equals('feuser', $feuser),
            $query->equals('recordid', $recordid),
            $query->greaterThan('rate', 0)
        ];

        $query->matching($query->logicalAnd(...$constraints));

        $results = $query->execute()->toArray();

        return $results;
    }
}