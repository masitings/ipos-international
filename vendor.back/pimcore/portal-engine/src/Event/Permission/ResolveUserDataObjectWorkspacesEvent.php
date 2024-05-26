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

use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\WorkspaceConfig;
use Pimcore\Bundle\PortalEngineBundle\Model\DataObject\PortalUserInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Can be used to dynamically modify the data object workspace permissions of the current user.
 */
class ResolveUserDataObjectWorkspacesEvent extends Event
{
    /**
     * @var WorkspaceConfig[]
     */
    private $workspaces;

    /**
     * @var PortalUserInterface
     */
    private $user;

    /**
     * @param WorkspaceConfig[] $workspaces
     * @param PortalUserInterface $user
     */
    public function __construct(array $workspaces, PortalUserInterface $user)
    {
        $this->workspaces = $workspaces;
        $this->user = $user;
    }

    /**
     * @return WorkspaceConfig[]
     */
    public function getWorkspaces(): array
    {
        return $this->workspaces;
    }

    /**
     * @return PortalUserInterface
     */
    public function getUser(): PortalUserInterface
    {
        return $this->user;
    }

    /**
     * @param WorkspaceConfig[] $workspaces
     */
    public function setWorkspaces(array $workspaces): void
    {
        $this->workspaces = $workspaces;
    }
}
