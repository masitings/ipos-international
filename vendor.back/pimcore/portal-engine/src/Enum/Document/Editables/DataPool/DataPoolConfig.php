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

class DataPoolConfig extends Enum
{
    const WORKSPACE_DEFINITION = 'workspace_definition';
    const GRID_CONFIGURATION_FILTERS = 'grid_configuration_filters';
    const PRECONDITION_SERVICE_ID = 'precondition_service_id';
    const ENABLE_TAG_NAVIGATION = 'enable_tag_navigation';
    const ROOT_TAG = 'root_tag';
    const ENABLE_VERSION_HISTORY = 'enable_version_history';
    const ENABLE_FOLDER_NAVIGATION = 'enable_folder_navigation';
    const GRID_CONFIGURATION_SORT_OPTIONS = 'grid_configuration_sort_options';
    const AVAILABLE_DOWNLOAD_THUMBNAILS = 'available_download_thumbnails';
    const GRID_CONFIGURATION_ATTRIBUTES = 'grid_configuration_attributes';
    const GRID_CONFIGURATION_NAME_ATTRIBUTE = 'grid_configuration_name_attribute';
    const VISIBLE_LANGUAGES = 'visible_languages';
    const EDITABLE_LANGUAGES = 'editable_languages';
    const AVAILABLE_DOWNLOAD_FORMATS = 'available_download_formats';
}
