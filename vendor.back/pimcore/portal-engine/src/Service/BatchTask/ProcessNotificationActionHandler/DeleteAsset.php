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

namespace Pimcore\Bundle\PortalEngineBundle\Service\BatchTask\ProcessNotificationActionHandler;

use Pimcore\Bundle\PortalEngineBundle\Entity\BatchTask;
use Pimcore\Bundle\PortalEngineBundle\Enum\BatchTask\Payload\AssetDelete;
use Pimcore\Bundle\PortalEngineBundle\Enum\BatchTask\Type;
use Pimcore\Bundle\PortalEngineBundle\Service\BatchTask\BatchTaskService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class DeleteAsset implements ProcessNotificationActionInterface
{
    /**
     * @var BatchTaskService
     */
    protected $batchTaskService;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    public function __construct(BatchTaskService $batchTaskService, EventDispatcherInterface $eventDispatcher)
    {
        $this->batchTaskService = $batchTaskService;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function supports(BatchTask $batchTask): bool
    {
        return $batchTask->getType() === Type::DELETE_ASSET;
    }

    public function handle(BatchTask $batchTask): Response
    {
        $targetPage = $this->getTargetPage($batchTask);

        return new RedirectResponse($targetPage);
    }

    public function terminate(BatchTask $batchTask)
    {
        $this->batchTaskService->deleteBatchTask($batchTask);
    }

    protected function getTargetPage(BatchTask $batchTask): string
    {
        return $batchTask->getPayload()[AssetDelete::TARGET_PAGE] ?? '/';
    }
}
