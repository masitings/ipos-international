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

use Pimcore\Bundle\PortalEngineBundle\Message\BatchTask\Interfaces\BatchTaskMessageInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadItemInterface;

class StartMessage implements BatchTaskMessageInterface
{
    /**
     * @var DownloadItemInterface[]
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
     * StartDownloadMessage constructor.
     *
     * @param int[] $items
     * @param array $metadata
     * @param int $taskId
     */
    public function __construct(array $items, array $metadata, array $tags, string $tagsApplyMode, int $dataPoolId, int $taskId)
    {
        $this->items = $items;
        $this->metadata = $metadata;
        $this->tags = $tags;
        $this->tagsApplyMode = $tagsApplyMode;
        $this->dataPoolId = $dataPoolId;
        $this->taskId = $taskId;
    }

    /**
     * @return int[]
     */
    public function getItems(): array
    {
        return $this->items;
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
    public function getDataPoolId(): int
    {
        return $this->dataPoolId;
    }

    /**
     * @return int
     */
    public function getTaskId(): int
    {
        return $this->taskId;
    }
}
