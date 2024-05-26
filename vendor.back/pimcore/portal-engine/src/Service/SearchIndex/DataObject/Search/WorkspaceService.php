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

namespace Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\DataObject\Search;

use Pimcore\Bundle\PortalEngineBundle\Event\Permission\ResolveDataObjectDataPoolWorkspacesEvent;
use Pimcore\Bundle\PortalEngineBundle\Event\Permission\ResolveUserDataObjectWorkspacesEvent;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataObjectConfig;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataPoolConfigInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\WorkspaceConfig;
use Pimcore\Bundle\PortalEngineBundle\Model\DataObject\PortalUserGroupInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\DataObject\PortalUserInterface;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Search\AbstractWorkspaceService;

class WorkspaceService extends AbstractWorkspaceService
{
    /**
     * @param WorkspaceConfig[] $workspaces
     * @param DataObjectConfig $dataPoolConfig
     *
     * @return WorkspaceConfig[]
     */
    protected function dispatchResolveDataPoolWorkspaceEvent(array $workspaces, DataPoolConfigInterface $dataPoolConfig): array
    {
        $event = new ResolveDataObjectDataPoolWorkspacesEvent($workspaces, $dataPoolConfig);
        $this->eventDispatcher->dispatch($event);

        return $event->getWorkspaces();
    }

    /**
     * @param WorkspaceConfig[] $workspaces
     * @param PortalUserInterface $user
     *
     * @return WorkspaceConfig[]
     */
    protected function dispatchResolveUserWorkspaceEvent(array $workspaces, PortalUserInterface $user): array
    {
        $event = new ResolveUserDataObjectWorkspacesEvent($workspaces, $user);
        $this->eventDispatcher->dispatch($event);

        return $event->getWorkspaces();
    }

    /**
     * @inheritDoc
     */
    protected function getWorkspaceDefinitionFromUser(PortalUserInterface $user)
    {
        return (array) $user->getDataObjectWorkspaceDefinition();
    }

    /**
     * @inheritDoc
     */
    protected function getWorkspaceDefinitionFromUserGroup(PortalUserGroupInterface $userGroup)
    {
        return (array) $userGroup->getDataObjectWorkspaceDefinition();
    }
}
