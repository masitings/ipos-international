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

namespace Pimcore\Bundle\PortalEngineBundle\MessageHandler\BatchTask\UpdateAssetMetadata;

use Pimcore\Bundle\PortalEngineBundle\Message\BatchTask\UpdateAssetMetadata\ProcessChunkMessage;
use Pimcore\Bundle\PortalEngineBundle\MessageHandler\BatchTask\Traits\BatchTaskHandlerTrait;
use Pimcore\Bundle\PortalEngineBundle\Service\Asset\MetadataService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\Element\TagsService;
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
     * @var MetadataService
     */
    protected $metadataService;

    /**
     * @var SecurityService
     */
    protected $securityService;

    /**
     * @var TagsService
     */
    protected $tagsService;

    /**
     * @param MessageBusInterface $messageBus
     * @param DataPoolConfigService $dataPoolConfigService
     * @param MetadataService $metadataService
     * @param SecurityService $securityService
     * @param TagsService $tagsService
     */
    public function __construct(
        MessageBusInterface $messageBus,
        DataPoolConfigService $dataPoolConfigService,
        MetadataService $metadataService,
        SecurityService $securityService,
        TagsService $tagsService
    ) {
        $this->messageBus = $messageBus;
        $this->dataPoolConfigService = $dataPoolConfigService;
        $this->metadataService = $metadataService;
        $this->securityService = $securityService;
        $this->tagsService = $tagsService;
    }

    /**
     * @param ProcessChunkMessage $message
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke(ProcessChunkMessage $message)
    {
        $this->logger->notice(
            sprintf(
                '[Task ID %s] Process asset metadata update chunk %s with %s item(s) [current memory usage: %s]',
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

                $this->metadataService->setMetadata($asset, $message->getMetadata(), true);
                $asset->save();

                $this->tagsService->assignTagsOnElement($asset, $message->getTags(), $message->getTagsApplyMode());
            }

            $this->batchTaskService->markItemIndexAsProcessed($batchTask, $itemIndex);
        }
    }
}
