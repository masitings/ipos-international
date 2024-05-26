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

namespace Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool;

class WorkspaceConfig
{
    /**
     * @var string
     */
    protected $fullPath;

    /**
     * @var bool
     */
    protected $permissionView;

    /**
     * @var bool
     */
    protected $permissionDownload;

    public function __construct(string $fullPath, bool $permissionView, bool $permissionDownload)
    {
        $this->fullPath = $fullPath;
        $this->permissionView = $permissionView;
        $this->permissionDownload = $permissionDownload;
    }

    /**
     * @return string
     */
    public function getFullPath(): string
    {
        return $this->fullPath;
    }

    /**
     * @return bool
     */
    public function getPermissionView(): bool
    {
        return $this->permissionView;
    }

    /**
     * @return bool
     */
    public function getPermissionDownload(): bool
    {
        return $this->permissionDownload;
    }

    /**
     * @param bool $permissionView
     */
    public function setPermissionView(bool $permissionView): void
    {
        $this->permissionView = $permissionView;
    }

    /**
     * @param bool $permissionDownload
     */
    public function setPermissionDownload(bool $permissionDownload): void
    {
        $this->permissionDownload = $permissionDownload;
    }
}
