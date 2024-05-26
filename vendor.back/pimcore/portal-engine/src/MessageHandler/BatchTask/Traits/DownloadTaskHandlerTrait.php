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

namespace Pimcore\Bundle\PortalEngineBundle\MessageHandler\BatchTask\Traits;

use Pimcore\Bundle\PortalEngineBundle\Entity\BatchTask;
use Pimcore\Bundle\PortalEngineBundle\Enum\BatchTask\Payload\Download;
use Pimcore\Bundle\PortalEngineBundle\Service\Download\StorageService;
use Pimcore\Bundle\PortalEngineBundle\Service\Zip\ZipArchiveService;

trait DownloadTaskHandlerTrait
{
    /**
     * @var ZipArchiveService
     */
    protected $zipArchiveService;

    /**
     * @var StorageService
     */
    protected $storageService;

    protected function getSingleFile(BatchTask $batchTask): ?string
    {
        return $batchTask->getPayload()[Download::SINGLE_FILE] ?? null;
    }

    protected function localZipExists(BatchTask $batchTask): bool
    {
        return file_exists($this->zipArchiveService->getZipFilesytemPathByZipId($batchTask->getPayload()[Download::ZIP_ID]));
    }

    /**
     * @param ZipArchiveService $zipArchiveService
     * @required
     */
    public function setZipArchiveService(ZipArchiveService $zipArchiveService)
    {
        $this->zipArchiveService = $zipArchiveService;
    }

    /**
     * @param StorageService $storageService
     * @required
     */
    public function setStorageService(StorageService $storageService): void
    {
        $this->storageService = $storageService;
    }
}
