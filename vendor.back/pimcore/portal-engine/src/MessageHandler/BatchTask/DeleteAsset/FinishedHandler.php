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

namespace Pimcore\Bundle\PortalEngineBundle\MessageHandler\BatchTask\DeleteAsset;

use Pimcore\Bundle\PortalEngineBundle\Message\BatchTask\DeleteAsset\FinishedMessage;
use Pimcore\Bundle\PortalEngineBundle\MessageHandler\BatchTask\Traits\BatchTaskHandlerTrait;
use Pimcore\Bundle\PortalEngineBundle\MessageHandler\BatchTask\Traits\RefreshIndexTrait;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class FinishedHandler implements MessageHandlerInterface
{
    use BatchTaskHandlerTrait;
    use RefreshIndexTrait;

    public function __invoke(FinishedMessage $message)
    {
        $this->logger->notice(
            sprintf(
                '[Task ID %s] Finish delete asset [current memory usage: %s]',
                $message->getTaskId(),
                formatBytes(memory_get_usage())
            )
        );

        //if task was deleted/stopped nothing should be processed anymore
        if (!$batchTask = $this->getBatchTask($message->getTaskId())) {
            return;
        }

        $this->refreshIndexByDataPoolId($message->getDataPoolId());

        $batchTask->setDisableDeleteConfirmation(true);
        $this->batchTaskService->saveBatchTask($batchTask);
    }
}
