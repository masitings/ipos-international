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

namespace Pimcore\Bundle\PortalEngineBundle\MessageHandler\BatchTask\RelocateAsset;

use Pimcore\Bundle\PortalEngineBundle\Message\BatchTask\RelocateAsset\ProcessChunkMessage;
use Pimcore\Bundle\PortalEngineBundle\MessageHandler\BatchTask\Traits\BatchTaskHandlerTrait;
use Pimcore\Bundle\PortalEngineBundle\Service\Asset\AssetRelocateService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\SecurityService;
use Pimcore\Model\Asset;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class ProcessChunkHandler implements MessageHandlerInterface
{
    use BatchTaskHandlerTrait;

    /**
     * @var MessageBusInterface
     */
    protected $messageBus;

    /**
     * @var DataPoolConfigService
     */
    protected $dataPoolConfigService;

    /**
     * @var AssetRelocateService
     */
    protected $assetRelocateService;

    /**
     * @var SecurityService
     */
    protected $securityService;

    /**
     * StartDownloadHandler constructor.
     *
     * @param MessageBusInterface $messageBus
     */
    public function __construct(MessageBusInterface $messageBus, DataPoolConfigService $dataPoolConfigService, AssetRelocateService $assetRelocateService, SecurityService $securityService)
    {
        $this->messageBus = $messageBus;
        $this->dataPoolConfigService = $dataPoolConfigService;
        $this->assetRelocateService = $assetRelocateService;
        $this->securityService = $securityService;
    }

    /**
     * @param ProcessChunkMessage $message
     *
     * @throws \Exception
     */
    public function __invoke(ProcessChunkMessage $message)
    {
        $this->logger->notice(
            sprintf(
                '[Task ID %s] Process asset relocate chunk %s with %s item(s) [current memory usage: %s]',
                $message->getTaskId(),
                $message->getChunkIndex(),
                sizeof($message->getItems()),
                formatBytes(memory_get_usage())
            )
        );

        //if task was deleted/stopped or user is not available anymote nothing should be processed
        if (!$batchTask = $this->getBatchTask($message->getTaskId())) {
            return;
        }
        if (!$user = $this->getUserFromBatchTask($batchTask)) {
            return;
        }
        $this->securityService->setPortalUser($user);

        $targetFolder = Asset\Folder::getById($message->getTargetFolder());

        if (empty($targetFolder)) {
            $this->logger->info(
                sprintf(
                    '[Task ID %s] Skip asset relocate (target folder %s not found anymore)',
                    $message->getTaskId(),
                    $message->getTargetFolder()
                )
            );

            return;
        }

        $this->dataPoolConfigService->setCurrentDataPoolConfigById($message->getDataPoolId());

        foreach ($message->getItems() as $itemIndex => $item) {
            if ($this->batchTaskService->isItemIndexProcessed($batchTask, $itemIndex)) {
                continue;
            }

            $asset = Asset::getById($item);

            if (empty($asset)) {
                $this->logger->info(
                    sprintf(
                        '[Task ID %s] Skip item %s (not found anymore)',
                        $message->getTaskId(),
                        $item
                    )
                );
            } else {
                $this->logger->info(
                    sprintf(
                        '[Task ID %s] Process item %s',
                        $message->getTaskId(),
                        $asset->getId()
                    )
                );

                $this->assetRelocateService->relocateAsset($asset, $targetFolder, $message->getDataPoolId());
            }

            $this->batchTaskService->markItemIndexAsProcessed($batchTask, $itemIndex);
        }
    }
}
