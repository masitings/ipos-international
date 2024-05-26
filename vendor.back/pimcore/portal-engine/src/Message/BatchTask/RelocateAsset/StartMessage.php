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

use Pimcore\Bundle\PortalEngineBundle\Message\BatchTask\Interfaces\BatchTaskMessageInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadItemInterface;

class StartMessage implements BatchTaskMessageInterface
{
    /**
     * @var DownloadItemInterface[]
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
     * StartMessage constructor.
     *
     * @param array $items
     * @param int $targetFolder
     * @param int $dataPoolId
     * @param int $taskId
     */
    public function __construct(array $items, int $targetFolder, int $dataPoolId, int $taskId)
    {
        $this->items = $items;
        $this->targetFolder = $targetFolder;
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

    /**
     * @return int
     */
    public function getTargetFolder(): int
    {
        return $this->targetFolder;
    }
}
