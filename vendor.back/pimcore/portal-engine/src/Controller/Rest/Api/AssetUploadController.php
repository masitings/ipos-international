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

use Pimcore\Bundle\PortalEngineBundle\Model\Rest\Api\Asset\Upload\AssetUploadList;
use Pimcore\Bundle\PortalEngineBundle\Model\Rest\Api\Asset\Upload\AssetUploadListEntry;
use Pimcore\Bundle\PortalEngineBundle\Model\Rest\ApiPayload;
use Pimcore\Bundle\PortalEngineBundle\Service\Asset\FileUpload\AssetListService;
use Pimcore\Bundle\PortalEngineBundle\Service\Asset\FileUploadService;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Asset\IndexService;
use Pimcore\Model\Asset;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/asset/upload", condition="request.attributes.get('isPortalEngineSite')")
 */
class AssetUploadController extends AbstractRestApiController
{
    /** @var FileUploadService */
    protected $fileUploadService;
    /** @var AssetListService */
    protected $assetListService;
    /** @var IndexService */
    protected $assetIndexService;

    /**
     * AssetUploadController constructor.
     *
     * @param FileUploadService $fileUploadService
     * @param AssetListService $assetListService
     * @param IndexService $assetIndexService
     */
    public function __construct(FileUploadService $fileUploadService, AssetListService $assetListService, IndexService $assetIndexService)
    {
        $this->fileUploadService = $fileUploadService;
        $this->assetListService = $assetListService;
        $this->assetIndexService = $assetIndexService;
    }

    /**
     * @Route("/add-asset", name="pimcore_portalengine_rest_api_asset_upload_add_asset", methods={"POST"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function addAssetAction(Request $request)
    {
        /** @var ApiPayload $apiPayload */
        $apiPayload = new ApiPayload([]);

        try {
            $this->dataPoolConfigService->setCurrentDataPoolConfigById((int)$request->get('dataPoolId'));

            /** @var UploadedFile|null $file */
            $file = $request->files->get('Filedata');
            if (!$file || !$file->isFile()) {
                throw new \Exception('Filedata not set');
            }

            $this
                ->fileUploadService
                ->setUploadFolderByRequest($request)
                ->setAssetUploadListId($request->get('uploadId'))
                ->setAssetMetadata($request->get('metadata') ? json_decode($request->get('metadata'), true) : [])
                ->setAssetTagIds($request->get('tags') ? explode(',', $request->get('tags')) : [])
                ->setAssetFilename($request->get('filename'))
                ->addAssetByFile($file);

            $apiPayload->setData(['stopUpload' => $this->fileUploadService->isStopUpload()]);
        } catch (\Exception $e) {
            $apiPayload->handleOutputErrorException($e);
        }

        return new JsonResponse(
            $apiPayload
        );
    }

    /**
     * @Route("/replace-asset", name="pimcore_portalengine_rest_api_asset_upload_replace_asset", methods={"POST"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function replaceAssetAction(Request $request)
    {
        /** @var ApiPayload $apiPayload */
        $apiPayload = new ApiPayload([]);

        try {
            $this->dataPoolConfigService->setCurrentDataPoolConfigById((int)$request->get('dataPoolId'));

            /** @var UploadedFile|null $file */
            $file = $request->files->get('Filedata');
            if (!$file || !$file->isFile()) {
                throw new \Exception('Filedata not set');
            }

            /** @var Asset|null $asset */
            $asset = Asset::getById($request->get('assetId'));
            if (!$asset) {
                throw new \Exception(sprintf('asset with id %s not found', $request->get('assetId')));
            }

            $this
                ->fileUploadService
                ->replaceAssetByFile($asset, $file);
        } catch (\Exception $e) {
            $apiPayload->handleOutputErrorException($e);
        }

        return new JsonResponse(
            $apiPayload
        );
    }

    /**
     * @Route("/import-zip-file", name="pimcore_portalengine_rest_api_asset_upload_import_zip_file", methods={"POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function importZipFileAction(Request $request)
    {
        /** @var ApiPayload $apiPayload */
        $apiPayload = new ApiPayload([]);

        try {
            $this->dataPoolConfigService->setCurrentDataPoolConfigById((int)$request->get('dataPoolId'));

            /** @var string $zipId */
            $zipId = (string)$request->get('zipId');
            /** @var int $zipIndex */
            $zipIndex = (int)$request->get('zipIndex');

            $this->fileUploadService
                ->setUploadFolderByRequest($request)
                ->setAssetUploadListId($request->get('uploadId'))
                ->setAssetMetadata($request->get('metadata') ? json_decode($request->get('metadata'), true) : [])
                ->setAssetTagIds($request->get('tags') ? explode(',', $request->get('tags')) : [])
                ->setAssetFilename($request->get('filename'))
                ->addAssetFromZip($zipId, $zipIndex);

            $apiPayload->setData(['stopUpload' => $this->fileUploadService->isStopUpload()]);
        } catch (\Exception $e) {
            $apiPayload->handleOutputErrorException($e);
        }

        return new JsonResponse(
            $apiPayload
        );
    }

    /**
     * @Route("/import-url", name="pimcore_portalengine_rest_api_asset_upload_import_url", methods={"POST"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws \Exception
     */
    public function importUrlAction(Request $request)
    {
        /** @var ApiPayload $apiPayload */
        $apiPayload = new ApiPayload([]);

        try {
            $this->dataPoolConfigService->setCurrentDataPoolConfigById((int)$request->get('dataPoolId'));

            /** @var string|null $url */
            $url = $request->get('url');
            if (!$url) {
                throw new \Exception('Url not set');
            }

            $this
                ->fileUploadService
                ->setUploadFolderByRequest($request)
                ->setAssetUploadListId($request->get('uploadId'))
                ->setAssetMetadata($request->get('metadata') ? json_decode($request->get('metadata'), true) : [])
                ->setAssetTagIds($request->get('tags') ? explode(',', $request->get('tags')) : [])
                ->setAssetFilename($request->get('filename'))
                ->addAssetByUrl($url);

            $apiPayload->setData(['stopUpload' => $this->fileUploadService->isStopUpload()]);
        } catch (\Exception $e) {
            $apiPayload->handleOutputErrorException($e);
        }

        return new JsonResponse(
            $apiPayload
        );
    }

    /**
     * @Route("/import-zip", name="pimcore_portalengine_rest_api_asset_upload_import_zip", methods={"POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function importZipAction(Request $request)
    {
        /** @var ApiPayload $apiPayload */
        $apiPayload = new ApiPayload([]);

        try {
            $this->dataPoolConfigService->setCurrentDataPoolConfigById((int)$request->get('dataPoolId'));

            /** @var UploadedFile|null $file */
            $file = $request->files->get('Filedata');
            if (!$file || !$file->isFile()) {
                throw new \Exception('Filedata not set');
            }

            /** @var $resultData */
            $resultData = $this->fileUploadService
                ->setUploadFolderByRequest($request)
                ->createAssetZip($file);

            $apiPayload->setData($resultData);
        } catch (\Exception $e) {
            $apiPayload->handleOutputErrorException($e);
        }

        return new JsonResponse(
            $apiPayload
        );
    }

    /**
     * @Route("/start", name="pimcore_portalengine_rest_api_asset_upload_start", methods={"GET"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function startAction(Request $request)
    {
        return new JsonResponse(
            new ApiPayload(['uploadId' => $this->assetListService->createList()])
        );
    }

    /**
     * @Route("/finalize", name="pimcore_portalengine_rest_api_asset_upload_finalize", methods={"GET"})
     *
     * @param Request $requestRefreshIndexTrait
     *
     * @return JsonResponse
     */
    public function finalizeAction(Request $request)
    {
        /** @var array $apiPayloadData */
        $apiPayloadData = [];
        /** @var AssetUploadList|null $assetUploadList */
        $assetUploadList = $this->assetListService->getFinalAssetUploadListById((string)$request->get('uploadId'));
        if ($assetUploadList) {
            $apiPayloadData = $this->hydrateAssetUploadList($assetUploadList);

            if (!empty($assetUploadList->getEntries())) {
                $this->assetIndexService->refreshIndex('asset');
            }
        }

        return new JsonResponse(
            new ApiPayload($apiPayloadData)
        );
    }

    /**
     * @Route("/reset-list", name="pimcore_portalengine_rest_api_asset_upload_reset_list", methods={"POST"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function resetListAction(Request $request)
    {
        $this->assetListService->deleteListById($request->get('uploadId'));

        return new JsonResponse(
            new ApiPayload([])
        );
    }

    /**
     * @param AssetUploadList $assetUploadList
     *
     * @return array
     */
    protected function hydrateAssetUploadList(AssetUploadList $assetUploadList): array
    {
        /** @var string[] $hydratedAssetUploadEntries */
        $hydratedAssetUploadEntries = [];
        foreach ($assetUploadList->getEntries() as $assetUploadListEntry) {
            $hydratedAssetUploadEntries[] = $this->hydrateAssetUploadListEntry($assetUploadListEntry);
        }

        return [
            'messages' => $assetUploadList->getMessages(),
            'entries' => $hydratedAssetUploadEntries
        ];
    }

    /**
     * @param AssetUploadListEntry $assetUploadListEntry
     *
     * @return array
     */
    protected function hydrateAssetUploadListEntry(AssetUploadListEntry $assetUploadListEntry): array
    {
        return [
            'name' => $assetUploadListEntry->getName(),
            'message' => $assetUploadListEntry->getMessage(),
            'assetId' => $assetUploadListEntry->getAssetId(),
            'fullPath' => $assetUploadListEntry->getFullPath(),
            'detailLink' => $assetUploadListEntry->getDetailLink()
        ];
    }
}
