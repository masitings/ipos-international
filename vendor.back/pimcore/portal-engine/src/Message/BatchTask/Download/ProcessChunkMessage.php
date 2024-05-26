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

namespace Pimcore\Bundle\PortalEngineBundle\Message\BatchTask\Download;

use Pimcore\Bundle\PortalEngineBundle\Message\BatchTask\Interfaces\SequentialBatchTaskMessageInterface;
use Pimcore\Bundle\PortalEngineBundle\Message\BatchTask\Interfaces\TriggerFinishedMessageBatchTaskMessageInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadItemInterface;
use Pimcore\Bundle\PortalEngineBundle\Service\BatchTask\BatchTaskService;

class ProcessChunkMessage implements SequentialBatchTaskMessageInterface, TriggerFinishedMessageBatchTaskMessageInterface
{
    /**
     * @var int
     */
    private $chunkIndex;

    /**
     * @var DownloadItemInterface[]
     */
    private $items = [];

    /**
     * @var DownloadItemInterface[]
     */
    private $remainingItems = [];

    /**
     * @var bool
     */
    private $includeDataPoolFolder;

    /**
     * @var int
     */
    private $batchSize;

    /**
     * @var string
     */
    private $downloadContext;

    /**
     * @var int
     */
    private $taskId;

    /**
     * @param int $chunkIndex
     * @param array $items
     * @param array $remainingItems
     * @param bool $includeDataPoolFolder
     * @param int $batchSize
     * @param string $downloadContext
     * @param int $taskId
     */
    public function __construct(
        int $chunkIndex,
        array $items,
        array $remainingItems,
        bool $includeDataPoolFolder,
        int $batchSize,
        string $downloadContext,
        int $taskId
    ) {
        $this->chunkIndex = $chunkIndex;
        $this->items = $items;
        $this->remainingItems = $remainingItems;
        $this->includeDataPoolFolder = $includeDataPoolFolder;
        $this->batchSize = $batchSize;
        $this->downloadContext = $downloadContext;
        $this->taskId = $taskId;
    }

    /**
     * @return DownloadItemInterface[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return bool
     */
    public function hasRemainingItems(): bool
    {
        return !empty($this->remainingItems);
    }

    public function createRemainingMessage(BatchTaskService $batchTaskService): SequentialBatchTaskMessageInterface
    {
        list($items, $remainingItems) = $batchTaskService->createSequentialChunk(
            $this->remainingItems,
            $this->getBatchSize()
        );

        return new static(
            $this->getChunkIndex() + 1,
            $items,
            $remainingItems,
            $this->getIncludeDataPoolFolder(),
            $this->getBatchSize(),
            $this->getDownloadContext(),
            $this->getTaskId()
        );
    }

    public function createFinishedMessage(): object
    {
        return new FinishedMessage($this->getTaskId());
    }

    /**
     * @return int
     */
    public function getTaskId(): int
    {
        return $this->taskId;
    }

    /**
     * @return int
     */
    public function getChunkIndex(): int
    {
        return $this->chunkIndex;
    }

    public function getBatchSize(): int
    {
        return $this->batchSize;
    }

    /**
     * @return bool
     */
    public function getIncludeDataPoolFolder(): bool
    {
        return $this->includeDataPoolFolder;
    }

    /**
     * @return string
     */
    public function getDownloadContext(): string
    {
        return $this->downloadContext;
    }
}
