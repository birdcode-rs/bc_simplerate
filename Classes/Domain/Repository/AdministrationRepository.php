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
    public function getTotalCounts(): array
    {
        $count = [];

        $queryBuilder = $this->getQueryBuilder('tx_bcsimplerate_domain_model_rate');
        $queryBuilder->getRestrictions()->removeAll();

        $count['tx_bcsimplerate_domain_model_rate'] = $queryBuilder
            ->count('*')
            ->from('tx_bcsimplerate_domain_model_rate')
            ->executeQuery()->fetchOne();
  
        return $count;
    }

    private function getConnection(string $table): Connection
    {
        return GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($table);
    }

    private function getQueryBuilder(string $table): QueryBuilder
    {
        return $this->getConnection($table)->createQueryBuilder();
    }
}
