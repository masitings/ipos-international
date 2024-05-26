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

use Pimcore\Bundle\PortalEngineBundle\Enum\BatchTask\Payload\AssetRelocate;
use Pimcore\Bundle\PortalEngineBundle\Enum\Permission;
use Pimcore\Bundle\PortalEngineBundle\MessageHandler\BatchTask\Traits\RefreshIndexTrait;
use Pimcore\Bundle\PortalEngineBundle\Service\BatchTask\BatchTaskService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\PermissionService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\SecurityService;
use Pimcore\Model\Asset;
use Symfony\Component\Messenger\MessageBusInterface;

class AssetRelocateService
{
    use RefreshIndexTrait;

    /**
     * @var int
     */
    private $directRelocateThreshold;

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
        int $directRelocateThreshold = 10
    ) {
        $this->directRelocateThreshold = $directRelocateThreshold;
        $this->batchTaskService = $batchTaskService;
        $this->messageBus = $messageBus;
        $this->security = $security;
        $this->permissionService = $permissionService;
    }

    /**
     * Triggers the relocation of the given asset IDs
     * If the direct relocation threshold is reached the actual work will be triggered as asynchronous worker message.
     * Returns true if the work is delegated to the asynchronous workers.
     *
     * @param array $ids
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function triggerAssetRelocate(array $ids, int $dataPoolId, string $targetPage, Asset\Folder $targetFolder): bool
    {
        if (sizeof($ids) <= $this->directRelocateThreshold) {
            foreach ($ids as $id) {
                if ($asset = Asset::getById($id)) {
                    $this->relocateAsset($asset, $targetFolder, $dataPoolId);
                }

                $this->refreshIndexByDataPoolId($dataPoolId);
            }

            return false;
        }

        $task = $this->batchTaskService->prepareBatchTask(
            $this->security->getPortalUser()->getId(),
            \Pimcore\Bundle\PortalEngineBundle\Enum\BatchTask\Type::RELOCATE_ASSET,
            sizeof($ids),
            [
                AssetRelocate::TARGET_PAGE => $targetPage,
            ]
        );
        $message = new \Pimcore\Bundle\PortalEngineBundle\Message\BatchTask\RelocateAsset\StartMessage($ids, $targetFolder->getId(), $dataPoolId, $task->getId());
        $this->messageBus->dispatch($message);

        return true;
    }

    /**
     * @param Asset $asset
     * @param Asset\Folder $targetFolder
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function relocateAsset(Asset $asset, Asset\Folder $targetFolder, int $dataPoolId): bool
    {
        if (
            $this->permissionService->isPermissionAllowed(Permission::UPDATE, $this->security->getPortalUser(), $dataPoolId, $asset->getRealFullPath(), false, false, true, true)
            && $this->permissionService->isPermissionAllowed(Permission::CREATE, $this->security->getPortalUser(), $dataPoolId, $targetFolder->getRealFullPath())
        ) {
            $asset
                ->setParent($targetFolder)
                ->setKey(Asset\Service::getUniqueKey($asset))
                ->save();

            return true;
        }

        return false;
    }

    /**
     * @param int $directRelocateThreshold
     */
    public function setDirectRelocateThreshold(int $directRelocateThreshold): void
    {
        $this->directRelocateThreshold = $directRelocateThreshold;
    }
}
