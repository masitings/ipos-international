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

namespace Pimcore\Bundle\PortalEngineBundle\Enum;

use MyCLabs\Enum\Enum;

/**
 * Class UserConfig
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Enum
 */
class Permission extends Enum
{
    const PORTAL_ACCESS = 'portal-access';
    const DATA_POOL_ACCESS = 'data-pool-access';
    const VERSION_HISTORY = 'version-history';
    const DATA_POOL_ASSET_UPLOAD_FOLDER_REVIEWING = 'asset-upload-folder-reviewing';
    const STATISTIC_EXPLORER_ACCESS = 'statistic-explorer-access';

    const VIEW = 'view';
    const DOWNLOAD = 'download';
    const EDIT = 'edit';
    const UPDATE = 'update';
    const CREATE = 'create';
    const DELETE = 'delete';
    const SUBFOLDER = 'subfolder';
    const VIEW_OWNED_ASSET_ONLY = 'viewOwnedAssetOnly';

    const DOWNLOAD_FORMAT_ACCESS = 'download-format-access';

    const PERMISSION_DELIMITER = '___';
}
