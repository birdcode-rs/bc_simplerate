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

use BirdCode\BcSimplerate\Domain\Repository\RateRepository;
use BirdCode\BcSimplerate\Utility\TypoScript;
use BirdCode\BcSimplerate\Domain\Model\Rate;  
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Pagination\SimplePagination;
use TYPO3\CMS\Core\Pagination\ArrayPaginator;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use Psr\Http\Message\ResponseInterface;
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
    protected array $originalSettings = [];
 
    protected RateRepository $rateRepository;
 
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
        private readonly Context $context
    ) {
        $this->rateRepository = $rateRepository; 
        $this->persistenceManager = $persistenceManager;
    }

    /**
     * Initializes the current action.
     *
     * @return void
     */
    public function initializeAction(): void
    {
        $this->buildSettings();
    }
    
    /**
     * Method initializeView
     *
     * @return void
     */
    protected function initializeView($view): void
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
     * Method rateItAction
     *
     * @param ?Rate $rate
     *
     * @return ResponseInterface
     */
    public function rateItAction(?Rate $rate = null): ResponseInterface
    {
        $userId = null;
        $frontendUser = $this->context->getAspect('frontend.user');

        if ($frontendUser !== null && $frontendUser->isLoggedIn()) {
            $userId = (int) $frontendUser->get('id');
        }
 
        if ($rate !== null) {
            if (null === $rate->getFeuser() && $userId) {
                $rate->setFeuser($userId);
            }

            $rate->setRecordlanguage($this->context->getAspect('language')->getId());

            // validate if note feature is enable and active
            if (null !== ($this->settings['feature']) && is_array(($this->settings['feature'])) && $this->settings['feature']['noteFieldEnabled'] && $this->settings['feature']['noteFieldRequired']) {
                if (empty(trim($rate->getNote()))) {
                    return $this->jsonResponse(
                        json_encode(
                            [
                                'status' => 'error', 
                                'message' => 'missing_req_field_data',
                            ]
                        )
                    );
                }
                if (null === ($rate->getNote())) {
                    return $this->jsonResponse(
                        json_encode(
                            [
                                'status' => 'error', 
                                'message' => 'missing_req_field',
                            ]
                        )
                    );
                }
            }

            $this->rateRepository->add($rate);
            $this->persistenceManager->persistAll();
            $currentRecordId = $rate->getRecordId();
 
            // Call the viewhelper to render the voting data.
            $getInstanceRRVH = new (GeneralUtility::makeInstance('BirdCode\\BcSimplerate\\ViewHelpers\\RenderRateResultViewHelper'));
            $getInstanceRRVH->setArguments(['recordid' => $rate->getRecordId(), 'tablename' => $rate->getTablename(), 'storage' => $rate->getPid(), 'featureFeuser' => 0]);
            $getInstanceRRVH->injectRateRepository($this->rateRepository);
   
            return $this->jsonResponse(
                json_encode(
                    [
                        'status' => 'success',
                        'message' => 'ok',
                        'rate' => $rate->getRate(), 
                        'recordId' => $currentRecordId,
                        'ratingResults' => $getInstanceRRVH->render()
                    ]
                )
            );
        }

        /* *
        * Block to render the form if the user attempts to remove the note field, which may be required
        */
        $this->view->assign('blockRating', 0);    
        if (isset($_COOKIE["blockRateFor60"])) {
            $this->view->assign('blockRating', 1);    
        }

        /**
         * This cookie is set to prevent the user from submitting the form twice.
         * If an error occurs on the front-end and the cookie is still active, it should be removed from the system after a refresh.
         * */
        if (isset($_COOKIE["tmpBcRateCookie"])) {
            setcookie('tmpBcRateCookie');
        }
 
        $this->view->assign('rate', $rate);  
        $this->view->assign('userId', $userId);
        $this->view->assign('user', $frontendUser);

        return $this->htmlResponse();
    }
 
    /**
     * ratingsAction.
     * @param int $currentPage
     * @return ResponseInterface
     */
    public function ratingsAction(int $currentPage = 1): ResponseInterface
    {   
        if (empty($this->settings['displayMode'])) {
            $ratings = $this->rateRepository->findByType($this->settings['resultType'], $this->settings['storage'], $this->context->getAspect('frontend.user') ? $this->context->getAspect('frontend.user')->get('id'):null);

            if (!empty($this->settings['limit'])) {
                $ratings = $ratings->getQuery()->setLimit((int) $this->settings['limit'])->execute();
            }
        } else {
            $ratings = $this->rateRepository->findByTypeWithDisplayMode($this->settings['resultType'], $this->settings['storage'], $this->context->getAspect('language')->getId(), $this->settings['limit']);
        }

        // paginate.itemsPerPage
        if ($ratings) {
            if (empty($this->settings['displayMode'])) {
                $paginator = new QueryResultPaginator(
                    $ratings,
                    $currentPage,
                    (int) ($this->settings['paginate']['itemsPerPage'] ? $this->settings['paginate']['itemsPerPage']:10)
                );
            } else {
                $paginator = new ArrayPaginator(
                    $ratings,
                    $currentPage,
                    (int) ($this->settings['paginate']['itemsPerPage'] ? $this->settings['paginate']['itemsPerPage']:10)
                );
            }

            $pagination = new SimplePagination($paginator);

            $this->view->assignMultiple([
                'ratings' => $ratings,
                'paginator' => $paginator,
                'paginatorItems' => $paginator->getPaginatedItems(),
                'pagination' => $pagination,
                'totalPages' => range(1, $pagination->getLastPageNumber()),
                'actionName' => str_replace("Action", "", __FUNCTION__),
                'currentPage' => $currentPage
            ]);
        }
  
        return $this->htmlResponse();
    }
 
    /***************************************************************************
     * helper
     **********************/
    /**
     * Injects the Configuration Manager and is initializing the framework settings.
     * Yap thanks to the Georg News ext
     * @param ConfigurationManagerInterface $configurationManager Instance of the Configuration Manager
     *
     * @return void
     */
    public function buildSettings(): void
    {
        $tsSettings = $this->configurationManager->getConfiguration(
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