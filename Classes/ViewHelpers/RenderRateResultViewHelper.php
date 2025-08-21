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
use TYPO3\CMS\Core\Context\LanguageAspect;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class RenderRateResultViewHelper.
 * @package BirdCode\BcSimplerate\ViewHelpers
 */
class RenderRateResultViewHelper extends AbstractViewHelper
{
    protected RateRepository $rateRepository;
 
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
        $this->registerArgument('recordid', 'int', 'ID of the record', true, 0 );
        $this->registerArgument('tablename', 'string', 'Name of the table', true, '');
        $this->registerArgument('storage', 'int', 'Record storage ID', true, 0);
        $this->registerArgument('featureFeuser', 'int', 'Define if Feuser feature is enabled', false, 0);
    }
     
    /**
     * Method render
     *
     * @return array
     */
    public function render(): array
    {
        $recordid = (int) $this->arguments['recordid'] ?? 0;
        $tablename = $this->arguments['tablename'] ?? '';
        $storage = (int) $this->arguments['storage'] ?? 0;
        $userId = null;
        $response = [];
        $languageAspect = GeneralUtility::makeInstance(Context::class)->getAspect('language');
        $languageUid = (int) $languageAspect->getId();

        $frontendUser = GeneralUtility::makeInstance(Context::class)->getAspect('frontend.user');
        if ($frontendUser !== null && $frontendUser->isLoggedIn()) {
            $userId = (int) $frontendUser->get('id');
            $results = $this->rateRepository->findRecordByIdAndTablenameForLoggedUsers($recordid, $tablename, $storage, $languageUid);
        } else {
            $results = $this->rateRepository->findRecordByIdAndTablename($recordid, $tablename, $storage, $languageUid);
        }
        
        if (is_array($results) && !empty($results)) {
            $i = count($results);
            $rates = 0;

            foreach ($results as $key => $result) {
                $rates += (int) $result->getRate();
            }
            
            $roundedResult = round($rates / $i * 2, 0) / 2;
            $response = [
                'result' => $rates / $i,
                'roundedResult' => $roundedResult,
                'numberOfRates' => $i,
                'ratingResults' => $results
            ];
 
            if ($userId) { 
                $rated = $this->rateRepository->findRecordByUserAndRecordId($userId, $recordid, $languageUid);
                $response['rateData'] = !empty($rated) ? $rated[0] : null;
            }
        }
        
        return $response;
    }
}