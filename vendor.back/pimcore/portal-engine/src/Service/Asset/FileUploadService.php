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

use Pimcore\Bundle\PortalEngineBundle\Enum\ImageThumbnails;
use Pimcore\Bundle\PortalEngineBundle\Enum\Index\DatabaseConfig;
use Pimcore\Bundle\PortalEngineBundle\Enum\Permission;
use Pimcore\Bundle\PortalEngineBundle\Event\Asset\Upload\PostAssetCreateEvent;
use Pimcore\Bundle\PortalEngineBundle\Event\Asset\Upload\PreAssetCreateEvent;
use Pimcore\Bundle\PortalEngineBundle\Exception\Asset\FileUploadException;
use Pimcore\Bundle\PortalEngineBundle\Exception\OutputErrorException;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\AssetConfig;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataPoolConfigInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\DataObject\PortalUserInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\Rest\Api\Asset\Upload\AssetUploadList;
use Pimcore\Bundle\PortalEngineBundle\Model\Rest\Api\Asset\Upload\AssetUploadListEntry;
use Pimcore\Bundle\PortalEngineBundle\Service\Asset\FileUpload\AssetListService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\Element\TagsService;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\IndexQueueService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\PermissionService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\SecurityService;
use Pimcore\Bundle\PortalEngineBundle\Service\Zip\ZipArchiveService;
use Pimcore\File;
use Pimcore\Model\Asset;
use Pimcore\Model\Element\Service;
use Pimcore\Tool;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class FileUploadService
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\Asset
 */
class FileUploadService
{
    /** @var EventDispatcherInterface */
    protected $eventDispatcher;
    /** @var SecurityService */
    protected $securityService;
    /** @var DataPoolConfigService */
    protected $dataPoolConfigService;
    /** @var PermissionService */
    protected $permissionService;
    /** @var AssetListService */
    protected $assetListService;
    /** @var IndexQueueService */
    protected $indexQueueService;
    /** @var ZipArchiveService */
    protected $zipArchiveService;
    /** @var TranslatorInterface */
    protected $translator;
    /** @var UrlGeneratorInterface */
    protected $urlGenerator;
    /** @var MetadataService */
    protected $metadataService;
    /** @var ThumbnailService */
    protected $thumbnailService;
    /** @var TagsService */
    protected $tagsService;

    /** @var string */
    protected $assetUploadListId;
    /** @var Asset\Folder */
    protected $assetFolder;
    /** @var array */
    protected $assetMetadata = [];
    /** @var int[] */
    protected $assetTagIds = [];
    /** @var string|null */
    protected $assetFilename;

    /** @var OutputErrorException|null */
    protected $outputErrorException = null;
    /** @var bool */
    protected $stopUpload = false;

    /**
     * FileUploadService constructor.
     *
     * @param EventDispatcherInterface $eventDispatcher
     * @param SecurityService $securityService
     * @param DataPoolConfigService $dataPoolConfigService
     * @param PermissionService $permissionService
     * @param AssetListService $assetListService
     * @param IndexQueueService $indexQueueService
     * @param ZipArchiveService $zipArchiveService
     * @param TranslatorInterface $translator
     * @param UrlGeneratorInterface $urlGenerator
     * @param MetadataService $metadataService
     * @param ThumbnailService $thumbnailService
     * @param TagsService $tagsService
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, SecurityService $securityService, DataPoolConfigService $dataPoolConfigService, PermissionService $permissionService, AssetListService $assetListService, IndexQueueService $indexQueueService, ZipArchiveService $zipArchiveService, TranslatorInterface $translator, UrlGeneratorInterface $urlGenerator, MetadataService $metadataService, ThumbnailService $thumbnailService, TagsService $tagsService)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->securityService = $securityService;
        $this->dataPoolConfigService = $dataPoolConfigService;
        $this->permissionService = $permissionService;
        $this->assetListService = $assetListService;
        $this->indexQueueService = $indexQueueService;
        $this->zipArchiveService = $zipArchiveService;
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
        $this->metadataService = $metadataService;
        $this->thumbnailService = $thumbnailService;
        $this->tagsService = $tagsService;
    }

    /**
     * @param UploadedFile $file
     *
     * @return Asset|null
     *
     * @throws OutputErrorException
     */
    public function addAssetByFile(UploadedFile $file)
    {
        /** @var Asset|null $asset */
        $asset = null;
        /** @var string $sourcePath */
        $sourcePath = $file->getRealPath();

        try {
            /** @var Asset\Folder $parentAsset */
            $parentAsset = $this->assetFolder;

            if (!$parentAsset) {
                throw new \Exception('asset upload folder not set');
            }
            if (!$this->assetListService->getListById($this->assetUploadListId)) {
                throw new \Exception('asset upload list not found');
            }

            /** @var string $clientOriginalFileName */
            $clientOriginalFileName = $file->getClientOriginalName();
            /** @var string $filename */
            $filename = $this->extractFilename($clientOriginalFileName);

            if (!is_file($sourcePath)) {
                throw new FileUploadException($this->translator->trans('portal-engine.asset-upload.error.max-filesize-or-write-permission'), $clientOriginalFileName);
            }
            if (is_file($sourcePath) && filesize($sourcePath) < 1) {
                throw new FileUploadException($this->translator->trans('portal-engine.asset-upload.error.file-empty'), $clientOriginalFileName);
            }
            if (!$this->permissionService->isPermissionAllowed(Permission::CREATE, $this->securityService->getPortalUser(), $this->dataPoolConfigService->getCurrentDataPoolConfig()->getId(), $parentAsset->getRealFullPath(), false, true, true, true)) {
                throw new FileUploadException($this->translator->trans('portal-engine.asset-upload.error.create-not-allowed'), $clientOriginalFileName);
            }

            // check for duplicate filename
            $filename = $this->getSafeFilename($parentAsset->getRealFullPath(), $filename);

            $asset = $this->createAsset($parentAsset->getId(), $filename, file_get_contents($sourcePath));
        } catch (\Exception $e) {
            $this->handleException($e);
        }

        $this
            ->deleteFile($sourcePath)
            ->throwOutputErrorException();

        return $asset;
    }

    /**
     * @param string $url
     *
     * @return Asset|null
     *
     * @throws OutputErrorException
     */
    public function addAssetByUrl(string $url)
    {
        /** @var Asset|null $asset */
        $asset = null;

        try {
            /** @var Asset\Folder $parentAsset */
            $parentAsset = $this->assetFolder;

            if (!$parentAsset) {
                throw new \Exception('asset upload folder not set');
            }
            if (!$this->assetListService->getListById($this->assetUploadListId)) {
                throw new \Exception('asset upload list not found');
            }

            /** @var string $filename */
            $filename = $this->extractFilename(basename($url));
            /** @var string|false $fileData */
            $fileData = Tool::getHttpData($url);

            if (!$fileData) {
                throw new FileUploadException($this->translator->trans('portal-engine.asset-upload.error.file-empty'), $filename);
            }
            if (!$this->permissionService->isPermissionAllowed(Permission::CREATE, $this->securityService->getPortalUser(), $this->dataPoolConfigService->getCurrentDataPoolConfig()->getId(), $parentAsset->getRealPath())) {
                throw new FileUploadException($this->translator->trans('portal-engine.asset-upload.error.create-not-allowed'), $filename);
            }

            // check for duplicate filename
            $filename = $this->getSafeFilename($parentAsset->getRealFullPath(), $filename);

            $asset = $this->createAsset($parentAsset->getId(), $filename, $fileData);
        } catch (\Exception $e) {
            $this->handleException($e);
        }

        $this->throwOutputErrorException();

        return $asset;
    }

    /**
     * @param string $zipId
     * @param int $zipIndex
     *
     * @return Asset|null
     *
     * @throws OutputErrorException
     */
    public function addAssetFromZip(string $zipId, int $zipIndex)
    {
        /** @var Asset|null $asset */
        $asset = null;
        /** @var bool $deleteZip */
        $deleteZip = false;
        /** @var string $zipFileSystemPath */
        $zipFileSystemPath = $this->zipArchiveService->getZipFilesytemPathByZipId($zipId);

        try {
            /** @var Asset\Folder $parentAsset */
            $parentAsset = $this->assetFolder;

            if (!$parentAsset) {
                throw new \Exception('asset upload folder not set');
            }
            if (!$this->assetListService->getListById($this->assetUploadListId)) {
                throw new \Exception('asset upload list not found');
            }
            if (!$this->permissionService->isPermissionAllowed(Permission::CREATE, $this->securityService->getPortalUser(), $this->dataPoolConfigService->getCurrentDataPoolConfig()->getId(), $parentAsset->getRealFullPath())) {
                throw new FileUploadException($this->translator->trans('portal-engine.asset-upload.error.create-not-allowed'));
            }
            if (!file_exists($zipFileSystemPath)) {
                throw new \Exception('zip file not found');
            }

            /** @var string|null $filePath */
            $filePath = null;
            /** @var string|false $fileData */
            $fileData = false;

            /** @var \ZipArchive $zip */
            $zip = new \ZipArchive;
            if ($zip->open($zipFileSystemPath) === true) {
                if (($zipIndex + 1) > $zip->numFiles) {
                    $zip->close();
                    throw new \Exception('zipIndex out of range');
                }

                $filePath = $zip->getNameIndex($zipIndex);
                $fileData = $zip->getFromIndex($zipIndex);

                $deleteZip = ($zipIndex + 1) == $zip->numFiles;

                $zip->close();
            }

            if (!$fileData) {
                throw new \Exception('file is empty or a folder');
            }

            /** @var string $relativePath */
            $relativePath = '';
            if (dirname($filePath) != '.') {
                $relativePath = dirname($filePath);
            }
            /** @var string $parentAssetPath */
            $parentAssetPath = $parentAsset->getRealFullPath() . '/' . preg_replace('@^/@', '', $relativePath);

            $parentAsset = $this->createFolderByPath($parentAssetPath);

            /** @var string $filename */
            $filename = $this->getSafeFilename($parentAsset->getRealFullPath(), $this->extractFilename(basename($filePath)));

            $asset = $this->createAsset($parentAsset->getId(), $filename, $fileData);
        } catch (\Exception $e) {
            $this->handleException($e);
        }

        if ($deleteZip) {
            $this->deleteFile($zipFileSystemPath);
        }
        $this->throwOutputErrorException();

        return $asset;
    }

    /**
     * @param UploadedFile $file
     *
     * @return array
     *
     * @throws OutputErrorException
     */
    public function createAssetZip(UploadedFile $file)
    {
        /** @var array $resultData */
        $resultData = [
            'zipId' => null,
            'zipNumFiles' => 0
        ];

        try {
            /** @var Asset\Folder $parentAsset */
            $parentAsset = $this->assetFolder;
            /** @var string $sourcePath */
            $sourcePath = $file->getRealPath();

            if (!$parentAsset) {
                throw new \Exception('asset upload folder not set');
            }
            if (!is_file($sourcePath)) {
                throw new OutputErrorException($this->translator->trans('portal-engine.asset-upload.error.max-filesize-or-write-permission'));
            }
            if (is_file($sourcePath) && filesize($sourcePath) < 1) {
                throw new OutputErrorException($this->translator->trans('portal-engine.asset-upload.error.file-empty'));
            }
            if (!$this->permissionService->isPermissionAllowed(Permission::CREATE, $this->securityService->getPortalUser(), $this->dataPoolConfigService->getCurrentDataPoolConfig()->getId(), $parentAsset->getRealFullPath())) {
                throw new OutputErrorException($this->translator->trans('portal-engine.asset-upload.error.create-not-allowed'));
            }

            /** @var string $zipId */
            $zipId = uniqid('upload_asset_zip_');
            /** @var string $zipFileSystemPath */
            $zipFileSystemPath = $this->zipArchiveService->getZipFilesytemPathByZipId($zipId);

            copy($file->getRealPath(), $zipFileSystemPath);

            /** @var \ZipArchive $zip */
            $zip = new \ZipArchive;
            if ($zip->open($zipFileSystemPath) === true) {
                $resultData['zipId'] = $zipId;
                $resultData['zipNumFiles'] = $zip->numFiles;
                $zip->close();
            }

            if (0 == $resultData['zipNumFiles']) {
                $this->deleteFile($zipFileSystemPath);
                throw new OutputErrorException($this->translator->trans('portal-engine.asset-upload.error.file-empty'));
            }
        } catch (\Exception $e) {
            $this->handleException($e);
        }

        $this->throwOutputErrorException();

        return $resultData;
    }

    /**
     * @param Asset $asset
     * @param UploadedFile $file
     *
     * @return Asset|null
     *
     * @throws OutputErrorException
     */
    public function replaceAssetByFile(Asset $asset, UploadedFile $file)
    {
        /** @var string $sourcePath */
        $sourcePath = $file->getRealPath();

        try {
            if (!is_file($sourcePath)) {
                throw new OutputErrorException($this->translator->trans('portal-engine.asset-upload.error.max-filesize-or-write-permission'));
            }
            if (is_file($sourcePath) && filesize($sourcePath) < 1) {
                throw new OutputErrorException($this->translator->trans('portal-engine.asset-upload.error.file-empty'));
            }
            if (!$this->permissionService->isPermissionAllowed(Permission::EDIT, $this->securityService->getPortalUser(), $this->dataPoolConfigService->getCurrentDataPoolConfig()->getId(), $asset->getRealPath())) {
                throw new OutputErrorException($this->translator->trans('portal-engine.asset-upload.error.create-not-allowed'));
            }

            /** @var string $newFilename */
            $newFilename = Service::getValidKey($file->getClientOriginalName(), 'asset');
            /** @var string $mimeType */
            $mimeType = MimeTypeGuesser::getInstance()->guess($sourcePath);

            /** @var string $newType */
            $newType = Asset::getTypeFromMimeMapping($mimeType, $newFilename);
            if ($newType != $asset->getType()) {
                throw new OutputErrorException($this->translator->trans('portal-engine.asset-upload.error.replace-type-change-not-allowed'));
            }

            $asset
                ->setData(file_get_contents($sourcePath))
                ->setCustomSetting('thumbnails', null)
                ->setUserModification($this->securityService->getPortalUser()->getPimcoreUser());

            $newFileExt = File::getFileExtension($newFilename);
            $currentFileExt = File::getFileExtension($asset->getFilename());
            if ($newFileExt != $currentFileExt) {
                $newFilename = preg_replace('/\.' . $currentFileExt . '$/i', '.' . $newFileExt, $asset->getFilename());
                $newFilename = Service::getSaveCopyName('asset', $newFilename, $asset->getParent());
                $asset->setFilename($newFilename);
            }

            $asset->save();
        } catch (\Exception $e) {
            $this->handleException($e);
        }

        $this
            ->deleteFile($sourcePath)
            ->throwOutputErrorException();

        return $asset;
    }

    /**
     * @param int $parentAssetId
     * @param string $filename
     * @param string $data
     *
     * @return Asset|null
     *
     * @throws \Exception
     */
    protected function createAsset(int $parentAssetId, string $filename, string $data)
    {
        /** @var Asset|null $asset */
        $asset = null;
        /** @var AssetUploadList $assetUploadList */
        $assetUploadList = $this->assetListService->getListById($this->assetUploadListId);

        try {
            if (!$assetUploadList->isAddEntryAllowed()) {
                throw new \Exception('add entry to upload list not allowed');
            }

            /** @var PortalUserInterface $portalUser */
            $portalUser = $this->securityService->getPortalUser();

            $asset = Asset::create($parentAssetId, [
                'filename' => $filename,
                'data' => $data,
                'userOwner' => $portalUser->getPimcoreUser(),
                'userModification' => $portalUser->getPimcoreUser(),
            ], false);

            /** @var AssetUploadListEntry $assetUploadListEntry */
            $assetUploadListEntry = (new AssetUploadListEntry())
                ->setName($asset->getFilename());

            /** @var PreAssetCreateEvent $preAssetCreateEvent */
            $preAssetCreateEvent = new PreAssetCreateEvent($asset);
            $this->eventDispatcher->dispatch($preAssetCreateEvent);

            $this
                ->assetListService
                ->addGlobalMessages($this->assetUploadListId, $preAssetCreateEvent->getGlobalMessages());

            $assetUploadListEntry
                ->setMessage($preAssetCreateEvent->getAssetListEntryMessage());

            if ($preAssetCreateEvent->isCancelCurrentUpload() || $preAssetCreateEvent->isCancelWholeUpload()) {
                $this
                    ->assetListService
                    ->addEntry($this->assetUploadListId, $assetUploadListEntry);

                $this->stopUpload = true;

                if ($preAssetCreateEvent->isCancelWholeUpload()) {
                    $this
                        ->assetListService
                        ->rollbackList($this->assetUploadListId);
                }

                throw new \Exception('stop createAsset after cancel current or whole upload in PreAssetCreateEvent');
            }

            $asset->save();

            $this->assignTagsOnAsset($asset);

            /** @var bool $performIndexRefreshBackup */
            $performIndexRefreshBackup = $this->indexQueueService->isPerformIndexRefresh();
            $this->indexQueueService->setPerformIndexRefresh(true);
            $this->indexQueueService->updateIndexQueue($asset, DatabaseConfig::QUEUE_TABLE_COLUMN_OPERATION_UPDATE, true);
            $this->indexQueueService->setPerformIndexRefresh($performIndexRefreshBackup);

            if (!empty($this->assetMetadata)) {
                $this
                    ->metadataService
                    ->setMetadata($asset, $this->assetMetadata, true);

                $asset->save();
            }

            $assetUploadListEntry
                ->setName($asset->getKey())
                ->setAssetId($asset->getId())
                ->setFullPath($this->thumbnailService->getThumbnailPath($asset, ImageThumbnails::ELEMENT_TEASER, true))
                ->setDetailLink($this->urlGenerator->generate('pimcore_portalengine_asset_detail', [
                    'id' => $asset->getId(),
                    'documentPath' => ltrim((string)$this->dataPoolConfigService->getCurrentDataPoolConfig()->getDocument(), '/')
                ]));

            /** @var PostAssetCreateEvent $postAssetCreateEvent */
            $postAssetCreateEvent = new PostAssetCreateEvent($asset);
            $this->eventDispatcher->dispatch($postAssetCreateEvent);

            $this
                ->assetListService
                ->addGlobalMessages($this->assetUploadListId, $postAssetCreateEvent->getGlobalMessages());

            if (!$preAssetCreateEvent->getAssetListEntryMessage()) {
                $assetUploadListEntry
                    ->setMessage($postAssetCreateEvent->getAssetListEntryMessage());
            }

            $this
                ->assetListService
                ->addEntry($this->assetUploadListId, $assetUploadListEntry);

            if ($postAssetCreateEvent->isCancelCurrentUpload()) {
                $this
                    ->assetListService
                    ->rollbackListEntry($this->assetUploadListId, $assetUploadListEntry);

                $this->stopUpload = true;
            }
            if ($postAssetCreateEvent->isCancelWholeUpload()) {
                $this
                    ->assetListService
                    ->rollbackList($this->assetUploadListId);

                $this->stopUpload = true;
            }
        } catch (\Exception $e) {
            // nothing to do
        }

        return $asset;
    }

    /**
     * @param string $path
     *
     * @return Asset|Asset\Folder|null
     *
     * @throws \Exception
     */
    protected function createFolderByPath(string $path)
    {
        /** @var Asset\Folder|null $lastFolder */
        $lastFolder = null;
        /** @var PortalUserInterface $portalUser */
        $portalUser = $this->securityService->getPortalUser();

        $pathsArray = [];
        $parts = explode('/', $path);
        $parts = array_filter($parts, function ($var) {
            return strlen($var) > 0;
        });

        $sanitizedPath = '/';

        $itemType = 'asset';

        foreach ($parts as $part) {
            $sanitizedPath = $sanitizedPath . Service::getValidKey($part, $itemType) . '/';
        }

        if (Service::pathExists($sanitizedPath, $itemType)) {
            return Asset::getByPath($sanitizedPath);
        }

        foreach ($parts as $part) {
            $pathPart = $pathsArray[count($pathsArray) - 1] ?? '';
            $pathsArray[] = $pathPart . '/' . Service::getValidKey($part, $itemType);
        }

        for ($i = 0; $i < count($pathsArray); $i++) {
            $currentPath = $pathsArray[$i];
            if (!Service::pathExists($currentPath, $itemType)) {
                $parentFolderPath = ($i == 0) ? '/' : $pathsArray[$i - 1];

                $parentFolder = Asset::getByPath($parentFolderPath);

                $folder = new Asset\Folder();
                $folder->setParent($parentFolder);
                if ($parentFolder) {
                    $folder->setParentId($parentFolder->getId());
                } else {
                    $folder->setParentId(1);
                }

                $key = substr($currentPath, strrpos($currentPath, '/') + 1, strlen($currentPath));

                $folder->setKey($key);
                $folder->setFilename($key);
                $folder->setType('folder');
                $folder->setPath($currentPath);
                $folder->setUserModification($portalUser->getPimcoreUser());
                $folder->setUserOwner($portalUser->getPimcoreUser());
                $folder->setCreationDate(time());
                $folder->setModificationDate(time());
                $folder->save();

                $this->assetListService->addAssetFolder($this->assetUploadListId, $folder);

                $lastFolder = $folder;
            }
        }

        return $lastFolder;
    }

    /**
     * @param string|null $filename
     *
     * @return string
     *
     * @throws FileUploadException
     */
    protected function extractFilename($filename)
    {
        if (empty($filename)) {
            throw new FileUploadException($this->translator->trans('portal-engine.asset-upload.error.filename-empty'));
        }

        /** @var string $filenameExtension */
        $filenameExtension = pathinfo($filename, PATHINFO_EXTENSION);
        if (empty($filenameExtension)) {
            throw new FileUploadException($this->translator->trans('portal-engine.asset-upload.error.filename-extension-empty'));
        }

        if (!empty($this->assetFilename)) {

            /** @var string $assetFilenameExtension */
            $assetFilenameExtension = pathinfo($this->assetFilename, PATHINFO_EXTENSION);
            if (!empty($assetFilenameExtension)) {
                $filename = pathinfo($this->assetFilename, PATHINFO_BASENAME);
            } else {
                $filename = pathinfo($this->assetFilename, PATHINFO_FILENAME) . '.' . $filenameExtension;
            }
        }

        $filename = Service::getValidKey($filename, 'asset');
        if (empty($filename)) {
            throw new FileUploadException($this->translator->trans('portal-engine.asset-upload.error.filename-empty'));
        }

        return $filename;
    }

    /**
     * @param string $targetPath
     * @param string $filename
     *
     * @return string
     */
    protected function getSafeFilename($targetPath, $filename)
    {
        $pathinfo = pathinfo($filename);
        $originalFilename = $pathinfo['filename'];
        $originalFileextension = empty($pathinfo['extension']) ? '' : '.' . $pathinfo['extension'];
        $count = 1;

        if ($targetPath == '/') {
            $targetPath = '';
        }

        while (true) {
            if (Asset\Service::pathExists($targetPath . '/' . $filename)) {
                $filename = $originalFilename . '_' . $count . $originalFileextension;
                $count++;
            } else {
                return $filename;
            }
        }
    }

    /**
     * @param string $fileSystemPath
     *
     * @return $this
     */
    protected function deleteFile(string $fileSystemPath)
    {
        if (file_exists($fileSystemPath)) {
            @unlink($fileSystemPath);
        }

        return $this;
    }

    /**
     * @param Asset $asset
     *
     * @return $this
     */
    protected function assignTagsOnAsset(Asset $asset)
    {
        $this->tagsService->assignTagsOnElement($asset, $this->assetTagIds);

        return $this;
    }

    /**
     * @param \Exception $exception
     *
     * @return $this
     */
    protected function handleException($exception)
    {
        if ($exception instanceof OutputErrorException) {
            $this->outputErrorException = $exception;
        }
        if ($exception instanceof FileUploadException) {
            $this->assetListService->addException($this->assetUploadListId, $exception);
        }

        return $this;
    }

    /**
     * @return $this
     *
     * @throws OutputErrorException
     */
    protected function throwOutputErrorException()
    {
        /** @var OutputErrorException|null $outputErrorException */
        $outputErrorException = $this->outputErrorException;

        if ($outputErrorException instanceof OutputErrorException) {
            $this->outputErrorException = null;

            throw $outputErrorException;
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isStopUpload(): bool
    {
        return $this->stopUpload;
    }

    /**
     * @param $assetUploadListId
     *
     * @return $this
     */
    public function setAssetUploadListId($assetUploadListId)
    {
        $this->assetUploadListId = $assetUploadListId;

        return $this;
    }

    /**
     * @param Request $request
     *
     * @return $this
     */
    public function setUploadFolderByRequest(Request $request)
    {
        if ('true' === $request->get('uploadFolder')) {

            /** @var DataPoolConfigInterface $currentDataPoolConfig */
            $currentDataPoolConfig = $this->dataPoolConfigService->getCurrentDataPoolConfig();
            if ($currentDataPoolConfig instanceof AssetConfig) {
                $this->assetFolder = $currentDataPoolConfig->getUploadFolder();
            }
        } elseif (!empty($request->get('fullPath'))) {
            $this->assetFolder = Asset\Folder::getByPath($request->get('fullPath'));
        }

        return $this;
    }

    /**
     * @param array|null $assetMetadata
     *
     * @return FileUploadService
     */
    public function setAssetMetadata($assetMetadata): self
    {
        if (is_array($assetMetadata)) {
            $this->assetMetadata = $assetMetadata;
        }

        return $this;
    }

    /**
     * @param int[] $assetTagIds
     *
     * @return FileUploadService
     */
    public function setAssetTagIds(array $assetTagIds): self
    {
        $this->assetTagIds = $assetTagIds;

        return $this;
    }

    /**
     * @param string|null $assetFilename
     *
     * @return FileUploadService
     */
    public function setAssetFilename(?string $assetFilename): self
    {
        $this->assetFilename = $assetFilename;

        return $this;
    }
}
