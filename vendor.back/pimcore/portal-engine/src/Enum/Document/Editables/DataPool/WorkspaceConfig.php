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

namespace Pimcore\Bundle\PortalEngineBundle\Enum\Document\Editables\DataPool;

use MyCLabs\Enum\Enum;

class WorkspaceConfig extends Enum
{
    const WORKSPACE_PATH = 'workspace_path';
    const PERMISSION_VIEW = 'permission_view';
    const PERMISSION_DOWNLOAD = 'permission_download';
    const PERMISSION_EDIT = 'permission_edit';
    const PERMISSION_UPDATE = 'permission_update';
    const PERMISSION_CREATE = 'permission_create';
    const PERMISSION_DELETE = 'permission_delete';
    const PERMISSION_SUBFOLDER = 'permission_subfolder';
    const PERMISSION_VIEW_OWNED_ASSETS_ONLY = 'view_owned_assets_only';
}
