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
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataObjectConfig;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\DataObject\DetailHandler;
use Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\DataObject\ListHandler;
use Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\ErrorHandler;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Translation\Translator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/public-share/data-object", condition="request.attributes.get('isPortalEngineSite')")
 */
class DataObjectController extends AbstractDataPoolController
{
    /**
     * @Route("/list",
     *     name="pimcore_portalengine_rest_api_public_share_data_object_list"
     * )
     */
    public function listAction(Request $request, ListHandler $listHandler, ErrorHandler $errorHandler): JsonResponse
    {
        /** @var array $data */
        $data = $listHandler->getData($request);

        return new JsonResponse(
            [
                'success' => !$errorHandler->hasError(),
                'error' => $errorHandler->getErrorMessage(),
                'data' => $data
            ]
        );
    }

    /**
     * @Route("/list-filters",
     *     name="pimcore_portalengine_rest_api_public_share_data_object_list_filters"
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
     *     name="pimcore_portalengine_rest_api_public_share_data_object_detail",
     *     requirements={
     *          "id": "\d+"
     *     }
     * )
     */
    public function detailAction(Request $request, DetailHandler $dataObjectDetailHandler, DataPoolConfigService $dataPoolConfigService, Translator $translator): JsonResponse
    {
        $id = $request->get('id');
        $object = AbstractObject::getById($id);

        if (empty($object)) {
            throw new NotFoundHttpException(sprintf('Data Object ID %s not found', $id));
        }
        if (!$this->publicShareService->isElementInPublicShare($this->publicShare, $object)) {
            throw new NotFoundHttpException('Element not in PublicShare.');
        }

        $dataPoolConfig = $dataPoolConfigService->getCurrentDataPoolConfig();

        if (!$dataPoolConfig instanceof DataObjectConfig) {
            throw new NotFoundHttpException(sprintf('Data Pool Config ID "%s" is not a valid data pool config.', $request->query->get('dataPoolId')));
        }

        if (!$dataPoolConfig->isEnabled()) {
            return new JsonResponse([
                'success' => false,
                'error' => $translator->trans('data-object.data-pool-config-disabled')
            ]);
        }

        return new JsonResponse(
            [
                'success' => true,
                'data' => $dataObjectDetailHandler->getData($object, $dataPoolConfig),
            ]
        );
    }

    /**
     * Used for result list slider on data object detail pages.
     *
     * @Route("/detail-results-list/{id}",
     *     name="pimcore_portalengine_rest_api_public_share_data_object_detail_results_list"
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
        if (!$this->dataPoolConfig instanceof DataObjectConfig) {
            throw new NotFoundHttpException('Data object pool config found.');
        }
    }
}
