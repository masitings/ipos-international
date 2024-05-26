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

namespace Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Asset\Search;

use Pimcore\Bundle\PortalEngineBundle\Event\Permission\ResolveAssetDataPoolWorkspacesEvent;
use Pimcore\Bundle\PortalEngineBundle\Event\Permission\ResolveUserAssetWorkspacesEvent;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Asset\WorkspaceConfig;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\AssetConfig;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataPoolConfigInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\DataObject\PortalUserGroupInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\DataObject\PortalUserInterface;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Search\AbstractWorkspaceService;
use Pimcore\Model\DataObject\Data\ElementMetadata;
use Pimcore\Model\Element\ElementInterface;

class WorkspaceService extends AbstractWorkspaceService
{
    /**
     * @param bool $forceWorkspacesRefresh
     * @param AssetConfig|null $dataPoolConfig
     *
     * @return array
     */
    public function getDataPoolWorkspaces(
        bool $forceWorkspacesRefresh = false,
        ?DataPoolConfigInterface $dataPoolConfig = null
    ): array {
        /**
         * @var AssetConfig $dataPoolConfig
         */
        $dataPoolConfig = $dataPoolConfig ?: $this->dataPoolConfigService->getCurrentDataPoolConfig();

        $inUploadFolder = $dataPoolConfig->getUploadFolder()
            && $this->requestStack->getMasterRequest()
            && $this->requestStack->getMasterRequest()->get('uploadFolder') === 'true';

        if ($inUploadFolder) {
            return [
                (new WorkspaceConfig(
                    $dataPoolConfig->getUploadFolder()->getRealFullPath(),
                    true,
                    true,
                    true,
                    true,
                    true,
                    true,
                    false,
                    true
                )),
            ];
        }

        return parent::getDataPoolWorkspaces(
            $forceWorkspacesRefresh,
            $dataPoolConfig
        );
    }

    /**
     * @param WorkspaceConfig[] $workspaces
     * @param AssetConfig $dataPoolConfig
     *
     * @return WorkspaceConfig[]
     */
    protected function dispatchResolveDataPoolWorkspaceEvent(array $workspaces, DataPoolConfigInterface $dataPoolConfig): array
    {
        $event = new ResolveAssetDataPoolWorkspacesEvent($workspaces, $dataPoolConfig);
        $this->eventDispatcher->dispatch($event);

        return $event->getWorkspaces();
    }

    /**
     * @param \Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\WorkspaceConfig[] $workspaces
     * @param PortalUserInterface $user
     *
     * @return WorkspaceConfig[]
     */
    protected function dispatchResolveUserWorkspaceEvent(array $workspaces, PortalUserInterface $user): array
    {
        $event = new ResolveUserAssetWorkspacesEvent($workspaces, $user);
        $this->eventDispatcher->dispatch($event);

        return $event->getWorkspaces();
    }

    protected function createAllowAllWorkspaceConfig(): \Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\WorkspaceConfig
    {
        return new WorkspaceConfig(
            '/',
            true,
            true,
            true,
            true,
            true,
            true,
            true,
            false
        );
    }

    protected function createPublicShareWorkspaceConfig(): \Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\WorkspaceConfig
    {
        return new WorkspaceConfig(
            '/',
            true,
            true,
            false,
            false,
            false,
            false,
            false,
            false
        );
    }

    /**
     * @param ElementInterface $element
     * @param ElementMetadata $elementMetadata
     *
     * @return \Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\WorkspaceConfig
     */
    protected function createWorkspaceFromElementMetadata(ElementInterface $element, ElementMetadata $elementMetadata): \Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\WorkspaceConfig
    {
        return new WorkspaceConfig(
            $element->getRealFullPath(),
            (bool)$elementMetadata->getPermission_view(),
            (bool)$elementMetadata->getPermission_download(),
            (bool)$elementMetadata->getPermission_edit(),
            (bool)$elementMetadata->getPermission_update(),
            (bool)$elementMetadata->getPermission_create(),
            (bool)$elementMetadata->getPermission_delete(),
            (bool)$elementMetadata->getPermission_subfolder(),
            (bool)$elementMetadata->getPermission_view_owned_assets_only()
        );
    }

    /**
     * @inheritDoc
     */
    protected function getWorkspaceDefinitionFromUser(PortalUserInterface $user)
    {
        return (array) $user->getAssetWorkspaceDefinition();
    }

    /**
     * @inheritDoc
     */
    protected function getWorkspaceDefinitionFromUserGroup(PortalUserGroupInterface $userGroup)
    {
        return (array) $userGroup->getAssetWorkspaceDefinition();
    }
}
