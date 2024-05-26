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

namespace Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Search;

use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataPoolConfigInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\WorkspaceConfig;
use Pimcore\Bundle\PortalEngineBundle\Model\DataObject\PortalUserInterface;

interface WorkspaceServiceInterface
{
    /**
     * @param bool $forceWorkspacesRefresh
     * @param DataPoolConfigInterface|null $dataPoolConfig
     *
     * @return WorkspaceConfig[]
     */
    public function getDataPoolWorkspaces(bool $forceWorkspacesRefresh = false, ?DataPoolConfigInterface $dataPoolConfig = null): array;

    /**
     * @param WorkspaceConfig[] $workspaceConfigs
     * @param DataPoolConfigInterface|null $dataPoolConfig
     *
     * @return $this
     */
    public function setDataPoolWorkspaces(array $workspaceConfigs, ?DataPoolConfigInterface $dataPoolConfig = null);

    /**
     * @param PortalUserInterface|null $user
     * @param bool $forceWorkspacesRefresh
     * @param DataPoolConfigInterface|null $dataPoolConfig
     *
     * @return WorkspaceConfig[]
     */
    public function getUserWorkspaces(PortalUserInterface $user = null, bool $forceWorkspacesRefresh = false, ?DataPoolConfigInterface $dataPoolConfig = null): array;

    /**
     * @param WorkspaceConfig[] $workspaces
     *
     * @return string
     */
    public function getRootPathFromWorkspaces(array $workspaces): string;

    /**
     * @param WorkspaceConfig[] $workspaces
     *
     * @return void
     */
    public function sortWorkspaces(array &$workspaces): void;
}
