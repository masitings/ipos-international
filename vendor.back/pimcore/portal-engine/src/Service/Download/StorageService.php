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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Download;

use Carbon\Carbon;
use League\Flysystem\FilesystemOperator;
use Pimcore\Bundle\PortalEngineBundle\Service\Zip\ZipArchiveService;
use Pimcore\File;

class StorageService
{
    /**
     * @var FilesystemOperator
     */
    protected $storage;

    /**
     * @var ZipArchiveService
     */
    protected $zipArchiveService;

    /**
     * @param FilesystemOperator $pimcorePortalEngineDownloadStorage
     * @param ZipArchiveService $zipArchiveService
     */
    public function __construct(FilesystemOperator $pimcorePortalEngineDownloadStorage, ZipArchiveService $zipArchiveService)
    {
        $this->storage = $pimcorePortalEngineDownloadStorage;
        $this->zipArchiveService = $zipArchiveService;
    }

    /**
     * @param string $zipId
     *
     * @return string
     */
    protected function getZipFileName(string $zipId): string
    {
        return $zipId . '.zip';
    }

    /**
     * @param string $zipId
     *
     * @throws \League\Flysystem\FilesystemException
     */
    public function initLocalZipFileFromStorage(string $zipId): void
    {
        $filename = $this->getZipFileName($zipId);
        if ($this->storage->fileExists($filename)) {
            $localZipFile = $this->zipArchiveService->getZipFilesytemPathByZipId($zipId);
            $dest = fopen($localZipFile, 'wb', false, File::getContext());
            if (!$dest) {
                throw new \Exception(sprintf('Unable to create temporary file in %s', $localZipFile));
            }
            stream_copy_to_stream($this->storage->readStream($filename), $dest);
            fclose($dest);
        }
    }

    /**
     * @param string $zipId
     *
     * @throws \League\Flysystem\FilesystemException
     */
    public function commitLocalZipFileToStorage(string $zipId)
    {
        $localZipFile = $this->zipArchiveService->getZipFilesytemPathByZipId($zipId);
        $zipFileStream = fopen($localZipFile, 'r');
        $this->storage->writeStream($this->getZipFileName($zipId), $zipFileStream);
        fclose($zipFileStream);
        unlink($localZipFile);
    }

    /**
     * @param string $zipId
     *
     * @return resource|null
     *
     * @throws \League\Flysystem\FilesystemException
     */
    public function openStreamForZipFileFromStorage(string $zipId)
    {
        $filename = $this->getZipFileName($zipId);
        if ($this->storage->fileExists($filename)) {
            return $this->storage->readStream($filename);
        }

        return null;
    }

    /**
     * @param string $zipId
     *
     * @return bool
     *
     * @throws \League\Flysystem\FilesystemException
     */
    public function zipFileInStorageExists(string $zipId): bool
    {
        return $this->storage->fileExists($this->getZipFileName($zipId));
    }

    /**
     * @param string $zipId
     *
     * @throws \League\Flysystem\FilesystemException
     */
    public function cleanupZipFileInStorage(string $zipId)
    {
        $this->storage->delete($this->getZipFileName($zipId));
    }

    public function commitLocalSingleFileToStorage(string $zipId, string $localFileSystemPath): string
    {
        $fileStream = fopen($localFileSystemPath, 'r');
        $filename = $zipId . '/' . basename($localFileSystemPath);
        $this->storage->writeStream($filename, $fileStream);
        fclose($fileStream);

        return $filename;
    }

    public function openStreamForSingleFileFromStorage(string $filePath)
    {
        if ($this->storage->fileExists($filePath)) {
            return $this->storage->readStream($filePath);
        }

        return null;
    }

    public function cleanupSingleFileInStorage(string $filePath)
    {
        $this->storage->delete($filePath);

        $folder = dirname($filePath);
        $folderContent = $this->storage->listContents($folder)->toArray();
        if (empty($folderContent)) {
            $this->storage->deleteDirectory($folder);
        }
    }

    /**
     * @return $this
     *
     * @throws \League\Flysystem\FilesystemException
     */
    public function cleanupOutdatedZipFiles(): self
    {
        /** @var Carbon $maxAgeTime */
        $maxAgeTime = Carbon::now()->subHours(24);
        /** @var array|false $filenames */
        $filenames = $this->storage->listContents('/');
        if (is_array($filenames)) {
            foreach ($filenames as $filename) {
                if ('zip' === pathinfo($filename, PATHINFO_EXTENSION)) {
                    $path = '/' . $filename;
                    if (Carbon::createFromTimestamp($this->storage->lastModified($path))->lessThan($maxAgeTime)) {
                        $this->storage->delete($path);
                    }
                }
            }
        }

        return $this;
    }
}
