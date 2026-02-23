<?php

declare(strict_types=1);

namespace Sitegeist\PermitA38\Application\ServerConfiguration\Controller;

use Neos\Flow\Mvc\View\ViewInterface;
use Neos\Fusion\View\FusionView;
use Neos\Neos\Controller\Module\AbstractModuleController;

class ServerConfigurationController extends AbstractModuleController
{
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
    }
}
