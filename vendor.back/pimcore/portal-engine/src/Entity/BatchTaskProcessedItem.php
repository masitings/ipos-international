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

namespace Pimcore\Bundle\PortalEngineBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="portal_engine_batch_task_processed_items")
 */
class BatchTaskProcessedItem
{
    const TABLE = 'portal_engine_batch_task_processed_items';

    /**
     * @var int|null
     *
     * @ORM\Id()
     * @ORM\Column(type="integer")
     */
    private $taskId;

    /**
     * @var int|null
     *
     * @ORM\Id()
     * @ORM\Column(type="integer")
     */
    private $itemIndex;

    /**
     * BatchTaskProcessedItem constructor.
     *
     * @param int|null $taskId
     * @param int|null $itemIndex
     */
    public function __construct(?int $taskId, ?int $itemIndex)
    {
        $this->taskId = $taskId;
        $this->itemIndex = $itemIndex;
    }

    /**
     * @return int|null
     */
    public function getTaskId(): ?int
    {
        return $this->taskId;
    }

    /**
     * @param int|null $taskId
     *
     * @return BatchTaskProcessedItem
     */
    public function setTaskId(?int $taskId): self
    {
        $this->taskId = $taskId;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getItemIndex(): ?int
    {
        return $this->itemIndex;
    }

    /**
     * @param int|null $itemIndex
     *
     * @return BatchTaskProcessedItem
     */
    public function setItemIndex(?int $itemIndex): self
    {
        $this->itemIndex = $itemIndex;

        return $this;
    }
}
