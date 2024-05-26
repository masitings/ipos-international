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

use Pimcore\Bundle\PortalEngineBundle\Message\BatchTask\Interfaces\SplittedBatchTaskMessageInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadItemInterface;

class StartMessage implements SplittedBatchTaskMessageInterface
{
    /**
     * @var DownloadItemInterface[]
     */
    private $items = [];

    /**
     * @var string
     */
    private $downloadContext;

    /**
     * @var int
     */
    private $taskId;

    /**
     * StartDownloadMessage constructor.
     *
     * @param DownloadItemInterface[] $items
     * @param string $downloadContext
     * @param int $taskId
     */
    public function __construct(array $items, string $downloadContext, int $taskId)
    {
        $this->items = $items;
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
     * @return int
     */
    public function getTaskId(): int
    {
        return $this->taskId;
    }

    /**
     * @return string
     */
    public function getDownloadContext(): string
    {
        return $this->downloadContext;
    }
}
