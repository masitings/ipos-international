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

namespace Pimcore\Bundle\PortalEngineBundle\Enum\Index;

use MyCLabs\Enum\Enum;

/**
 * Class DatabaseConfig
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Enum
 */
class DatabaseConfig extends Enum
{
    const QUEUE_TABLE_NAME = 'portal_engine_search_index_queue';

    const QUEUE_TABLE_COLUMN_ELEMENT_TYPE_DATA_OBJECT = 'dataObject';
    const QUEUE_TABLE_COLUMN_ELEMENT_TYPE_ASSET = 'asset';

    const QUEUE_TABLE_COLUMN_OPERATION_UPDATE = 'update';
    const QUEUE_TABLE_COLUMN_OPERATION_DELETE = 'delete';
}
