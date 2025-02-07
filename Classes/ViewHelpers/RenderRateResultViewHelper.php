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

/**
 * Class RenderRateResultViewHelper.
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
     * Arguments initialization.
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();

        // registerArgument($name, $type, $description, $required, $defaultValue, $escape)
        $this->registerArgument(
            'recordid', 
            'integer', 
            '', 
            true, 
            0,
        );

        $this->registerArgument(
            'tablename', 
            'string', 
            '', 
            true, 
            '',
        );

        $this->registerArgument(
            'storage',
            'integer',
            '',
            true, 
            0,
        );
    }

    public function render()
    {
        $recordid = $this->arguments['recordid'];
        $tablename = $this->arguments['tablename'];
        $storage = $this->arguments['storage'];
        $response = [];

        $results = $this->rateRepository->findRecordByIdAndTablename((int) $recordid, $tablename, (int) $storage);

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
            ];
        }
        
        return $response;
    }
}
