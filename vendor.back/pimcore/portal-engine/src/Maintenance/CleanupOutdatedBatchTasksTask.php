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

namespace Pimcore\Bundle\PortalEngineBundle\Maintenance;

use Pimcore\Bundle\PortalEngineBundle\Service\BatchTask\BatchTaskService;
use Pimcore\Maintenance\TaskInterface;

class CleanupOutdatedBatchTasksTask implements TaskInterface
{
    /**
     * @var BatchTaskService
     */
    protected $batchTaskService;

    /**
     * CleanupOutdatedBatchTasksTask constructor.
     *
     * @param BatchTaskService $batchTaskService
     */
    public function __construct(BatchTaskService $batchTaskService)
    {
        $this->batchTaskService = $batchTaskService;
    }

    /**
     * @throws \Exception
     */
    public function execute()
    {
        $this->batchTaskService->cleanupOutdatedBatchTasks();
    }
}
