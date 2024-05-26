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
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolService;
use Pimcore\Bundle\PortalEngineBundle\Service\Download\DownloadProviderService;
use Pimcore\Bundle\PortalEngineBundle\Service\PublicShare\PublicShareService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/public-share", condition="request.attributes.get('isPortalEngineSite')")
 */
class DetailActionsController extends AbstractRestApiController
{
    /**
     * @Route(
     *     "/detail-actions",
     *     name="pimcore_portalengine_rest_api_public_share_detail_actions"
     * )
     *
     * @throws \Exception
     */
    public function getPublicShareDetailActionsAction(PublicShareService $publicShareService, DownloadProviderService $downloadProviderService, DataPoolService $dataPoolService, DataPoolConfigService $dataPoolConfigService): JsonResponse
    {
        $actions = [];

        $dataPoolConfigs = $publicShareService->getDataPoolConfigsByPublicShare($this->publicShare);
        if (sizeof($dataPoolConfigs)) {
            $downloadPools = [];
            foreach ($dataPoolConfigs as $dataPoolConfig) {
                if (!sizeof($downloadProviderService->getPublicShareAllowedDownloadTypes($dataPoolConfig, $this->publicShare))) {
                    continue;
                }
                $downloadPools[] = [
                    'dataPoolId' => $dataPoolConfig->getId(),
                    'name' => $dataPoolConfig->getDataPoolName()
                ];
            }
            if (sizeof($downloadPools)) {
                $actions['download'] = $downloadPools;
            }
        }

        return new JsonResponse([
            'success' => true,
            'data' => [
                'actions' => $actions
            ]
        ]);
    }
}
