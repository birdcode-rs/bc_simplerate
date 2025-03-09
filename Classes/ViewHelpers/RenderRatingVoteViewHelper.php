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
 * Class RenderRateResultViewHelper
 * @package BirdCode\BcSimplerate\ViewHelpers
 */
class RenderRatingVoteViewHelper extends AbstractViewHelper
{
    /**
     * rateRepository.
     *
     * @var RateRepository
     */
    public $rateRepository = null;

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
        parent::initializeArguments();
        
        // registerArgument($name, $type, $description, $required, $defaultValue, $escape)
        $this->registerArgument('recordid', 'integer', 'ID of the record', true, 0 );
        $this->registerArgument('tablename', 'string', 'Name of the table', true, '');
        $this->registerArgument('cookiename', 'string', 'Name of the cookie used to store rate results', true, '');
        $this->registerArgument('storage', 'integer', 'Record storage ID', true, 0);
    }
     
    /**
     * Method render
     *
     * @return mixed
     */
    public function render(): mixed
    {
        $recordid = $this->arguments['recordid'];
        $tablename = $this->arguments['tablename'];
        $cookiename = $this->arguments['cookiename'];
        $storage = $this->arguments['storage'];
        $rateData = null;

        if (isset($_COOKIE[$cookiename])) {
            // 25|5, 30|3
            $cookieValue = $_COOKIE[$cookiename];
            $cookieValAsArray = explode("," , $cookieValue);
 
            foreach ($cookieValAsArray as $key => $value) {
                if (isset($value)) {
                    $uidAndRate = explode("|" , $value);
                    if ($uidAndRate[0] == $recordid) {
                        $rated = $this->rateRepository->findByParams((int) $uidAndRate[0], (string) $tablename, (int) $uidAndRate[1], (int) $storage);
                        if (isset($rated) && !empty($rated)) {
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
