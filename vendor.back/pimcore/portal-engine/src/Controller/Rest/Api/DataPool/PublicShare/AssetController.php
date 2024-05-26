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

namespace Pimcore\Bundle\PortalEngineBundle\Controller\Rest\Api\DataPool\PublicShare;

use Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\AssetConfig;
use Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\Asset\DetailHandler;
use Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\Asset\ListHandler;
use Pimcore\Model\Asset;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/public-share/asset", condition="request.attributes.get('isPortalEngineSite')")
 */
class AssetController extends AbstractDataPoolController
{
    /**
     * @Route("/list",
     *     name="pimcore_portalengine_rest_api_public_share_asset_list"
     * )
     *
     * @throws \Exception
     */
    public function listAction(Request $request, ListHandler $assetListHandler): JsonResponse
    {
        return new JsonResponse(
            [
                'success' => true,
                'data' => $assetListHandler->getData($request),
            ]
        );
    }

    /**
     * @Route("/list-filters",
     *     name="pimcore_portalengine_rest_api_public_share_asset_list_filters"
     * )
     */
    public function listFiltersAction(Request $request, ListHandler $listHandler): JsonResponse
    {
        return new JsonResponse(
            [
                'success' => true,
                'data' => $listHandler->getFiltersData($request),
            ]
        );
    }

    /**
     * @Route("/{id}",
     *     name="pimcore_portalengine_rest_api_public_share_asset_detail",
     *     requirements={
     *          "id": "\d+"
     *     }
     * )
     */
    public function detailAction($id, Request $request, DetailHandler $assetDetailHandler): JsonResponse
    {
        $asset = Asset::getById($id);

        if (empty($asset)) {
            throw new NotFoundHttpException(sprintf('Asset ID %s not found', $id));
        }
        if (!$this->publicShareService->isElementInPublicShare($this->publicShare, $asset)) {
            throw new NotFoundHttpException('Element not in PublicShare.');
        }

        return new JsonResponse(
            [
                'success' => true,
                'data' => $assetDetailHandler->getData($asset),
            ]
        );
    }

    /**
     * Used for result list slider on data asset detail pages.
     *
     * @Route("/detail-results-list/{id}",
     *     name="pimcore_portalengine_rest_api_public_share_asset_detail_results_list"
     * )
     */
    public function detailResultsListAction($id, Request $request, DetailHandler $detailHandler): JsonResponse
    {
        return new JsonResponse(
            [
                'success' => true,
                'data' => $detailHandler->getResultListData($id, $request->query->all()),
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function validateDataPoolConfig()
    {
        if (!$this->dataPoolConfig instanceof AssetConfig) {
            throw new NotFoundHttpException('Asset data pool config found.');
        }
    }
}
