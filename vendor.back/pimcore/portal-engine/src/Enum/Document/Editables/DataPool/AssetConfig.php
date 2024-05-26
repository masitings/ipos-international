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

class AssetConfig extends Enum
{
    public const UPLOAD_FOLDER = 'upload_folder';
    public const DETAIL_PAGE_SHORTCUT_DOWNLOADS = 'detail_page_shortcut_downloads';
    public const DETAIL_PAGE_METADATA_CLASS_DEFINITIONS = 'metadata_class_definitions';
    public const GENERAL_ATTRIBUTES = 'general_attributes';
    const DIRECT_DOWNLOAD_SHORTCUTS = 'direct_download_shortcuts';
}
