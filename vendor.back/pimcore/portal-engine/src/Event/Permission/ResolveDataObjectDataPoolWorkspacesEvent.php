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

namespace Pimcore\Bundle\PortalEngineBundle\Event\Permission;

use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataObjectConfig;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\WorkspaceConfig;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Can be used to dynamically modify the workspaces of an data object data pool.
 */
class ResolveDataObjectDataPoolWorkspacesEvent extends Event
{
    /**
     * @var WorkspaceConfig[]
     */
    private $workspaces;

    /**
     * @var DataObjectConfig
     */
    private $dataPoolConfig;

    /**
     * @param WorkspaceConfig[] $workspaces
     * @param DataObjectConfig $dataPoolConfig
     */
    public function __construct(array $workspaces, DataObjectConfig $dataPoolConfig)
    {
        $this->workspaces = $workspaces;
        $this->dataPoolConfig = $dataPoolConfig;
    }

    /**
     * @return WorkspaceConfig[]
     */
    public function getWorkspaces(): array
    {
        return $this->workspaces;
    }

    /**
     * @return DataObjectConfig
     */
    public function getDataPoolConfig(): DataObjectConfig
    {
        return $this->dataPoolConfig;
    }

    /**
     * @param WorkspaceConfig[] $workspaces
     */
    public function setWorkspaces(array $workspaces): void
    {
        $this->workspaces = $workspaces;
    }
}
