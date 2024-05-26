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

namespace Pimcore\Bundle\PortalEngineBundle\Message\BatchTask\Interfaces;

use Pimcore\Bundle\PortalEngineBundle\Service\BatchTask\BatchTaskService;

interface SequentialBatchTaskMessageInterface extends BatchTaskMessageInterface
{
    public function hasRemainingItems(): bool;

    public function getBatchSize(): int;

    public function createRemainingMessage(BatchTaskService $batchTaskService): self;
}
