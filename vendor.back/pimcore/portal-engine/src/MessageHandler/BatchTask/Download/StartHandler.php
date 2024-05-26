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

use Pimcore\Bundle\PortalEngineBundle\Message\BatchTask\Download\ProcessChunkMessage;
use Pimcore\Bundle\PortalEngineBundle\Message\BatchTask\Download\StartMessage;
use Pimcore\Bundle\PortalEngineBundle\MessageHandler\BatchTask\Traits\BatchTaskHandlerTrait;
use Pimcore\Bundle\PortalEngineBundle\Service\Zip\ZipArchiveService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class StartHandler implements MessageHandlerInterface
{
    use BatchTaskHandlerTrait;

    /**
     * @var MessageBusInterface
     */
    protected $messageBus;

    /**
     * @var ZipArchiveService
     */
    protected $zipArchiveService;

    /**
     * @var int $batchSize
     */
    protected $batchSize;

    public function __construct(MessageBusInterface $messageBus, ZipArchiveService $zipArchiveService, int $batchSize)
    {
        $this->messageBus = $messageBus;
        $this->zipArchiveService = $zipArchiveService;
        $this->batchSize = $batchSize;
    }

    public function __invoke(StartMessage $message)
    {
        $this->logger->notice(
            sprintf(
                '[Task ID %s] Start download of %s item(s) [current memory usage: %s]',
                $message->getTaskId(),
                sizeof($message->getItems()),
                formatBytes(memory_get_usage())
            )
        );

        //if task was deleted/stopped or user is not available anymote nothing should be processed
        if (!$batchTask = $this->getBatchTask($message->getTaskId())) {
            return;
        }
        if (!$user = $this->getUserFromBatchTask($batchTask)) {
            return;
        }
        $this->setupPublicShare($batchTask);

        $this->batchTaskService->startBatchTask($batchTask);

        list($items, $remainingItems) = $this->batchTaskService->createSequentialChunk($message->getItems(), $this->batchSize);
        $chunkMessage = new ProcessChunkMessage(1, $items, $remainingItems, $this->countDataPools($message) > 1, $this->batchSize, $message->getDownloadContext(), $batchTask->getId());
        $this->messageBus->dispatch($chunkMessage);
    }

    protected function countDataPools(StartMessage $message): int
    {
        $dataPoolIds = [];

        foreach ($message->getItems() as $downloadItem) {
            if (!in_array($downloadItem->getDataPoolId(), $dataPoolIds)) {
                $dataPoolIds[] = $downloadItem->getDataPoolId();
            }
        }

        return sizeof($dataPoolIds);
    }
}
