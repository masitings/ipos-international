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
use Pimcore\Bundle\PortalEngineBundle\Enum\BatchTask\Payload\AssetMetadataUpdate;
use Pimcore\Bundle\PortalEngineBundle\Enum\DataPool\TranslatorDomain;
use Pimcore\Bundle\PortalEngineBundle\Enum\Download\DownloadContext;
use Pimcore\Bundle\PortalEngineBundle\Enum\Download\Type;
use Pimcore\Bundle\PortalEngineBundle\Enum\ImageThumbnails;
use Pimcore\Bundle\PortalEngineBundle\Enum\Permission;
use Pimcore\Bundle\PortalEngineBundle\Enum\TagsApplyMode;
use Pimcore\Bundle\PortalEngineBundle\Event\Download\DownloadAssetEvent;
use Pimcore\Bundle\PortalEngineBundle\Message\BatchTask\UpdateAssetMetadata\StartMessage;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\AssetConfig;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\Downloadable\DownloadableAsset;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadAccess;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadConfig;
use Pimcore\Bundle\PortalEngineBundle\Model\Rest\ApiPayload;
use Pimcore\Bundle\PortalEngineBundle\Service\Asset\AssetDeleteService;
use Pimcore\Bundle\PortalEngineBundle\Service\Asset\AssetRelocateService;
use Pimcore\Bundle\PortalEngineBundle\Service\Asset\MetadataService;
use Pimcore\Bundle\PortalEngineBundle\Service\Asset\UrlExtractorService;
use Pimcore\Bundle\PortalEngineBundle\Service\BatchTask\BatchTaskService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\TranslatorService;
use Pimcore\Bundle\PortalEngineBundle\Service\Download\Generator\OriginalAssetGenerator;
use Pimcore\Bundle\PortalEngineBundle\Service\Download\Generator\ThumbnailGenerator;
use Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\Asset\DetailHandler;
use Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\Asset\ListHandler;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\IndexQueueService;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Search\TagService;
use Pimcore\Bundle\PortalEngineBundle\Service\Workflow\WorkflowService;
use Pimcore\Model\Asset;
use Pimcore\Model\Element\Service;
use Pimcore\Model\Element\Tag;
use Pimcore\Model\Version;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/asset", condition="request.attributes.get('isPortalEngineSite')")
 */
class AssetController extends AbstractDataPoolController
{
    /**
     * @var AssetConfig|null
     */
    protected $dataPoolConfig;

    /**
     * @Route("/list",
     *     name="pimcore_portalengine_rest_api_asset_list"
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
     *     name="pimcore_portalengine_rest_api_asset_list_filters"
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
     *     name="pimcore_portalengine_rest_api_asset_list_folders"
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
     *     name="pimcore_portalengine_rest_api_asset_list_tags"
     * )
     */
    public function listTagsAction(Request $request, ListHandler $listHandler, TagService $tagService): JsonResponse
    {
        $data = (bool)$request->get('includeAll', false) ? $tagService->getTagTree([], [], true) : $listHandler->getTagsData($request);

        return new JsonResponse(
            [
                'success' => true,
                'data' => $data,
            ]
        );
    }

    /**
     * @Route("/select-all-ids",
     *     name="pimcore_portalengine_rest_api_asset_select_all_ids"
     * )
     *
     * @throws \Exception
     */
    public function listSelectAllIdsAction(Request $request, ListHandler $assetListHandler): JsonResponse
    {
        return new JsonResponse(
            [
                'success' => true,
                'data' => $assetListHandler->getSelectAllIds($request),
            ]
        );
    }

    /**
     * @Route(
     *     "/metadata-layout",
     *     name = "pimcore_portalengine_rest_api_asset_metadata_layout"
     * )
     */
    public function metadataLayoutAction(DetailHandler $detailHandler)
    {
        return new JsonResponse(
            [
                'success' => true,
                'data' => $detailHandler->getLayoutDefinitions()
            ]
        );
    }

    /**
     * @Route("/{id}",
     *     name="pimcore_portalengine_rest_api_asset_detail",
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

        $this->denyAccessUnlessGranted(Permission::VIEW, $asset);

        return new JsonResponse(
            [
                'success' => true,
                'data' => $assetDetailHandler->getData($asset),
            ]
        );
    }

    /**
     * @Route("/version-history/{id}",
     *     name="pimcore_portalengine_rest_api_asset_version_history",
     *     requirements={
     *          "id": "\d+"
     *     }
     * )
     */
    public function versionHistoryAction($id, Request $request, DetailHandler $detailHandler): JsonResponse
    {
        $this->denyAccessUnlessGranted(Permission::VERSION_HISTORY);

        $asset = Asset::getById($id);

        if (empty($asset)) {
            throw new NotFoundHttpException(sprintf('Data Object ID %s not found', $id));
        }

        return new JsonResponse(
            [
                'success' => true,
                'data' => $detailHandler->getVersionHistory($asset),
            ]
        );
    }

    /**
     * @Route("/version-comparison/{id}",
     *     name="pimcore_portalengine_rest_api_asset_version_comparison",
     *     requirements={
     *          "id": "\d+"
     *     }
     * )
     */
    public function versionComparisonAction($id, Request $request, DetailHandler $detailHandler): JsonResponse
    {
        $this->denyAccessUnlessGranted(Permission::VERSION_HISTORY);

        $asset = Asset::getById($id);
        $ids = $request->get('ids');

        if (empty($asset)) {
            throw new NotFoundHttpException(sprintf('Data Object ID %s not found', $id));
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
                'data' => $detailHandler->getVersionComparison($asset, $versions),
            ]
        );
    }

    /**
     * @Route("/publish-version/{id}",
     *     name="pimcore_portalengine_rest_api_asset_publish_version",
     *     requirements={
     *          "id": "\d+"
     *     }
     * )
     */
    public function publishVersionAction($id, Request $request, DetailHandler $detailHandler): JsonResponse
    {
        $this->denyAccessUnlessGranted(Permission::VERSION_HISTORY);

        $asset = Asset::getById($id);

        if (empty($asset)) {
            throw new NotFoundHttpException(sprintf('Data Object ID %s not found', $id));
        }

        $this->denyAccessUnlessGranted(Permission::EDIT, $asset);

        $version = Version::getById($request->get('versionId'));

        if (!$version || $version->getCid() !== $asset->getId() || $version->getCtype() !== Service::getElementType($asset)) {
            throw new NotFoundHttpException(sprintf('Version %s not found or is not a version of the given element.', $request->get('versionId')));
        }

        // publish new version
        $asset = $version->loadData();
        $asset->save();

        return new JsonResponse(new ApiPayload([]));
    }

    /**
     * Used for result list slider on data asset detail pages.
     *
     * @Route("/detail-results-list/{id}",
     *     name="pimcore_portalengine_rest_api_asset_detail_results_list"
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
     * @Route(
     *     "/save-metadata/{id}",
     *     name = "pimcore_portalengine_rest_api_asset_save_metadata"
     * )
     */
    public function saveMetadataAction($id, Request $request, MetadataService $metadataService, TranslatorService $translatorService)
    {
        $asset = Asset::getById($id);

        if (empty($asset)) {
            throw new NotFoundHttpException(sprintf('Asset ID %s not found', $id));
        }

        $this->denyAccessUnlessGranted(Permission::EDIT, $asset);

        $error = null;

        try {
            $metadataService->setMetadata($asset, json_decode($request->getContent(), true));
            $asset->save();
        } catch (\Exception $e) {
            $error = $translatorService->translate('error.could-not-save-metadata', TranslatorDomain::DOMAIN_ASSET);
        }

        return new JsonResponse(new ApiPayload([], $error));
    }

    /**
     * @Route(
     *     "/save-tags/{id}",
     *     name = "pimcore_portalengine_rest_api_asset_save_tags"
     * )
     */
    public function saveTagsAction($id, Request $request, MetadataService $metadataService, TranslatorService $translatorService)
    {
        $asset = Asset::getById($id);

        if (empty($asset)) {
            throw new NotFoundHttpException(sprintf('Asset ID %s not found', $id));
        }

        $this->denyAccessUnlessGranted(Permission::EDIT, $asset);

        $error = null;

        try {
            $content = json_decode($request->getContent(), true);
            $tags = $content['tags'];

            Tag::setTagsForElement('asset', $asset->getId(), array_filter(array_map(function ($tag) {
                return Tag::getById($tag['value']);
            }, $tags ?: [])));
        } catch (\Exception $e) {
            $error = $translatorService->translate('error.could-not-save-metadata', TranslatorDomain::DOMAIN_ASSET);
        }

        return new JsonResponse(new ApiPayload([], $error));
    }

    /**
     * @Route("/trigger-metadata-batch-update",
     *     name="pimcore_portalengine_rest_api_asset_trigger_metadata_batch_update"
     * )
     */
    public function triggerMetadataBatchUpdateAction(
        Request $request,
        MessageBusInterface $messageBus,
        BatchTaskService $batchTaskService,
        Security $security,
        DataPoolConfigService $dataPoolConfigService
    ) {
        $data = json_decode($request->getContent(), true);

        $ids = (array)$data['ids'] ?? [];
        $dataPoolId = $request->get('dataPoolId');
        $targetPage = $request->get('targetPage');
        $metadata = (array)$data['metadata'] ?? [];
        if (empty($ids) || empty($dataPoolId) || empty($metadata) || empty($targetPage)) {
            throw new BadRequestHttpException('ids, dataPoolId, metadata and targetPage params required');
        }

        $tags = (array)$data['tags'] ?? [];
        $tagsApplyMode = $data['tagsApplyMode'] ?? TagsApplyMode::ADD;

        $task = $batchTaskService->prepareBatchTask(
            $security->getUser()->getId(),
            \Pimcore\Bundle\PortalEngineBundle\Enum\BatchTask\Type::UPDATE_ASSET_METADATA,
            sizeof($ids),
            [
                AssetMetadataUpdate::TARGET_PAGE => $targetPage,
            ]
        );

        $msg = new StartMessage(
            $ids,
            $metadata,
            $tags,
            $tagsApplyMode,
            $dataPoolConfigService->getCurrentDataPoolConfig()->getId(),
            $task->getId()
        );
        $messageBus->dispatch($msg);

        return new JsonResponse([
            'success' => true
        ]);
    }

    /**
     * @Route("/delete/{id}",
     *     name="pimcore_portalengine_rest_api_asset_delete_asset"
     * )
     */
    public function deleteAssetAction($id, Request $request, IndexQueueService $indexQueueService): JsonResponse
    {
        $asset = Asset::getById($id);

        if (empty($asset)) {
            return new JsonResponse(
                [
                    'success' => false,
                    'error' => 'Asset not found'
                ]
            );
        }

        $this->denyAccessUnlessGranted(Permission::DELETE, $asset);

        /** @var bool $performIndexRefreshBackup */
        $performIndexRefreshBackup = $indexQueueService->isPerformIndexRefresh();
        $indexQueueService->setPerformIndexRefresh(true);

        $asset->delete();

        $indexQueueService->setPerformIndexRefresh($performIndexRefreshBackup);

        return new JsonResponse(
            [
                'success' => true
            ]
        );
    }

    /**
     * @Route("/trigger-batch-delete",
     *     name="pimcore_portalengine_rest_api_asset_trigger_batch_delete"
     * )
     *
     * @throws \Exception
     */
    public function triggerBatchDeleteAction(Request $request, AssetDeleteService $assetDeleteService): JsonResponse
    {
        $ids = (array)explode(',', (string)$request->get('ids'));
        $dataPoolId = $request->get('dataPoolId');
        $targetPage = $request->get('targetPage');

        if (empty($ids) || empty($dataPoolId) || empty($targetPage)) {
            throw new BadRequestHttpException('ids, dataPoolId and targetPage params required');
        }

        $asBatchTask = $assetDeleteService->triggerAssetDeletion($ids, $dataPoolId, $targetPage);

        return new JsonResponse(
            [
                'success' => true,
                'batchTask' => $asBatchTask
            ]
        );
    }

    /**
     * @Route("/relocate/{id}",
     *     name="pimcore_portalengine_rest_api_asset_relocate_asset"
     * )
     *
     * @param $id
     * @param Request $request
     * @param IndexQueueService $indexQueueService
     * @param AssetRelocateService $assetRelocateService
     * @param DataPoolConfigService $dataPoolConfigService
     * @param UrlExtractorService $urlExtractorService
     *
     * @return JsonResponse
     *
     * @throws \Exception
     */
    public function relocateAssetAction($id, Request $request, IndexQueueService $indexQueueService, AssetRelocateService $assetRelocateService, DataPoolConfigService $dataPoolConfigService, UrlExtractorService $urlExtractorService): JsonResponse
    {
        $targetFolderPath = $request->get('targetFolder');
        if (empty($targetFolderPath)) {
            throw new BadRequestHttpException('targetFolder param required');
        }

        $asset = Asset::getById($id);

        if (empty($asset)) {
            return new JsonResponse(
                [
                    'success' => false,
                    'error' => 'Asset not found'
                ]
            );
        }

        $this->denyAccessUnlessGranted(Permission::UPDATE, $asset);

        $targetFolder = Asset\Folder::getByPath(str_replace('//', '/', $targetFolderPath));

        if (empty($targetFolder)) {
            return new JsonResponse(
                [
                    'success' => false,
                    'error' => 'Target folder not found'
                ]
            );
        }

        $this->denyAccessUnlessGranted(Permission::CREATE, $targetFolder);

        /** @var bool $performIndexRefreshBackup */
        $performIndexRefreshBackup = $indexQueueService->isPerformIndexRefresh();
        $indexQueueService->setPerformIndexRefresh(true);
        $success = $assetRelocateService->relocateAsset($asset, $targetFolder, $dataPoolConfigService->getCurrentDataPoolConfig()->getId());
        $indexQueueService->setPerformIndexRefresh($performIndexRefreshBackup);

        if (!$success) {
            return new JsonResponse(
                [
                    'success' => false,
                    'error' => 'Could not relocate'
                ]
            );
        }

        $responsePayload = [
            'success' => true
        ];

        //relocate from detail page - need to full reload page in order to update upload folder state
        if ($request->get('fromDetailPage') === 'true') {
            $responsePayload['redirectUrl'] = $urlExtractorService->extractUrl($asset, $dataPoolConfigService->getCurrentDataPoolConfig(), ['folder' => $targetFolder->getRealFullPath()]);
        }

        return new JsonResponse($responsePayload);
    }

    /**
     * @Route("/trigger-batch-relocate",
     *     name="pimcore_portalengine_rest_api_asset_trigger_batch_relocate"
     * )
     *
     * @throws \Exception
     */
    public function triggerBatchRelocateAction(Request $request, AssetRelocateService $assetRelocateService): JsonResponse
    {
        $ids = (array)explode(',', (string)$request->get('ids'));
        $dataPoolId = $request->get('dataPoolId');
        $targetPage = $request->get('targetPage');
        $targetFolderPath = $request->get('targetFolder');
        if (empty($ids) || empty($dataPoolId) || empty($targetPage) || empty($targetFolderPath)) {
            throw new BadRequestHttpException('ids, dataPoolId, targetPage and targetFolder params required');
        }

        $targetFolder = Asset\Folder::getByPath(str_replace('//', '/', $targetFolderPath));
        if (empty($targetFolder)) {
            return new JsonResponse(
                [
                    'success' => false,
                    'error' => 'Target folder not found'
                ]
            );
        }

        $asBatchTask = $assetRelocateService->triggerAssetRelocate($ids, $dataPoolId, $targetPage, $targetFolder);

        return new JsonResponse(
            [
                'success' => true,
                'batchTask' => $asBatchTask
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

    /**
     * @Route("/download/{id}",
     *     name="pimcore_portalengine_rest_api_asset_download",
     *     requirements={
     *          "id": "\d+"
     *     })
     */
    public function downloadAction($id, Request $request, OriginalAssetGenerator $originalAssetGenerator, ThumbnailGenerator $thumbnailGenerator, DataPoolConfigService $dataPoolConfigService, EventDispatcherInterface $eventDispatcher)
    {
        $asset = Asset::getById($id);
        $thumbnailName = $request->get('thumbnail', OriginalAssetGenerator::ORIGINAL_FORMAT);
        $thumbnail = $thumbnailName === OriginalAssetGenerator::ORIGINAL_FORMAT ? null : $thumbnailName;

        if (empty($asset)) {
            throw new NotFoundHttpException(sprintf('Asset ID %s not found', $id));
        }

        $dataPoolConfig = $dataPoolConfigService->getCurrentDataPoolConfig();
        if (!$dataPoolConfig instanceof AssetConfig) {
            throw new NotFoundHttpException('Asset Data Pool not found');
        }

        $this->denyAccessUnlessGranted(Permission::VIEW, $asset);
        $downloadAccess = DownloadAccess::create($dataPoolConfig->getId(), Type::ASSET, null, [$thumbnailName]);
        $this->denyAccessUnlessGranted(Permission::DOWNLOAD, $downloadAccess);

        /**
         * @var DownloadableAsset $downloadable
         */
        $downloadable = (new DownloadableAsset())
            ->setGenerator(is_null($thumbnail) ? $originalAssetGenerator : $thumbnailGenerator)
            ->setAsset($asset)
            ->setDataPoolConfig($dataPoolConfigService->getCurrentDataPoolConfig())
            ->setDownloadUniqid(uniqid())
            ->setDownloadConfig((new DownloadConfig())->setFormat($thumbnail))
        ;

        if ($thumbnail) {
            $downloadable->setThumbnail($thumbnail);
        }

        $file = $downloadable->generate()->getDownloadFilePath();
        $filename = $downloadable->getDownloadFileName();

        $event = new DownloadAssetEvent($downloadable, DownloadContext::DIRECT_DOWNLOAD_SHORTCUT);
        $eventDispatcher->dispatch($event);

        return (new BinaryFileResponse($file))
            ->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
    }

    /**
     * @Route("/document-preview/{id}/{page}",
     *     name="pimcore_portalengine_rest_api_asset_document_preview",
     *     requirements={
     *          "id": "\d+",
     *          "page": "\d+",
     *     })
     */
    public function documentPreviewAction($id, $page)
    {
        $asset = Asset::getById($id);

        if (empty($asset) || !$asset instanceof Asset\Document) {
            throw new NotFoundHttpException(sprintf('Asset ID %s not found', $id));
        }

        $this->denyAccessUnlessGranted(Permission::VIEW, $asset);

        return $this->json(new ApiPayload([
            'thumbnail' => $asset->getImageThumbnail(ImageThumbnails::ELEMENT_DETAIL, $page)->getPath()
        ]));
    }

    /**
     * @Route("/workflow/{id}",
     *     name="pimcore_portalengine_rest_api_asset_workflow",
     *     requirements={
     *          "id": "\d+"
     *     }
     * )
     */
    public function workflowAction(int $id, WorkflowService $workflowService)
    {
        $asset = Asset::getById($id);

        if (empty($asset)) {
            throw new NotFoundHttpException(sprintf('Asset ID %s not found', $id));
        }

        $this->denyAccessUnlessGranted(Permission::VIEW, $asset);

        return new JsonResponse(
            [
                'success' => true,
                'data' => $workflowService->getWorkflowDetails($asset)
            ]
        );
    }

    /**
     * @Route("/workflow/apply-transition/{id}",
     *     name="pimcore_portalengine_rest_api_asset_apply_workflow_transition",
     *     requirements={
     *          "id": "\d+"
     *     }
     * )
     *
     * @throws \Exception
     */
    public function applyTransitionAction(int $id, Request $request, WorkflowService $workflowService)
    {
        $asset = Asset::getById($id);

        if (empty($asset)) {
            throw new NotFoundHttpException(sprintf('Asset ID %s not found', $id));
        }

        $this->denyAccessUnlessGranted(Permission::VIEW, $asset);

        $requestPayload = json_decode($request->getContent(), true);
        $data = $requestPayload['data'] ?? [];

        if ($requestPayload['type'] == 'globalAction') {
            $error = $workflowService->applyGlobalAction($asset, $requestPayload['workflow'], $requestPayload['transition'], $data);
        } else {
            $error = $workflowService->applyTransition($asset, $requestPayload['workflow'], $requestPayload['transition'], $data);
        }

        $responseParams = [
            'success' => is_null($error)
        ];
        if (!is_null($error)) {
            $responseParams['error'] = $error;
        }

        return new JsonResponse(
            $responseParams
        );
    }
}
