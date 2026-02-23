<?php

declare(strict_types=1);

namespace Sitegeist\PermitA38\Application\ClientManagement\Controller;

use Neos\Flow\Mvc\View\ViewInterface;
use Neos\Flow\Security\Cryptography\HashService;
use Neos\Fusion\View\FusionView;
use Neos\Neos\Controller\Module\AbstractModuleController;
use Sitegeist\Flow\OAuth2Server\Infrastructure\FlowClientEntity;
use Sitegeist\Flow\OAuth2Server\Infrastructure\FlowClientEntityRepository;
use Sitegeist\Flow\OAuth2Server\Infrastructure\FlowScopeEntity;

class ScopeManagementController extends AbstractModuleController
{
    public function __construct(
        private readonly FlowScopeEntity $clientRepository,
        private readonly HashService $hashService,
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
        $this->view->assign('clients', $this->clientRepository->findAll()->toArray());
    }

    public function creationFormAction(): void
    {
    }

    public function creationAction(
        string $clientId,
        ?string $clientSecret,
        ?string $name,
        string $redirectUri,
    ): void {
        $client = new FlowClientEntity();
        $client->setIdentifier($clientId);
        $client->setSecret($clientSecret ? $this->hashService->hashPassword($clientSecret) : null);
        if ($name) {
            $client->setName($name);
        }
        $client->setRedirectUri(trim($redirectUri));

        $this->clientRepository->add($client);

        $this->redirect('index');
    }

    public function updateFormAction(
        string $identity
    ): void {
        $this->view->assign('client', $this->clientRepository->findByIdentifier($identity));
    }

    public function updateAction(
        string $identity,
        ?string $name,
        string $redirectUri,
    ): void {
        $client = $this->clientRepository->findByIdentifier($identity);
        $client->setName($name);
        $client->setRedirectUri(trim($redirectUri));

        $this->clientRepository->update($client);

        $this->redirect('index');
    }

    public function updateSecretAction(
        string $identity,
        ?string $clientSecret,
    ): void {
        $client = $this->clientRepository->findByIdentifier($identity);
        $client->setSecret($clientSecret ? $this->hashService->hashPassword($clientSecret) : null);
        $this->clientRepository->update($client);
        $this->redirect('index');
    }

    public function deleteAction(string $clientId): void
    {
        $this->clientRepository->remove($this->clientRepository->getClientEntity($clientId));
        $this->redirect('index');
    }
}
