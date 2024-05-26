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

namespace Pimcore\Bundle\PortalEngineBundle\Controller\Rest\Api\PublicShare;

use Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse;
use Pimcore\Bundle\PortalEngineBundle\Enum\Download\DownloadContext;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadItem;
use Pimcore\Bundle\PortalEngineBundle\Model\Rest\ApiPayload;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\Download\DownloadProviderService;
use Pimcore\Bundle\PortalEngineBundle\Service\Download\SizeEstimation\AsyncSizeEstimationService;
use Pimcore\Bundle\PortalEngineBundle\Service\PublicShare\PublicShareService;
use Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\Download\DetailHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/public-share/download", condition="request.attributes.get('isPortalEngineSite')")
 */
class DownloadController extends AbstractRestApiController
{
    /**
     * @Route(
     *     "/download-types",
     *     name="pimcore_portalengine_rest_api_public_share_download_download_types"
     * )
     */
    public function downloadTypesAction(Request $request, DataPoolConfigService $dataPoolConfigService, DownloadProviderService $downloadProviderService)
    {
        /** @var array $downloadTypes */
        $downloadTypes = [];

        $dataPoolConfigService->setCurrentDataPoolConfigById($request->get('dataPoolId'));
        if ($dataPoolConfig = $dataPoolConfigService->getCurrentDataPoolConfig()) {
            $downloadTypes = $downloadProviderService->getPublicShareAllowedDownloadTypes($dataPoolConfig, $this->publicShare);
        }

        return new JsonResponse(
            new ApiPayload($downloadTypes)
        );
    }

    /**
     * @Route(
     *     "/trigger-download-estimation",
     *     name = "pimcore_portalengine_rest_api_public_share_trigger_download_estimation"
     * )
     *
     * @throws \Exception
     */
    public function triggerDownloadEstimationAction(
        Request $request,
        AsyncSizeEstimationService $asyncSizeEstimationService,
        DataPoolConfigService $dataPoolConfigService,
        DetailHandler $detailHandler,
        PublicShareService $publicShareService
    ) {
        $dataPoolId = $request->query->get('dataPoolId');
        $dataPoolConfigService->setCurrentDataPoolConfigById($dataPoolId);
        $dataPoolConfig = $dataPoolConfigService->getCurrentDataPoolConfig();
        $configs = $detailHandler->getDownloadConfigsFromRequest($request);

        $downloadItems = [];
        foreach ($publicShareService->getItemIdsByDataPool($this->publicShare, $dataPoolConfig) as $elementId) {
            $downloadItems[] = (new DownloadItem())
                ->setElementId($elementId)
                ->setElementType($dataPoolConfig->getElementType())
                ->setDataPoolId($dataPoolId)
                ->setConfigs($configs);
        }

        $tmpStoreKey = $asyncSizeEstimationService->startEstimation($downloadItems, DownloadContext::GUEST_SHARE);

        return new JsonResponse([
            'success' => true,
            'tmpStoreKey' => $tmpStoreKey
        ]);
    }
}
