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

namespace Pimcore\Bundle\PortalEngineBundle\Message\BatchTask\UpdateAssetMetadata;

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
     * @var array
     */
    private $metadata = [];

    /**
     * @var array
     */
    private $tags = [];

    /**
     * @var string
     */
    private $tagsApplyMode;
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
     * @param array $metadata
     * @param array $tags
     * @param string $tagsApplyMode
     * @param int $dataPoolId
     * @param int $taskId
     */
    public function __construct(int $chunkIndex, array $items, array $metadata, array $tags, string $tagsApplyMode, int $dataPoolId, int $taskId)
    {
        $this->chunkIndex = $chunkIndex;
        $this->items = $items;
        $this->metadata = $metadata;
        $this->tags = $tags;
        $this->tagsApplyMode = $tagsApplyMode;
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
     * @return array
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * @return array
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @return string
     */
    public function getTagsApplyMode(): string
    {
        return $this->tagsApplyMode;
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
}
