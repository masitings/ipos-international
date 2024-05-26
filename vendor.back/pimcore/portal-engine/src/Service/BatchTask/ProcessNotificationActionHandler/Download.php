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

namespace Pimcore\Bundle\PortalEngineBundle\Service\BatchTask\ProcessNotificationActionHandler;

use Pimcore\Bundle\PortalEngineBundle\Entity\BatchTask;
use Pimcore\Bundle\PortalEngineBundle\Enum\BatchTask\Type;
use Pimcore\Bundle\PortalEngineBundle\Event\Download\DownloadZipFilenameEvent;
use Pimcore\Bundle\PortalEngineBundle\Service\BatchTask\BatchTaskService;
use Pimcore\Bundle\PortalEngineBundle\Service\Download\StorageService;
use Pimcore\Bundle\PortalEngineBundle\Service\Zip\ZipArchiveService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Download implements ProcessNotificationActionInterface
{
    /**
     * @var ZipArchiveService
     */
    protected $zipArchiveService;

    /**
     * @var BatchTaskService
     */
    protected $batchTaskService;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var StorageService
     */
    protected $storageService;

    public function __construct(ZipArchiveService $zipArchiveService, BatchTaskService $batchTaskService, StorageService $storageService, EventDispatcherInterface $eventDispatcher)
    {
        $this->zipArchiveService = $zipArchiveService;
        $this->batchTaskService = $batchTaskService;
        $this->eventDispatcher = $eventDispatcher;
        $this->storageService = $storageService;
    }

    public function supports(BatchTask $batchTask): bool
    {
        return $batchTask->getType() === Type::DOWNLOAD_GENERATION;
    }

    public function handle(BatchTask $batchTask): Response
    {
        if ($singleFile = $this->getSingleFile($batchTask)) {
            $stream = $this->storageService->openStreamForSingleFileFromStorage($singleFile);

            $filename = basename($singleFile);
            $headers['Content-Disposition'] = sprintf('attachment; filename="%s"', $filename);
            $headers['Content-Length'] = fstat($stream)['size'];

            $storageService = $this->storageService;

            return new StreamedResponse(function () use ($stream, $storageService, $singleFile) {
                fpassthru($stream);
                $storageService->cleanupSingleFileInStorage($singleFile);
            }, 200, $headers);
        }

        $zipFile = $this->getZipFileStream($batchTask);
        if ($zipFile !== null) {
            $filename = sprintf('download_%s.zip', date('Y-m-d_H-i-s'));
            $event = new DownloadZipFilenameEvent($filename, $batchTask);
            $this->eventDispatcher->dispatch($event);
            $filename = $event->getFilename();

            $headers['Content-Type'] = 'application/zip';
            $headers['Content-Disposition'] = sprintf('attachment; filename="%s"', $filename);
            $headers['Content-Length'] = fstat($zipFile)['size'];

            $storageService = $this->storageService;
            $zipId = $batchTask->getPayload()[\Pimcore\Bundle\PortalEngineBundle\Enum\BatchTask\Payload\Download::ZIP_ID];

            return new StreamedResponse(function () use ($zipFile, $storageService, $zipId) {
                fpassthru($zipFile);
                $storageService->cleanupZipFileInStorage($zipId);
            }, 200, $headers);
        } else {
            return new Response('download resulted in zero files');
        }
    }

    public function terminate(BatchTask $batchTask)
    {
        $this->storageService->cleanupZipFileInStorage($this->getZipId($batchTask));
        $file = $this->zipArchiveService->getZipFilesytemPathByZipId($this->getZipId($batchTask));
        if (file_exists($file)) {
            unlink($file);
        }

        $this->batchTaskService->deleteBatchTask($batchTask);
    }

    protected function getSingleFile(BatchTask $batchTask): ?string
    {
        return $batchTask->getPayload()[\Pimcore\Bundle\PortalEngineBundle\Enum\BatchTask\Payload\Download::SINGLE_FILE] ?? null;
    }

    protected function getZipId(BatchTask $batchTask): ?string
    {
        return $batchTask->getPayload()[\Pimcore\Bundle\PortalEngineBundle\Enum\BatchTask\Payload\Download::ZIP_ID];
    }

    protected function getZipFileStream(BatchTask $batchTask)
    {
        $zipId = $this->getZipId($batchTask);

        return $this->storageService->openStreamForZipFileFromStorage($zipId);
    }
}
