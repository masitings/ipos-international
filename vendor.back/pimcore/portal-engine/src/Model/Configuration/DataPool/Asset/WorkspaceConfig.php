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

namespace Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Asset;

class WorkspaceConfig extends \Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\WorkspaceConfig
{
    /**
     * @var bool
     */
    protected $permissionEdit;

    /**
     * @var bool
     */
    protected $permissionUpdate;

    /**
     * @var bool
     */
    protected $permissionCreate;

    /**
     * @var bool
     */
    protected $permissionDelete;

    /**
     * @var bool
     */
    protected $permissionSubfolder;

    /**
     * @var bool
     */
    protected $permissionViewOwnedAssetsOnly;

    public function __construct(string $fullPath, bool $permissionView, bool $permissionDownload, bool $permissionEdit = true, bool $permissionUpdate = true, bool $permissionCreate = true, bool $permissionDelete = true, bool $permissionSubfolder = true, bool $permissionViewOwnedAssetsOnly = true)
    {
        parent::__construct($fullPath, $permissionView, $permissionDownload);
        $this->permissionEdit = $permissionEdit;
        $this->permissionUpdate = $permissionUpdate;
        $this->permissionCreate = $permissionCreate;
        $this->permissionDelete = $permissionDelete;
        $this->permissionSubfolder = $permissionSubfolder;
        $this->permissionViewOwnedAssetsOnly = $permissionViewOwnedAssetsOnly;
    }

    /**
     * @return bool
     */
    public function getPermissionCreate(): bool
    {
        return $this->permissionCreate;
    }

    /**
     * @return bool
     */
    public function getPermissionEdit(): bool
    {
        return $this->permissionEdit;
    }

    /**
     * @return bool
     */
    public function getPermissionUpdate(): bool
    {
        return $this->permissionUpdate;
    }

    /**
     * @return bool
     */
    public function getPermissionDelete(): bool
    {
        return $this->permissionDelete;
    }

    /**
     * @return bool
     */
    public function getPermissionSubfolder(): bool
    {
        return $this->permissionSubfolder;
    }

    /**
     * @return bool
     */
    public function getPermissionViewOwnedAssetsOnly(): bool
    {
        return $this->permissionViewOwnedAssetsOnly;
    }
}
