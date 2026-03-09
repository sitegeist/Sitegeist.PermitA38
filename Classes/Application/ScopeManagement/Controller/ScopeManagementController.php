<?php

declare(strict_types=1);

namespace Sitegeist\PermitA38\Application\ScopeManagement\Controller;

use Neos\Error\Messages\Message;
use Neos\Flow\Mvc\View\ViewInterface;
use Neos\Fusion\View\FusionView;
use Neos\Neos\Controller\Module\AbstractModuleController;
use Sitegeist\Flow\OAuth2Server\Infrastructure\Scope;
use Sitegeist\Flow\OAuth2Server\Infrastructure\ScopeRepository;

class ScopeManagementController extends AbstractModuleController
{
    public function __construct(
        private readonly ScopeRepository $scopeRepository,
    ) {
    }
    /**
     * @var string
     */
    protected $defaultViewObjectName = FusionView::class;

    protected function initializeView(ViewInterface $view): void
    {
        /** @var FusionView $view */
        $view->setOption('fusionPathPatterns', ['resource://Sitegeist.PermitA38/Private/Fusion/Backend']);
    }

    public function indexAction(): void
    {
        $this->view->assign('scopes', $this->scopeRepository->findAll()->toArray());
    }

    public function creationFormAction(): void
    {
    }

    public function creationAction(
        string $identifier,
        ?string $description,
    ): void {
        $scope = new Scope();
        $scope->setIdentifier($identifier);
        $scope->setDescription($description);

        $this->scopeRepository->add($scope);

        $this->redirect('index');
    }

    public function updateFormAction(
        string $identity
    ): void {
        $this->view->assign('scope', $this->scopeRepository->findByIdentifier($identity));
    }

    public function updateAction(
        string $identity,
        string $identifier,
        ?string $description,
    ): void {
        $scope = $this->scopeRepository->findByIdentifier($identity);
        if (!$scope) {
            $this->addFlashMessage(messageBody: 'Unknown scope "' . $identity . '"', severity: Message::SEVERITY_WARNING);
        } else {
            $scope->setIdentifier($identifier);
            $scope->setDescription($description);

            $this->scopeRepository->update($scope);
            $this->addFlashMessage(messageBody: 'Scope "' . $identity . '" successfully updated');
        }

        $this->redirect('index');
    }

    public function deleteAction(string $identity): void
    {
        $scope = $this->scopeRepository->findByIdentifier($identity);
        if (!$scope) {
            $this->addFlashMessage(messageBody: 'Unknown scope "' . $identity . '"', severity: Message::SEVERITY_WARNING);
        } else {
            $this->scopeRepository->remove($scope);
            $this->addFlashMessage(messageBody: 'Scope "' . $identity . '" successfully removed');
        }
        $this->redirect('index');
    }
}
