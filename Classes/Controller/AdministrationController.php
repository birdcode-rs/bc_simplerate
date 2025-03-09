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

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use BirdCode\BcSimplerate\Domain\Model\Dto\EmConfiguration;
use BirdCode\BcSimplerate\Domain\Model\Rate; 
use BirdCode\BcSimplerate\Domain\Repository\RateRepository;
use BirdCode\BcSimplerate\Domain\Repository\AdministrationRepository;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\Components\Menu\Menu;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Imaging\IconSize;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Pagination\SimplePagination;
use TYPO3\CMS\Core\Type\Bitmask\Permission;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;
use TYPO3\CMS\Extensionmanager\Utility\EmConfUtility;
use TYPO3\CMS\Core\Pagination\ArrayPaginator;


/**
 * The backend controller for the BcSimplerate extension
 */
class AdministrationController extends ActionController
{
    protected int $pageUid = 0;
 
    /** @var AdministrationRepository */
    protected $administrationRepository;

    /** @var RateRepository */
    protected $rateRepository;

    /** @var EmConfiguration */
    protected $emConfiguration;
     
    /**
     * Method injectAdministrationRepository
     *
     * @param AdministrationRepository $administrationRepository
     *
     * @return void
     */
    public function injectAdministrationRepository(AdministrationRepository $administrationRepository): void
    {
        $this->administrationRepository = $administrationRepository;
    }
    
    /**
     * Method injectRateRepository
     *
     * @param RateRepository $rateRepository
     *
     * @return void
     */
    public function injectRateRepository(RateRepository $rateRepository): void
    {
        $this->rateRepository = $rateRepository;
    }

    /**
     * BackendController constructor. Takes care of dependency injection
     *
     * @return void
     */
    public function __construct(
        protected readonly ModuleTemplateFactory $moduleTemplateFactory,
        private readonly IconFactory $iconFactory,
        private readonly LanguageServiceFactory $languageServiceFactory,
    ) {}


    /**
     * @return array<string,scalar>|false
     */
    public function getMetaInformation(): array|false
    {
        $permissionClause = $GLOBALS['BE_USER']->getPagePermsClause(Permission::PAGE_SHOW);
        return BackendUtility::readPageAccess(
            $this->pageUid,
            $permissionClause,
        );
    }
    
     
    /**
     * Method initializeAction
     *
     * @return void
     */
    protected function initializeAction(): void
    {
        $this->pageUid = (int)($this->request->getQueryParams()['id'] ?? 0);
        $this->emConfiguration = GeneralUtility::makeInstance(EmConfiguration::class);
    }
 
    /**
     * Method indexAction
     *
     * @param int $currentPage
     *
     * @return ResponseInterface
     */
    public function indexAction(int $currentPage = 1): ResponseInterface
    {   
        $view = $this->initializeModuleTemplate($this->request);
        
        $filterCriteria = ['tablename' => ($this->request->getQueryParams()['filtertable'] ?? '')];
        $filterOrderBy = ['uid' => 'DESC'];

        if (($this->request->getQueryParams()['filtertable'] ?? '') == '') {
            $filterCriteria = [];
        }

        $allAvailableRates = $this->rateRepository->findBy($filterCriteria, $filterOrderBy);

        if ($allAvailableRates) {
            $paginator = new QueryResultPaginator(
                $allAvailableRates,
                $currentPage,
                $this->emConfiguration->getPaginateItemsPerPage(),
            );
            $pagination = new SimplePagination($paginator);

            $view->assignMultiple([
                'rates' => $allAvailableRates,
                'paginator' => $paginator,
                'paginatorItems' => $paginator->getPaginatedItems(),
                'pagination' => $pagination,
                'allPageNumbers' => range(1, $pagination->getLastPageNumber()),
                'featureGetRecordsField' => $this->emConfiguration->getFeatureGetRecordsFieldArray()
            ]);
        }

        $view->assignMultiple([
            'actionName' => str_replace("Action", "", __FUNCTION__),
            'currentPage' => $currentPage,
            'generateFilter' => $this->administrationRepository->generateFilter($this->pageUid),
            'filtertable' =>  (string)($this->request->getQueryParams()['filtertable'] ?? ''),
        ]);

        return $view->renderResponse('Backend/Index');
    }

     
    /**
     * Method ratingResultsAction
     *
     * @param int $currentPage
     *
     * @return ResponseInterface
     */
    public function ratingResultsAction(int $currentPage = 1): ResponseInterface
    {
        $view = $this->initializeModuleTemplate($this->request);
        $filterCriteria = 'tablename = "' .  ($this->request->getQueryParams()['filtertable'] ?? '') .'"';
        $filterOrderBy = ['roundrate', 'DESC'];

        if (($this->request->getQueryParams()['filtertable'] ?? '') == '') {
            $filterCriteria = '';
        }
 
        $allAvailableRates = $this->administrationRepository->roundedResults($this->pageUid, $filterCriteria, $filterOrderBy);
 
        if ($allAvailableRates) {
            $paginator = new ArrayPaginator(
                $allAvailableRates,
                $currentPage,
                $this->emConfiguration->getPaginateItemsPerPage(),
            );
            $pagination = new SimplePagination($paginator);

            $view->assignMultiple([
                'rates' => $allAvailableRates,
                'paginator' => $paginator,
                'paginatorItems' => $paginator->getPaginatedItems(),
                'pagination' => $pagination,
                'allPageNumbers' => range(1, $pagination->getLastPageNumber()),
                'featureGetRecordsField' => $this->emConfiguration->getFeatureGetRecordsFieldArray()
            ]);
        }
 
        $view->assignMultiple([
            'actionName' => str_replace("Action", "", __FUNCTION__),
            'currentPage' => $currentPage,
            'generateFilter' => $this->administrationRepository->generateFilter($this->pageUid),
            'filtertable' =>  (string)($this->request->getQueryParams()['filtertable'] ?? '')
        ]);
 
        return $view->renderResponse('Backend/RatingResults');
    }
     
     
    /**
     * Method initializeModuleTemplate
     *
     * @return ModuleTemplate
     */
    protected function initializeModuleTemplate(
        ServerRequestInterface $request,
    ): ModuleTemplate {
        $view = $this->moduleTemplateFactory->create($request);

        $context = '';
        $this->modifyDocHeaderComponent($view, $context);
        $view->setFlashMessageQueue($this->getFlashMessageQueue());
        $view->setTitle(
            $this->getLanguageService()->sL('LLL:EXT:bc_simplerate/Resources/Private/Language/locallang_modadministration.xlf:mlang_tabs_tab'),
            $context,
        );

        return $view;
    }
    
    /**
     * Method modifyDocHeaderComponent
     *
     * @param ModuleTemplate $view 
     * @param string $context
     *
     * @return void
     */
    private function modifyDocHeaderComponent(ModuleTemplate $view, string &$context): void
    {
        $menu = $this->buildMenu($view, $context);
        $view->getDocHeaderComponent()->getMenuRegistry()->addMenu($menu);
 
        $metaInformation = $this->getMetaInformation();
        if (is_array($metaInformation)) {
            $view->getDocHeaderComponent()->setMetaInformation($metaInformation);
        }
    }
     
    /**
     * Method getLanguageService
     *
     * @return LanguageService
     */
    protected function getLanguageService(): LanguageService
    {
        return $this->languageServiceFactory->createFromUserPreferences($GLOBALS['BE_USER']);
    }
    
    /**
     * Method buildMenu
     *
     * @param ModuleTemplate $view 
     * @param string $context 
     *
     * @return Menu
     */
    private function buildMenu(ModuleTemplate $view, string &$context): Menu
    {
        $menuItems = [
            'index' => [
                'controller' => 'Administration',
                'action' => 'index',
                'label' => $this->getLanguageService()->sL('LLL:EXT:bc_simplerate/Resources/Private/Language/locallang_modadministration.xlf:administration.menu.index'),
            ],
            'ratingresults' => [
                'controller' => 'Administration',
                'action' => 'ratingResults',
                'label' => $this->getLanguageService()->sL('LLL:EXT:bc_simplerate/Resources/Private/Language/locallang_modadministration.xlf:administration.menu.ratingResults'),
            ],
        ];

        $menu = $view->getDocHeaderComponent()->getMenuRegistry()->makeMenu();
        $menu->setIdentifier('BcSimplerateModuleMenu');

        foreach ($menuItems as $menuItemConfig) {
            $isActive = $this->request->getControllerActionName() === $menuItemConfig['action'];
            $menuItem = $menu->makeMenuItem()
                ->setTitle($menuItemConfig['label'])
                ->setHref($this->uriBuilder->reset()->uriFor(
                    $menuItemConfig['action'],
                    [],
                    $menuItemConfig['controller'],
                ))
                ->setActive($isActive);
            $menu->addMenuItem($menuItem);
            if ($isActive) {
                $context = $menuItemConfig['label'];
            }
        }
        return $menu;
    }
}