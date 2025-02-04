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

namespace BirdCode\BcSimplerate\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use Psr\Http\Message\ResponseInterface;

use BirdCode\BcSimplerate\Domain\Repository\RateRepository;
use BirdCode\BcSimplerate\Utility\TypoScript;
use BirdCode\BcSimplerate\Domain\Model\Rate;

/**
 * RateController.
 */
class RateController extends ActionController
{
    /**
     * Original settings without any magic done by stdWrap and skipping empty values.
     *
     * @var array
     */
    protected $originalSettings = [];

    /** @var RateRepository */
    public $rateRepository = null;

    protected PersistenceManager $persistenceManager;
 
    /**
     * __construct.
     *
     * @param RateRepository rateRepository
     * @param PersistenceManager persistenceManager
     *
     * @return void
     */
    public function __construct(
        RateRepository $rateRepository,
        PersistenceManager $persistenceManager,
    ) {
        $this->rateRepository = $rateRepository; 
        $this->persistenceManager = $persistenceManager;
    }

    /**
     * Initializes the current action.
     *
     * @return void
     */
    public function initializeAction()
    {
        $this->buildSettings();
    }

    protected function initializeView($view)
    {
        // @extensionScannerIgnoreLine
        /**
         * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $currentContentObject
         */
        $currentContentObject = $this->request->getAttribute('currentContentObject');
        $view->assign('currentContentObject', $currentContentObject->data);
        
        if (isset($GLOBALS['TSFE']) && is_object($GLOBALS['TSFE'])) {
            $view->assign('pageData', $GLOBALS['TSFE']->page);
        }
    }
 
    /**
     * rateIt.
     *
     * @param Rate rate
     *
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     */
    public function rateItAction(Rate $rate = null): ResponseInterface
    {
        if ($rate !== null) {

            $this->rateRepository->add($rate);
            $this->persistenceManager->persistAll();
            $currentRecordId = $rate->getRecordId();

            return $this->jsonResponse(
                json_encode(
                    [
                        'status' => 'success', 
                        'rate' => $rate->getRate(), 
                        'recordId' => $currentRecordId
                    ]
                )
            );
        }

        $this->view->assign('rate', $rate);
        return $this->htmlResponse();
    }
 

    /***************************************************************************
     * helper
     **********************/
    /**
     * Injects the Configuration Manager and is initializing the framework settings.
     *
     * @param ConfigurationManagerInterface $configurationManager Instance of the Configuration Manager
     *
     * @return void
     */
    public function buildSettings()
    {
        $tsSettings = $this->request->getAttribute->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
            'bc_simplerate',
            'bc_simplerate_pi1'
        );
        $originalSettings = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS
        );

        $propertiesNotAllowedViaFlexForms = ['orderByAllowed'];
        foreach ($propertiesNotAllowedViaFlexForms as $key => $property) {
            $originalSettings[$property] = ($tsSettings['settings'] ?? [])[$property] ?? ($originalSettings[$property] ?? '');
        }
        $this->originalSettings = $originalSettings;
 
        if (isset($originalSettings['useStdWrap']) && !empty($originalSettings['useStdWrap'])) {
            $typoScriptService = GeneralUtility::makeInstance(TypoScriptService::class);
            $typoScriptArray = $typoScriptService->convertPlainArrayToTypoScriptArray($originalSettings);
            $stdWrapProperties = GeneralUtility::trimExplode(',', $originalSettings['useStdWrap'], true);
            foreach ($stdWrapProperties as $key) {
                if (is_array($typoScriptArray[$key.'.'])) {
                    $originalSettings[$key] = $this->request->getAttribute('currentContentObject')->stdWrap(
                        $typoScriptArray[$key],
                        $typoScriptArray[$key.'.']
                    );
                }
            }
        }

        // start override
        if (isset($tsSettings['settings']['overrideFlexformSettingsIfEmpty'])) {
            $typoScriptUtility = GeneralUtility::makeInstance(TypoScript::class);
            $originalSettings = $typoScriptUtility->override($originalSettings, $tsSettings);
        }

        foreach ($hooks = ($GLOBALS['TYPO3_CONF_VARS']['EXT']['bc_simplerate']['Controller/RateController.php']['overrideSettings'] ?? []) as $_funcRef) {
            $_params = [
                'originalSettings' => $originalSettings,
                'tsSettings' => $tsSettings,
            ];
            $originalSettings = GeneralUtility::callUserFunction($_funcRef, $_params, $this);
        }

        if (isset($originalSettings) && isset($originalSettings['maxRateNumber']) && !empty($originalSettings['maxRateNumber'])) {
            $i = 1;
            $generateRating = [];

            while ($i <= $originalSettings['maxRateNumber']) {
                $generateRating[$i] = $i;
                ++$i;
            }
            $originalSettings['generateRating'] = $generateRating;
        }

        $this->settings = $originalSettings;
    }
}
