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
use Pimcore\Bundle\PortalEngineBundle\Enum\BatchTask\Payload\Download;
use Pimcore\Bundle\PortalEngineBundle\Enum\BatchTask\Payload\PublicShare;
use Pimcore\Bundle\PortalEngineBundle\Enum\Download\DownloadContext;
use Pimcore\Bundle\PortalEngineBundle\Enum\Permission;
use Pimcore\Bundle\PortalEngineBundle\Event\Download\DownloadAssetEvent;
use Pimcore\Bundle\PortalEngineBundle\Event\Download\DownloadZipFilenameEvent;
use Pimcore\Bundle\PortalEngineBundle\Message\BatchTask\Download\StartMessage;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\Downloadable\DownloadableAsset;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\Downloadable\DownloadableStructuredData;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadItem;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadSize;
use Pimcore\Bundle\PortalEngineBundle\Model\Rest\ApiPayload;
use Pimcore\Bundle\PortalEngineBundle\Service\BatchTask\BatchTaskService;
use Pimcore\Bundle\PortalEngineBundle\Service\Collection\CollectionService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DownloadFormatHandler;
use Pimcore\Bundle\PortalEngineBundle\Service\Download\DownloadCartService;
use Pimcore\Bundle\PortalEngineBundle\Service\Download\DownloadProviderService;
use Pimcore\Bundle\PortalEngineBundle\Service\Download\DownloadService;
use Pimcore\Bundle\PortalEngineBundle\Service\Download\DownloadZipGenerationService;
use Pimcore\Bundle\PortalEngineBundle\Service\Download\SizeEstimation\AsyncSizeEstimationService;
use Pimcore\Bundle\PortalEngineBundle\Service\Entity\EntityManagerService;
use Pimcore\Bundle\PortalEngineBundle\Service\PublicShare\PublicShareService;
use Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\Download\DetailHandler;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\SecurityService;
use Pimcore\Bundle\PortalEngineBundle\Service\Zip\ZipArchiveService;
use Pimcore\Localization\LocaleServiceInterface;
use Pimcore\Model\Element\Service;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/download", condition="request.attributes.get('isPortalEngineSite')")
 */
class DownloadController extends AbstractRestApiController
{
    /**
     * @Route(
     *     "/download-types",
     *     name="pimcore_portalengine_rest_api_download_download_types"
     * )
     */
    public function downloadTypesAction(Request $request, DataPoolConfigService $dataPoolConfigService, DownloadProviderService $downloadProviderService)
    {
        /** @var array $downloadTypes */
        $downloadTypes = [];

        $dataPoolConfigService->setCurrentDataPoolConfigById($request->get('dataPoolId'));
        if ($dataPoolConfigService->getCurrentDataPoolConfig()) {
            $downloadTypes = $downloadProviderService->getDownloadTypes($dataPoolConfigService->getCurrentDataPoolConfig());
        }

        return new JsonResponse(
            new ApiPayload($downloadTypes)
        );
    }

    /**
     * @Route(
     *     "/add-to-download-cart",
     *     name = "pimcore_portalengine_rest_api_download_add_to_download_cart"
     * )
     *
     * @throws \Exception
     */
    public function addToDownloadCartAction(Request $request, DataPoolConfigService $dataPoolConfigService, DownloadCartService $downloadCartService, DetailHandler $detailHandler, EntityManagerService $entityManagerService, AuthorizationCheckerInterface $authorizationChecker)
    {
        $dataPoolId = $request->get('dataPoolId');
        $dataPoolConfigService->setCurrentDataPoolConfigById($dataPoolId);

        $configs = $detailHandler->getDownloadConfigsFromRequest($request);
        $elements = $detailHandler->getSelectedElementsFromRequest($request);

        if (!empty($elements)) {
            foreach ($elements as $element) {
                if (!$authorizationChecker->isGranted(Permission::DOWNLOAD, $element)) {
                    continue;
                }
                $downloadCartService->addItemToDownloadCart(
                    $dataPoolConfigService->getCurrentDataPoolConfig(),
                    $element,
                    $configs,
                    false
                );
            }

            $entityManagerService->flush();
        }

        return new JsonResponse(
            new ApiPayload($detailHandler->getData($request))
        );
    }

    /**
     * @Route(
     *     "/remove-from-download-cart",
     *     name = "pimcore_portalengine_rest_api_download_remove_from_download_cart"
     * )
     */
    public function removeFromDownloadCartAction(Request $request, DownloadCartService $downloadCartService, DetailHandler $detailHandler)
    {
        $downloadCartService->removeItemFromDownloadCart($request->get('itemKey'));

        return new JsonResponse(
            new ApiPayload($detailHandler->getData($request))
        );
    }

    /**
     * @Route(
     *     "/update-download-cart",
     *     name = "pimcore_portalengine_rest_api_download_update_download_cart"
     * )
     */
    public function updateDownloadCartAction(
        Request $request,
        DownloadCartService $downloadCartService,
        DetailHandler $detailHandler,
        EntityManagerService $entityManagerService,
        DataPoolConfigService $dataPoolConfigService
    ) {
        $item = $downloadCartService->getItemByItemKey($request->get('itemKey'));

        if (!$item) {
            return new JsonResponse(
                new ApiPayload(null, 'cart item not found')
            );
        }
        $dataPoolConfigService->setCurrentDataPoolConfigById($item->getDataPoolId());
        $configs = $detailHandler->getDownloadConfigsFromRequest($request);
        $item->setConfigs($configs);

        $entityManagerService->persist($item);

        return new JsonResponse(
            new ApiPayload($detailHandler->getDownloadCartItemData($item))
        );
    }

    /**
     * @Route(
     *     "/download-cart",
     *     name = "pimcore_portalengine_rest_api_download_download_cart"
     * )
     *
     * @throws \Exception
     */
    public function downloadCartDetailAction(Request $request, DownloadCartService $downloadCartService, DetailHandler $detailHandler)
    {
        return new JsonResponse(
            new ApiPayload($detailHandler->getData($request))
        );
    }

    /**
     * @Route(
     *     "/clear-cart",
     *     name = "pimcore_portalengine_rest_api_clear_cart"
     * )
     */
    public function clearCartAction(Request $request, DownloadCartService $downloadCartService, DetailHandler $detailHandler)
    {
        $downloadCartService->clearDownloadCart();

        return new JsonResponse([
            'success' => true
        ]);
    }

    /**
     * @Route(
     *     "/single-download/{elementId}",
     *     name = "pimcore_portalengine_rest_api_download_single_download"
     * )
     *
     * @throws \Exception
     */
    public function singleDownloadAction(
        Request $request,
        DataPoolConfigService $dataPoolConfigService,
        DetailHandler $detailHandler,
        ZipArchiveService $zipArchiveService,
        DownloadZipGenerationService $downloadZipGenerationService,
        EventDispatcherInterface $eventDispatcher,
        DownloadService $downloadService,
        DownloadFormatHandler $downloadFormatHandler,
        int $elementId
    ) {
        $dataPoolId = $request->get('dataPoolId');
        $dataPoolConfigService->setCurrentDataPoolConfigById($dataPoolId);
        $dataPoolConfig = $dataPoolConfigService->getCurrentDataPoolConfig();

        $element = Service::getElementById($dataPoolConfig->getElementType(), $elementId);

        if (empty($element)) {
            throw new NotFoundHttpException(sprintf('Element ID %s not found', $elementId));
        }

        $this->denyAccessUnlessGranted(Permission::DOWNLOAD, $element);

        $configs = $detailHandler->getDownloadConfigsFromRequest($request);

        $downloadItem = (new DownloadItem())
            ->setElementId($elementId)
            ->setElementType($dataPoolConfig->getElementType())
            ->setDataPoolId($dataPoolId)
            ->setConfigs($configs);

        $downloadUniqid = uniqid();
        $downloadables = $downloadService->getDownloadablesFromDownloadItem($downloadItem, $downloadUniqid);
        $downloadableCount = sizeof($downloadables);

        foreach ($downloadables as $downloadable) {
            if ($downloadable instanceof DownloadableAsset) {
                $event = new DownloadAssetEvent($downloadable, DownloadContext::SINGLE_DOWNLOAD);
                $eventDispatcher->dispatch($event);
            }
        }

        if ($downloadableCount === 0) {
            return new JsonResponse(['success' => false, 'error' => 'no-downloadable-files']);
        } elseif ($downloadableCount === 1) {
            $downloadable = array_pop($downloadables);
            if ($downloadable instanceof DownloadableStructuredData) {
                $downloadable->generate();
                $downloadFormat = $downloadable->getDownloadFormat();
                $downloadUniqid = $downloadable->getDownloadUniqid();
                $downloadFormat = $downloadFormatHandler->getDownloadFormatService($downloadFormat);
                if (empty($downloadFormat)) {
                    return new JsonResponse(['success' => false, 'error' => 'download-format-not-found']);
                }
                $fileSystemPath = $downloadFormat->bundle($downloadUniqid);

                return (new BinaryFileResponse($fileSystemPath))
                    ->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $downloadFormat->getDownloadFilename($downloadUniqid))
                    ->deleteFileAfterSend();
            } else {
                $fileSystemPath = $downloadable->generate()->getDownloadFilePath();

                return (new BinaryFileResponse($fileSystemPath))
                    ->deleteFileAfterSend($downloadable->shouldDeleteAfter())
                    ->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $downloadable->getDownloadFileName());
            }
        } else {
            $zipId = uniqid();
            $zipArchiveService->createEmptyZipByZipId($zipId);

            $bundleStructuredData = $downloadZipGenerationService->addDownloadItemToZip($downloadItem, $zipId);
            $downloadZipGenerationService->bundleStructuredDataDownloadFormatsIntoZip($bundleStructuredData, $zipId);

            $zipFile = $zipArchiveService->getZipFilesytemPathByZipId($zipId);

            $filename = $downloadZipGenerationService->getDefaultDownloadFilename();
            $event = new DownloadZipFilenameEvent($filename);
            $eventDispatcher->dispatch($event);
            $filename = $event->getFilename();

            $response = (new BinaryFileResponse($zipFile))
                ->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename)
                ->deleteFileAfterSend();

            return $response;
        }
    }

    /**
     * @Route(
     *     "/multi-download-trigger-download-estimation",
     *     name = "pimcore_portalengine_rest_api_download_multi_download_trigger_download_estimation"
     * )
     *
     * @throws \Exception
     */
    public function multiDownloadTriggerDownloadEstimationAction(
        Request $request,
        AsyncSizeEstimationService $asyncSizeEstimationService,
        DetailHandler $detailHandler,
        DataPoolConfigService $dataPoolConfigService
    ) {
        $dataPoolId = $request->query->get('dataPoolId');
        $dataPoolConfigService->setCurrentDataPoolConfigById($dataPoolId);
        $dataPoolConfig = $dataPoolConfigService->getCurrentDataPoolConfig();

        $configs = $detailHandler->getDownloadConfigsFromRequest($request);

        $downloadItems = [];
        foreach ($request->query->get('ids', []) as $elementId) {
            $downloadItems[] = (new DownloadItem())
                ->setElementId($elementId)
                ->setElementType($dataPoolConfig->getElementType())
                ->setDataPoolId($dataPoolId)
                ->setConfigs($configs);
        }

        $tmpStoreKey = $asyncSizeEstimationService->startEstimation($downloadItems, DownloadContext::MULTI_DOWNLOAD);

        return new JsonResponse([
            'success' => true,
            'tmpStoreKey' => $tmpStoreKey
        ]);
    }

    /**
     * @Route(
     *     "/download-cart-trigger-download-estimation",
     *     name = "pimcore_portalengine_rest_api_download_cart_trigger_download_estimation"
     * )
     */
    public function downloadCartTriggerDownloadEstimationAction(
        AsyncSizeEstimationService $asyncSizeEstimationService,
        DownloadCartService $downloadCartService
    ) {
        $tmpStoreKey = $asyncSizeEstimationService->startEstimation($downloadCartService->getDownloadCartItems(), DownloadContext::CART);

        return new JsonResponse([
            'success' => true,
            'tmpStoreKey' => $tmpStoreKey
        ]);
    }

    /**
     * @Route(
     *     "/collection-trigger-download-estimation",
     *     name = "pimcore_portalengine_rest_collection_trigger_download_estimation"
     * )
     *
     * @throws \Exception
     */
    public function collectionTriggerDownloadEstimationAction(
        Request $request,
        AsyncSizeEstimationService $asyncSizeEstimationService,
        DataPoolConfigService $dataPoolConfigService,
        DetailHandler $detailHandler,
        CollectionService $collectionService
    ) {
        $dataPoolId = $request->query->get('dataPoolId');
        $dataPoolConfigService->setCurrentDataPoolConfigById($dataPoolId);
        $dataPoolConfig = $dataPoolConfigService->getCurrentDataPoolConfig();

        if (!$collection = $collectionService->getCollectionById($request->query->get('collectionId'))) {
            throw new NotFoundHttpException('collection not found');
        }

        $configs = $detailHandler->getDownloadConfigsFromRequest($request);

        $downloadItems = [];
        foreach ($collectionService->getItemIdsByDataPool($collection, $dataPoolConfig) as $elementId) {
            $downloadItems[] = (new DownloadItem())
                ->setElementId($elementId)
                ->setElementType($dataPoolConfig->getElementType())
                ->setDataPoolId($dataPoolId)
                ->setConfigs($configs);
        }

        $tmpStoreKey = $asyncSizeEstimationService->startEstimation($downloadItems, DownloadContext::COLLECTION);

        return new JsonResponse([
            'success' => true,
            'tmpStoreKey' => $tmpStoreKey
        ]);
    }

    /**
     * @Route(
     *     "/get-estimation-result",
     *     name = "pimcore_portalengine_rest_api_download_get_estimation_result"
     * )
     */
    public function getEstimationResultAction(
        Request $request,
        AsyncSizeEstimationService $asyncSizeEstimationService,
        LocaleServiceInterface $localeService,
        TranslatorInterface $translator
    ) {
        $tmpStoreKey = $request->query->get('tmpStoreKey');
        $estimationResult = $asyncSizeEstimationService->getEstimationResult($tmpStoreKey);

        $data = [
            'success' => !is_null($estimationResult),
            'finished' => $estimationResult instanceof DownloadSize
        ];

        if ($estimationResult instanceof DownloadSize) {
            $data['triggerMessageType'] = null;

            if ($asyncSizeEstimationService->fileSizeTooBig($tmpStoreKey)) {
                $data['triggerMessageType'] = 'filesize-too-big';
                $data['triggerMessage'] = $translator->trans(
                    'portal-engine.download.filesize-too-big-message',
                    [':max-size:' => $asyncSizeEstimationService->getRejectSizeString()]
                );
            } elseif ($asyncSizeEstimationService->fileSizeWarning($tmpStoreKey)) {
                $data['triggerMessageType'] = 'filesize-warning';
                $data['triggerMessage'] = $translator->trans(
                    'portal-engine.download.filesize-warning',
                    [':max-size:' => $asyncSizeEstimationService->getWarningSizeString()]
                );
            }
        }

        return new JsonResponse($data);
    }

    /**
     * @Route(
     *     "/trigger-download",
     *     name = "pimcore_portalengine_rest_api_download_trigger_download"
     * )
     */
    public function triggerDownloadAction(
        Request $request,
        MessageBusInterface $messageBus,
        BatchTaskService $batchTaskService,
        AsyncSizeEstimationService $asyncSizeEstimationService,
        TranslatorInterface $translator,
        SecurityService $securityService,
        PublicShareService $publicShareService
    ) {
        $tmpStoreKey = $request->query->get('tmpStoreKey');
        $downloadItems = $asyncSizeEstimationService->getDownloadItems($tmpStoreKey);

        if (is_null($downloadItems)) {
            return new JsonResponse([
                'success' => false,
                'error' => $translator->trans('portal-engine.download-invalid-message')
            ]);
        }

        $downloadContext = $asyncSizeEstimationService->getDownloadContext($tmpStoreKey);
        $asyncSizeEstimationService->cleanupTmpStore($tmpStoreKey);

        $payload = [
            Download::ZIP_ID => uniqid(),
        ];
        if ($hash = $publicShareService->getCurrentPublicShareHash()) {
            $payload[PublicShare::PUBLIC_SHARE_HASH] = $hash;
        }

        $task = $batchTaskService->prepareBatchTask(
            $securityService->getPortalUser()->getPortalUserId(),
            \Pimcore\Bundle\PortalEngineBundle\Enum\BatchTask\Type::DOWNLOAD_GENERATION,
            sizeof($downloadItems),
            $payload
        );

        $msg = new StartMessage($downloadItems, $downloadContext, $task->getId());
        $messageBus->dispatch($msg);

        return new JsonResponse([
            'success' => true
        ]);
    }
}
