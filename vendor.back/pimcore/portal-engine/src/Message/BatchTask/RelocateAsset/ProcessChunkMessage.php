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

namespace Pimcore\Bundle\PortalEngineBundle\Message\BatchTask\RelocateAsset;

use Pimcore\Bundle\PortalEngineBundle\Message\BatchTask\Interfaces\TriggerFinishedMessageBatchTaskMessageInterface;

class ProcessChunkMessage implements TriggerFinishedMessageBatchTaskMessageInterface
{
    /**
     * @var int
     */
    private $chunkIndex;

    /**
     * @var string[]
     */
    private $items = [];

    /**
     * @var int
     */
    private $targetFolder;

    /**
     * @var int
     */
    private $dataPoolId;

    /**
     * @var int
     */
    private $taskId;

    /**
     * ProcessChunkMessage constructor.
     *
     * @param int $chunkIndex
     * @param array $items
     * @param int $targetFolder
     * @param int $dataPoolId
     * @param int $taskId
     */
    public function __construct(int $chunkIndex, array $items, int $targetFolder, int $dataPoolId, int $taskId)
    {
        $this->chunkIndex = $chunkIndex;
        $this->items = $items;
        $this->targetFolder = $targetFolder;
        $this->dataPoolId = $dataPoolId;
        $this->taskId = $taskId;
    }

    /**
     * @return string[]
     */
    public function getItems(): array
    {
        return $this->items;
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
    public function getDataPoolId(): int
    {
        return $this->dataPoolId;
    }

    /**
     * @return int
     */
    public function getChunkIndex(): int
    {
        return $this->chunkIndex;
    }

    public function createFinishedMessage(): object
    {
        return new FinishedMessage($this->getTaskId(), $this->getDataPoolId());
    }

    /**
     * @return int
     */
    public function getTargetFolder(): int
    {
        return $this->targetFolder;
    }
}
