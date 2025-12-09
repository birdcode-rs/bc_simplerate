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

use BirdCode\BcSimplerate\Domain\Model\Rate;
use BirdCode\BcSimplerate\Domain\Repository\RateRepository;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class RenderRatingVoteViewHelper
 * @package BirdCode\BcSimplerate\ViewHelpers
 */
class RenderRatingVoteViewHelper extends AbstractViewHelper
{
    public RateRepository $rateRepository;
 
    /**
     * Inject the RateRepository
     *
     * @param RateRepository $rateRepository
     */
    public function injectRateRepository(RateRepository $rateRepository)
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
        // registerArgument($name, $type, $description, $required, $defaultValue, $escape)
        $this->registerArgument('recordid', 'integer', 'ID of the record', true);
        $this->registerArgument('tablename', 'string', 'Name of the table', true);
        $this->registerArgument('cookiename', 'string', 'Name of the cookie used to store rate results', true);
        $this->registerArgument('storage', 'integer', 'Record storage ID', true);
        $this->registerArgument('featureFeuser', 'integer', 'Define if Feuser feature is enabled', false, 0);
    }
     
    /**
     * Method render
     *
     * @return Rate|null
     */
    public function render(): ?Rate
    {
        $recordid = (int)$this->arguments['recordid'];
        $tablename = (string)$this->arguments['tablename'];
        $cookiename = (string)$this->arguments['cookiename'];
        $storage = (int)$this->arguments['storage'];
        $featureFeuser = (int)$this->arguments['featureFeuser'];

        $languageAspect = GeneralUtility::makeInstance(Context::class)->getAspect('language');
        $languageUid = (int) $languageAspect->getId();
 
        $rateData = null;
        $userId = null;
 
        $frontendUser = GeneralUtility::makeInstance(Context::class)->getAspect('frontend.user');
        if ($frontendUser !== null && $frontendUser->isLoggedIn()) {
            $userId = (int)$frontendUser->get('id');
        }
 
        if ($userId && $featureFeuser) {
            $rated = $this->rateRepository->findRecordByUserAndRecordId($userId, $recordid, $languageUid);
            $rateData = reset($rated) ?? null;
        } elseif (isset($_COOKIE[$cookiename]) && !$featureFeuser) {

            // 25|5, 30|3
            $cookieValue = (string)$_COOKIE[$cookiename];
            $cookieItems = explode(",", $cookieValue);
 
            foreach ($cookieItems as $key => $value) {
                if (isset($value)) {
                    $uidAndRate = explode("|", $value);
                    if ($uidAndRate[0] == $recordid) {
                        $rated = $this->rateRepository->findByParams((int) $uidAndRate[0], $tablename, (int) $uidAndRate[1], $storage, $languageUid);
                        if (!empty($rated)) {
                            $rateData = reset($rated);
                            break;
                        }
                    }
                }
            }
        }
        
        return $rateData;
    }
}