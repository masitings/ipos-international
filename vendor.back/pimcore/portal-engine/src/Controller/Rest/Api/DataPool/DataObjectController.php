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

namespace Pimcore\Bundle\PortalEngineBundle\Controller\Rest\Api\DataPool;

use Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse;
use Pimcore\Bundle\PortalEngineBundle\Enum\Permission;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataObjectConfig;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\DataObject\DetailHandler;
use Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\DataObject\ListHandler;
use Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\ErrorHandler;
use Pimcore\Document\Editable\Exception\NotFoundException;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\Version;
use Pimcore\Translation\Translator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/data-object", condition="request.attributes.get('isPortalEngineSite')")
 */
class DataObjectController extends AbstractDataPoolController
{
    /**
     * @var DataObjectConfig|null
     */
    protected $dataPoolConfig;

    /**
     * @Route("/list",
     *     name="pimcore_portalengine_rest_api_data_object_list"
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
     * @Route("/select-all-ids",
     *     name="pimcore_portalengine_rest_api_data_object_select_all_ids"
     * )
     *
     * @throws \Exception
     */
    public function listSelectAllIdsAction(Request $request, ListHandler $listHandler): JsonResponse
    {
        return new JsonResponse(
            [
                'success' => true,
                'data' => $listHandler->getSelectAllIds($request),
            ]
        );
    }

    /**
     * @Route("/list-filters",
     *     name="pimcore_portalengine_rest_api_data_object_list_filters"
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
     * @Route("/list-folders",
     *     name="pimcore_portalengine_rest_api_data_object_list_folders"
     * )
     */
    public function listFoldersAction(Request $request, ListHandler $listHandler): JsonResponse
    {
        return new JsonResponse(
            [
                'success' => true,
                'data' => $listHandler->getFoldersData($request),
            ]
        );
    }

    /**
     * @Route("/list-tags",
     *     name="pimcore_portalengine_rest_api_data_object_list_tags"
     * )
     */
    public function listTagsAction(Request $request, ListHandler $listHandler): JsonResponse
    {
        return new JsonResponse(
            [
                'success' => true,
                'data' => $listHandler->getTagsData($request),
            ]
        );
    }

    /**
     * @Route("/{id}",
     *     name="pimcore_portalengine_rest_api_data_object_detail",
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
        $this->denyAccessUnlessGranted(Permission::VIEW, $object);

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
     * @Route("/version-history/{id}",
     *     name="pimcore_portalengine_rest_api_data_object_version_history",
     *     requirements={
     *          "id": "\d+"
     *     }
     * )
     */
    public function versionHistoryAction(Request $request, DetailHandler $detailHandler, DataPoolConfigService $dataPoolConfigService): JsonResponse
    {
        $this->denyAccessUnlessGranted(Permission::VERSION_HISTORY);

        $id = $request->get('id');
        $object = AbstractObject::getById($id);

        if (empty($object)) {
            throw new NotFoundHttpException(sprintf('Data Object ID %s not found', $id));
        }

        return new JsonResponse(
            [
                'success' => true,
                'data' => $detailHandler->getVersionHistory($object, $dataPoolConfigService->getCurrentDataPoolConfig()),
            ]
        );
    }

    /**
     * @Route("/version-data",
     *     name="pimcore_portalengine_rest_api_data_object_version_data"
     * )
     */
    public function versionDataAction(Request $request, DetailHandler $detailHandler, DataPoolConfigService $dataPoolConfigService): JsonResponse
    {
        $ids = $request->get('ids');

        if (empty($ids)) {
            throw new NotFoundException('No version IDs given.');
        }

        $versions = array_filter(array_map(function ($id) {
            return Version::getById($id);
        }, $ids));

        if (empty($versions)) {
            throw new NotFoundHttpException(sprintf('Version ID %s not found', implode(', ', $ids)));
        }

        return new JsonResponse(
            [
                'success' => true,
                'data' => $detailHandler->getVersionData($versions, $dataPoolConfigService->getCurrentDataPoolConfig()),
            ]
        );
    }

    /**
     * Used for result list slider on data object detail pages.
     *
     * @Route("/detail-results-list/{id}",
     *     name="pimcore_portalengine_rest_api_data_object_detail_results_list"
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
