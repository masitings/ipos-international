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

namespace Pimcore\Bundle\PortalEngineBundle\MessageHandler\BatchTask\RelocateAsset;

use Pimcore\Bundle\PortalEngineBundle\Message\BatchTask\RelocateAsset\ProcessChunkMessage;
use Pimcore\Bundle\PortalEngineBundle\Message\BatchTask\RelocateAsset\StartMessage;
use Pimcore\Bundle\PortalEngineBundle\MessageHandler\BatchTask\Traits\BatchTaskHandlerTrait;
use Pimcore\Bundle\PortalEngineBundle\Service\BatchTask\BatchTaskService;
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
     * @var int $batchSize
     */
    protected $batchSize;

    /**
     * StartHandler constructor.
     *
     * @param MessageBusInterface $messageBus
     * @param BatchTaskService $batchTaskService
     * @param int $batchSize
     */
    public function __construct(MessageBusInterface $messageBus, int $batchSize = 200)
    {
        $this->messageBus = $messageBus;
        $this->batchSize = $batchSize;
    }

    public function __invoke(StartMessage $message)
    {
        $this->logger->notice(
            sprintf(
                '[Task ID %s] Start asset relocate of %s item(s) [current memory usage: %s]',
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

        $this->batchTaskService->startBatchTask($batchTask);

        foreach ($this->batchTaskService->createParallelChunks($message->getItems(), $this->batchSize) as $chunkIndex => $chunk) {
            $chunkMessage = new ProcessChunkMessage($chunkIndex + 1, $chunk, $message->getTargetFolder(), $message->getDataPoolId(), $batchTask->getId());
            $this->messageBus->dispatch($chunkMessage);
        }
    }
}
