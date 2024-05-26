<?php

/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */

namespace Pimcore\Bundle\PortalEngineBundle\Service\Asset;

use Pimcore\Bundle\DirectEditBundle\Controller\FileEditControllerTrait;
use Pimcore\Bundle\DirectEditBundle\Service\AssetVersionService;
use Pimcore\Bundle\DirectEditBundle\Service\ClientCommunicationService;
use Pimcore\Bundle\DirectEditBundle\Service\FileService;
use Pimcore\Bundle\DirectEditBundle\Service\ModalRenderService;
use Pimcore\Bundle\DirectEditBundle\Service\TokenService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\DirectEditPermissionService;
use Pimcore\Controller\FrontendController;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class DirectEditConnector extends FrontendController implements DirectEditConnectorInterface
{
    use FileEditControllerTrait {
        FileEditControllerTrait::__construct as protected __fileEditConstruct;
    }

    protected $modalRenderService;
    protected $assetVersionService;

    public function __construct(
        ContainerInterface $container,
        RouterInterface $router,
        ClientCommunicationService $clientCommunicationService,
        TokenService $tokenService,
        FileService $fileService,
        ModalRenderService $modalRenderService,
        LoggerInterface $directEditLogger,
        DirectEditPermissionService $permissionService,
        AssetVersionService $assetVersionService
    ) {
        $this->container = $container;

        $this->__fileEditConstruct($router, $clientCommunicationService, $tokenService, $fileService, $modalRenderService, $directEditLogger, $permissionService);

        $this->modalRenderService = $modalRenderService;
        $this->assetVersionService = $assetVersionService;
    }

    public function generateLink(int $assetId)
    {
        return $this->generateLinkAction($assetId, $this->modalRenderService, $this->assetVersionService);
    }

    public function cancelEdit(int $assetId)
    {
        return $this->cancelEditAction($assetId);
    }

    public function confirmEdit(int $assetId, Request $request)
    {
        return $this->confirmEditAction($assetId, $request, $this->assetVersionService);
    }

    public function confirmOverwriteAfterLocalEdit(int $assetId)
    {
        return $this->confirmOverwriteAfterLocalEditAction($assetId, $this->assetVersionService);
    }

    public function confirmVersionSaveAfterLocalEdit(int $assetId)
    {
        return $this->confirmVersionSaveAfterLocalEditAction($assetId, $this->assetVersionService);
    }

    public function eventServerHasGone(int $assetId)
    {
        return $this->eventServerHasGoneAction($assetId);
    }
}
