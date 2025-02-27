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
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;

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

    public function injectAdministrationRepository(AdministrationRepository $administrationRepository)
    {
        $this->administrationRepository = $administrationRepository;
    }

    public function injectRateRepository(RateRepository $rateRepository)
    {
        $this->rateRepository = $rateRepository;
    }

    /**
     * BackendController constructor. Takes care of dependency injection
     */
    public function __construct(
        protected readonly ModuleTemplateFactory $moduleTemplateFactory,
        private readonly IconFactory $iconFactory,
        private readonly LanguageServiceFactory $languageServiceFactory,
       
    ) {}

 
    public function addButtons(ButtonBar $buttonBar): void
    {
         
    }

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
     * Function will be called before every other action
     */
    protected function initializeAction(): void
    {
        $this->pageUid = (int)($this->request->getQueryParams()['id'] ?? 0);
    }

    /**
     * Index action for this controller
     */
    public function indexAction(int $currentPage = 1): ResponseInterface
    {
        $view = $this->initializeModuleTemplate($this->request);
        $allAvailableRates = $this->rateRepository->findAll();
        $paginator = new QueryResultPaginator(
            $allAvailableRates,
            $currentPage,
            3,
        );
        $pagination = new SimplePagination($paginator);

        $view->assignMultiple([
            'rates' => $allAvailableRates,
            'paginator' => $paginator,
            'pagination' => $pagination,
            'pages' => range(1, $pagination->getLastPageNumber()),
        ]);

        return $view->renderResponse('Index');
    }
 
    /**
     * Generates the action menu
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

    private function modifyDocHeaderComponent(ModuleTemplate $view, string &$context): void
    {
        $menu = $this->buildMenu($view, $context);
        $view->getDocHeaderComponent()->getMenuRegistry()->addMenu($menu);

        $buttonBar = $view->getDocHeaderComponent()->getButtonBar();
        $this->addButtons($buttonBar);

        $metaInformation = $this->getMetaInformation();
        if (is_array($metaInformation)) {
            $view->getDocHeaderComponent()->setMetaInformation($metaInformation);
        }
    }
 
    protected function getLanguageService(): LanguageService
    {
        return $this->languageServiceFactory->createFromUserPreferences($GLOBALS['BE_USER']);
    }

    private function buildMenu(ModuleTemplate $view, string &$context): Menu
    {
        $menuItems = [
            'index' => [
                'controller' => 'Administration',
                'action' => 'index',
                'label' => $this->getLanguageService()->sL('LLL:EXT:bc_simplerate/Resources/Private/Language/locallang.xlf:administration.menu.index'),
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