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

namespace Pimcore\Bundle\PortalEngineBundle\MessageHandler\BatchTask\Download;

use Pimcore\Bundle\PortalEngineBundle\Enum\BatchTask\Payload\Download;
use Pimcore\Bundle\PortalEngineBundle\Message\BatchTask\Download\FinishedMessage;
use Pimcore\Bundle\PortalEngineBundle\MessageHandler\BatchTask\Traits\BatchTaskHandlerTrait;
use Pimcore\Bundle\PortalEngineBundle\MessageHandler\BatchTask\Traits\DownloadTaskHandlerTrait;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DownloadFormatHandler;
use Pimcore\Bundle\PortalEngineBundle\Service\Download\DownloadZipGenerationService;
use Pimcore\Bundle\PortalEngineBundle\Service\Download\StorageService;
use Pimcore\Bundle\PortalEngineBundle\Service\Zip\ContainedInZipService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class FinishedHandler implements MessageHandlerInterface
{
    use BatchTaskHandlerTrait;
    use DownloadTaskHandlerTrait;

    /**
     * @var ContainedInZipService
     */
    protected $containedInZipService;

    /**
     * @var DownloadZipGenerationService
     */
    protected $downloadZipGenerationService;

    /**
     * @var StorageService
     */
    protected $storageService;

    /**
     * @var DownloadFormatHandler
     */
    protected $downloadFormatHandler;

    public function __construct(ContainedInZipService $containedInZipService, DownloadZipGenerationService $downloadZipGenerationService, StorageService $storageService, DownloadFormatHandler $downloadFormatHandler)
    {
        $this->containedInZipService = $containedInZipService;
        $this->downloadZipGenerationService = $downloadZipGenerationService;
        $this->downloadFormatHandler = $downloadFormatHandler;
        $this->storageService = $storageService;
    }

    public function __invoke(FinishedMessage $message)
    {
        $this->logger->notice(
            sprintf(
                '[Task ID %s] Finish download [current memory usage: %s]',
                $message->getTaskId(),
                formatBytes(memory_get_usage())
            )
        );

        //if task was deleted/stopped nothing should be processed anymore
        if (!$batchTask = $this->getBatchTask($message->getTaskId())) {
            return;
        }
        $this->setupPublicShare($batchTask);

        $payload = $batchTask->getPayload();
        $zipId = $payload[Download::ZIP_ID];

        if (isset($payload[Download::BUNDLE_STRUCTURED_DATA])) {
            $this->storageService->initLocalZipFileFromStorage($zipId);

            $bundleStructuredData = unserialize($payload[Download::BUNDLE_STRUCTURED_DATA]);
            $this->downloadZipGenerationService->bundleStructuredDataDownloadFormatsIntoZip($bundleStructuredData, $zipId);

            $this->storageService->commitLocalZipFileToStorage($zipId);
        }

        $this->containedInZipService->clearStore($zipId);

        if (!$this->getSingleFile($batchTask) && !$this->storageService->zipFileInStorageExists($zipId)) {
            $batchTask->setDisableNotificationAction(true);
            $batchTask->setDisableDeleteConfirmation(true);
            $this->batchTaskService->saveBatchTask($batchTask);
        }
    }
}
