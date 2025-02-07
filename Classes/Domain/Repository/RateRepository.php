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
 * The repository for Simplerate.
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
     * findRecordByIdAndTablename.
     *
     * @param int uid
     * @param string tablename
     * @param int pid
     *
     * @return ?array
     */
    public function findRecordByIdAndTablename(int $uid, string $tablename, int $pid): ?array
    {
        $query = $this->createQuery();

        $query->getQuerySettings()->setRespectStoragePage(false);
        
        if (isset($uid)) {
            $constraints[] = $query->equals('recordid', $uid);
        }
        if (isset($tablename)) {
            $constraints[] = $query->equals('tablename', $tablename);
        }
        if (isset($pid)) {
            $constraints[] = $query->equals('pid', $pid);
        }

        $constraints[] = $query->greaterThan('rate', 0);

        $query->matching($query->logicalAnd(...$constraints));

        $results = $query->execute()->toArray();

        return $results;
    }

    /**
     * findByParams.
     *
     * @param int uid
     * @param string tablename
     * @param int ratedNumber
     * @param int pid
     *
     * @return ?array
     */
    public function findByParams(int $uid, string $tablename, int $ratedNumber, int $pid): ?array
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

        $query->matching($query->logicalAnd(...$constraints));

        $results = $query->execute()->toArray();

        return $results;
    }
}
