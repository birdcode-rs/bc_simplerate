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

namespace BirdCode\BcSimplerate\ViewHelpers\Backend;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
 
/**
 * Class BeGetTcaFieldDataViewHelper.
 * @package BirdCode\BcSimplerate\ViewHelpers\Backend
 */
class BeGetTcaFieldDataViewHelper extends AbstractViewHelper
{
    /**
     * Method initializeArguments
     *
     * @return void
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments(); 

        // registerArgument($name, $type, $description, $required, $defaultValue, $escape)
        $this->registerArgument('fieldname', 'string', 'Field of the TCA', true, '');
        $this->registerArgument('tablename', 'string', 'TCA config table name', true, '');
    }
 
    /**
     * Method render
     *
     * @return mixed
     */
    public function render(): mixed
    {   
        $fieldname = $this->arguments['fieldname'];
        $tablename = $this->arguments['tablename'];
 
        if (!empty($fieldname) && !empty($tablename)) {
            // Get the TCA array for a specific table, e.g., 'tx_news_domain_model_news'
            $tcaConfig = $GLOBALS['TCA'][$tablename];
            if (!empty($tcaConfig)) {
                // Access the 'ctrl' section of the TCA configuration
                $ctrlData = $tcaConfig['ctrl'];

                if (isset($ctrlData[$fieldname])) {
                    return $ctrlData[$fieldname];
                }
            }
        }

        return '';
    }
}