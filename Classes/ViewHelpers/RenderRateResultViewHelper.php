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

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use BirdCode\BcSimplerate\Domain\Repository\RateRepository;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class RenderRateResultViewHelper.
 * @package BirdCode\BcSimplerate\ViewHelpers
 */
class RenderRateResultViewHelper extends AbstractViewHelper
{
    /** @var RateRepository */
    protected $rateRepository;

    /**
     * Inject the RateRepository
     *
     * @param RateRepository $rateRepository
     */
    public function injectRateRepository(RateRepository $rateRepository): void
    {
        $this->rateRepository = $rateRepository;
    }
 
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
        $this->registerArgument('storage', 'integer', 'Record storage ID', true, 0);
        $this->registerArgument('featureFeuser', 'integer', 'Define if Feuser feature is enabled', false, 0);
    }
     
    /**
     * Method render
     *
     * @return array
     */
    public function render(): array
    {
        $recordid = $this->arguments['recordid'];
        $tablename = $this->arguments['tablename'];
        $storage = $this->arguments['storage'];
        $featureFeuser = $this->arguments['featureFeuser'];
        $response = [];
     
        if ($featureFeuser) {
            $results = $this->rateRepository->findRecordByIdAndTablenameForLoggedUsers((int) $recordid, $tablename, (int) $storage);
        } else {
            $results = $this->rateRepository->findRecordByIdAndTablename((int) $recordid, $tablename, (int) $storage);
        }
        
        if (is_array($results) && !empty($results)) {
            $i = count($results);
            $rates = 0;

            foreach ($results as $key => $result) {
                (int) $rates += (int) $result->getRate();
            }
            
            $roundedResult = round($rates / $i * 2, 0) / 2;
            $response = [
                'result' => $rates / $i,
                'roundedResult' => $roundedResult,
                'numberOfRates' => $i,
                'ratingResults' => $results
            ];

            $frontendUser = GeneralUtility::makeInstance(Context::class)->getAspect('frontend.user');
             
            if (null !== $frontendUser) {
                $userId = $frontendUser->get('id');
                $rated = $this->rateRepository->findRecordByUserAndRecordId((int) $userId, (int) $recordid);
                if (isset($rated) && !empty($rated)) {
                    $response['rateData'] = reset($rated);
                }
            }
        }
        
        return $response;
    }
}