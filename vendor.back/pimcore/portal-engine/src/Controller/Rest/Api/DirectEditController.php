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

namespace Pimcore\Bundle\PortalEngineBundle\Controller\Rest\Api;

use Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse;
use Pimcore\Bundle\DirectEditBundle\Exception\DirectEditException;
use Pimcore\Bundle\DirectEditBundle\Service\AssetVersionService;
use Pimcore\Bundle\DirectEditBundle\Service\ModalRenderService;
use Pimcore\Bundle\PortalEngineBundle\Service\Asset\DirectEditConnectorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/direct-edit", condition="request.attributes.get('isPortalEngineSite')")
 */
class DirectEditController extends AbstractRestApiController
{
    protected $directEditConnector;

    public function __construct(DirectEditConnectorInterface $directEditConnector)
    {
        $this->directEditConnector = $directEditConnector;
    }

    /**
     * @Route("/generate_link/{assetId}")
     *
     * @param int $assetId
     * @param ModalRenderService $modalRenderService
     * @param AssetVersionService $assetVersionService
     *
     * @return JsonResponse
     */
    public function generateLinkAction(int $assetId)
    {
        return $this->directEditConnector->generateLink($assetId);
    }

    /**
     * @Route("/cancel_edit/{assetId}")
     *
     * @param int $assetId
     *
     * @return JsonResponse
     */
    public function cancelEditAction(int $assetId)
    {
        return $this->directEditConnector->cancelEdit($assetId);
    }

    /**
     * @Route("/confirm_edit/{assetId}")
     *
     * @param int $assetId
     * @param Request $request
     * @param AssetVersionService $assetVersionService
     *
     * @return JsonResponse
     *
     * @throws DirectEditException
     */
    public function confirmEditAction(int $assetId, Request $request)
    {
        return $this->directEditConnector->confirmEdit($assetId, $request);
    }

    /**
     * @Route("/confirm_overwrite_after_local_edit/{assetId}")
     *
     * @param int $assetId
     * @param AssetVersionService $assetVersionService
     *
     * @return JsonResponse
     */
    public function confirmOverwriteAfterLocalEditAction(int $assetId)
    {
        return $this->directEditConnector->confirmOverwriteAfterLocalEdit($assetId);
    }

    /**
     * @Route("/confirm_versionsave_after_local_edit/{assetId}")
     *
     * @param int $assetId
     * @param AssetVersionService $assetVersionService
     *
     * @return JsonResponse
     */
    public function confirmVersionSaveAfterLocalEditAction(int $assetId)
    {
        return $this->directEditConnector->confirmVersionSaveAfterLocalEdit($assetId);
    }

    /**
     * @Route("/event_server_has_gone/{assetId}")
     *
     * @param int $assetId
     *
     * @return JsonResponse
     */
    public function eventServerHasGoneAction(int $assetId)
    {
        return $this->directEditConnector->eventServerHasGone($assetId);
    }
}
