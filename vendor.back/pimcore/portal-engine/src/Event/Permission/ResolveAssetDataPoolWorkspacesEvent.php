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

use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Asset\WorkspaceConfig;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\AssetConfig;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Can be used to dynamically modify the workspaces of an asset data pool.
 */
class ResolveAssetDataPoolWorkspacesEvent extends Event
{
    /**
     * @var WorkspaceConfig[]
     */
    private $workspaces;

    /**
     * @var AssetConfig
     */
    private $dataPoolConfig;

    /**
     * @param WorkspaceConfig[] $workspaces
     * @param AssetConfig $dataPoolConfig
     */
    public function __construct(array $workspaces, AssetConfig $dataPoolConfig)
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
     * @return AssetConfig
     */
    public function getDataPoolConfig(): AssetConfig
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
