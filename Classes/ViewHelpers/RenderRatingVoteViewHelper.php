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

namespace Bc\BcSimplerate\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use Bc\BcSimplerate\Domain\Repository\RateRepository;

/**
 * Class RenderRateResultViewHelper
 *
 * @package Bc\BcSimplerate\ViewHelpers
 */
class RenderRatingVoteViewHelper extends AbstractViewHelper
{
    /**
     * rateRepository.
     *
     * @var RateRepository
     */
    public $rateRepository = null;

    public function injectRateRepository(RateRepository $rateRepository)
    {
        $this->rateRepository = $rateRepository;
    }
    
    /**
     * Arguments initialization.
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        
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
            'cookiename', 
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
