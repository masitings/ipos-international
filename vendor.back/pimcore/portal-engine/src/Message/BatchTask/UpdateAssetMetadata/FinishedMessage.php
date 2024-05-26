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

class FinishedMessage
{
    /**
     * @var int
     */
    protected $taskId;
    /**
     * @var int
     */
    protected $dataPoolId;

    /**
     * @param int $taskId
     * @param int $dataPoolId
     */
    public function __construct(int $taskId, int $dataPoolId)
    {
        $this->taskId = $taskId;
        $this->dataPoolId = $dataPoolId;
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
}
