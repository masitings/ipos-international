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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Asset;

use Pimcore\Bundle\PortalEngineBundle\Enum\BatchTask\Payload\AssetDelete;
use Pimcore\Bundle\PortalEngineBundle\Enum\BatchTask\Type;
use Pimcore\Bundle\PortalEngineBundle\Enum\Permission;
use Pimcore\Bundle\PortalEngineBundle\MessageHandler\BatchTask\Traits\RefreshIndexTrait;
use Pimcore\Bundle\PortalEngineBundle\Service\BatchTask\BatchTaskService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\PermissionService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\SecurityService;
use Pimcore\Model\Asset;
use Symfony\Component\Messenger\MessageBusInterface;

class AssetDeleteService
{
    use RefreshIndexTrait;

    /**
     * @var int
     */
    private $directDeletionThreshold;

    /**
     * @var BatchTaskService
     */
    private $batchTaskService;

    /**
     * @var MessageBusInterface
     */
    private $messageBus;

    /**
     * @var SecurityService
     */
    private $security;

    /**
     * @var PermissionService
     */
    private $permissionService;

    /**
     * AssetDeleteService constructor.
     *
     * @param BatchTaskService $batchTaskService
     * @param MessageBusInterface $messageBus
     * @param SecurityService $security
     * @param int $directDeletionThreshold
     */
    public function __construct(
        BatchTaskService $batchTaskService,
        MessageBusInterface $messageBus,
        SecurityService $security,
        PermissionService $permissionService,
        int $directDeletionThreshold = 10
    ) {
        $this->directDeletionThreshold = $directDeletionThreshold;
        $this->batchTaskService = $batchTaskService;
        $this->messageBus = $messageBus;
        $this->security = $security;
        $this->permissionService = $permissionService;
    }

    /**
     * Triggers the deletion of the given asset IDs.
     * If the direct deletion threshold is reached the actual work will be triggered as asynchronous worker message.
     * Returns true if the work is delegated to the asynchronous workers.
     *
     * @param array $ids
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function triggerAssetDeletion(array $ids, int $dataPoolId, string $targetPage): bool
    {
        if (sizeof($ids) <= $this->directDeletionThreshold) {
            foreach ($ids as $id) {
                if ($asset = Asset::getById($id)) {
                    if ($this->permissionService->isPermissionAllowed(Permission::DELETE, $this->security->getPortalUser(), $dataPoolId, $asset->getRealFullPath())) {
                        $asset->delete();
                    }
                }

                $this->refreshIndexByDataPoolId($dataPoolId);
            }

            return false;
        }

        $task = $this->batchTaskService->prepareBatchTask(
            $this->security->getPortalUser()->getPortalUserId(),
            Type::DELETE_ASSET,
            sizeof($ids),
            [
                AssetDelete::TARGET_PAGE => $targetPage,
            ]
        );
        $message = new \Pimcore\Bundle\PortalEngineBundle\Message\BatchTask\DeleteAsset\StartMessage($ids, $dataPoolId, $task->getId());
        $this->messageBus->dispatch($message);

        return true;
    }

    /**
     * @param int $directDeletionThreshold
     */
    public function setDirectDeletionThreshold(int $directDeletionThreshold): void
    {
        $this->directDeletionThreshold = $directDeletionThreshold;
    }
}
