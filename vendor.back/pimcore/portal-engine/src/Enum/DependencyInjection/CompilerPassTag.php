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

namespace Pimcore\Bundle\PortalEngineBundle\Enum\DependencyInjection;

use MyCLabs\Enum\Enum;

/**
 * Class UserConfig
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Enum
 */
class CompilerPassTag extends Enum
{
    const DOWNLOAD_FORMAT = 'pimcore.portal_engine.download_format';
    const DATA_POOL_SERVICE = 'pimcore.portal_engine.data_pool_service';
    const PRE_CONDITION_SERVICE_HANDLER = 'pimcore.portal_engine.pre_condition_service_handler';
    const OBJECT_SEARCH_INDEX_FIELD_DEFINITION = 'pimcore.portal_engine.object.search_index_field_definition';
    const ASSET_SEARCH_INDEX_FIELD_DEFINITION = 'pimcore.portal_engine.asset.search_index_field_definition';
    const REST_API_FIELD_DEFINITION = 'pimcore.portal_engine.rest_api_field_definition';
    const REST_API_METADATA_DEFINITION = 'pimcore.portal_engine.rest_api_metadata_definition';
}
