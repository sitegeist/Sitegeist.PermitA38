<?php

declare(strict_types=1);

namespace Sitegeist\PermitA38\Application\ClientManagement\Controller;

use Neos\Error\Messages\Message;
use Neos\Flow\Mvc\View\ViewInterface;
use Neos\Flow\Security\Cryptography\HashService;
use Neos\Fusion\View\FusionView;
use Neos\Neos\Controller\Module\AbstractModuleController;
use Sitegeist\Flow\OAuth2Server\Infrastructure\Client;
use Sitegeist\Flow\OAuth2Server\Infrastructure\ClientRepository;

class ClientManagementController extends AbstractModuleController
{
    public function __construct(
        private readonly ClientRepository $clientRepository,
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
        $client = new Client();
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
        if (!$client) {
            $this->addFlashMessage(messageBody: 'Unknown client "' . $identity . '"', severity: Message::SEVERITY_WARNING);
        } else {
            $client->setName($name);
            $client->setRedirectUri(trim($redirectUri));
            $this->clientRepository->update($client);
            $this->addFlashMessage(messageBody: 'Client successfully updated');
        }

        $this->redirect('index');
    }

    public function updateSecretAction(
        string $identity,
        ?string $clientSecret,
    ): void {
        $client = $this->clientRepository->findByIdentifier($identity);
        if (!$client) {
            $this->addFlashMessage(messageBody: 'Unknown client "' . $identity . '"', severity: Message::SEVERITY_WARNING);
        } else {
            $client->setSecret($clientSecret ? $this->hashService->hashPassword($clientSecret) : null);
            $this->clientRepository->update($client);
            $this->addFlashMessage(messageBody: 'Client secret successfully updated');
        }
        $this->redirect('index');
    }

    public function deleteAction(string $clientId): void
    {
        $client = $this->clientRepository->getClientEntity($clientId);
        if (!$client) {
            $this->addFlashMessage(messageBody: 'Unknown client "' . $clientId . '"', severity: Message::SEVERITY_WARNING);
        } else {
            $this->clientRepository->remove($client);
            $this->addFlashMessage(messageBody: 'Client "' . $clientId . '" successfully removed');
        }
        $this->redirect('index');
    }
}
