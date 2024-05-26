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
 * Class ElasticSearchFields
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Enum
 */
class ElasticSearchFields extends Enum
{
    const SYSTEM_FIELDS = 'system_fields';
    const STANDARD_FIELDS = 'standard_fields';
    const CUSTOM_FIELDS = 'custom_fields';

    const SYSTEM_FIELDS_ID = 'id';
    const SYSTEM_FIELDS_CREATION_DATE = 'creationDate';
    const SYSTEM_FIELDS_MODIFICATION_DATE = 'modificationDate';
    const SYSTEM_FIELDS_PUBLISHED = 'published';
    const SYSTEM_FIELDS_TYPE = 'type';
    const SYSTEM_FIELDS_KEY = 'key';
    const SYSTEM_FIELDS_PATH = 'path';
    const SYSTEM_FIELDS_FULL_PATH = 'fullPath';
    const SYSTEM_FIELDS_PATH_LEVELS = 'pathLevels';
    const SYSTEM_FIELDS_TAGS = 'tags';
    const SYSTEM_FIELDS_MIME_TYPE = 'mimetype';
    const SYSTEM_FIELDS_CLASS_NAME = 'className';
    const SYSTEM_FIELDS_NAME = 'name';
    const SYSTEM_FIELDS_THUMBNAIL = 'thumbnail';
    const SYSTEM_FIELDS_CHECKSUM = 'checksum';
    const SYSTEM_FIELDS_COLLECTIONS = 'collections';
    const SYSTEM_FIELDS_PUBLIC_SHARES = 'publicShares';
    const SYSTEM_FIELDS_USER_OWNER = 'userOwner';
    const SYSTEM_FIELDS_HAS_WORKFLOW_WITH_PERMISSIONS = 'hasWorkflowWithPermissions';
    const SYSTEM_FIELDS_FILE_SIZE = 'fileSize';

    const ORDER_BY_ASC = 'asc';
    const ORDER_BY_DESC = 'desc';

    const TYPE_TEXT = 'text';
    const TYPE_KEYWORD = 'keyword';
    const TYPE_DATE = 'date';
    const TYPE_FLOAT = 'float';
    const TYPE_INTEGER = 'integer';
    const TYPE_LONG = 'long';
    const TYPE_NESTED = 'nested';
    const TYPE_OBJECT = 'object';
    const TYPE_BOOLEAN = 'boolean';

    const TIEBREAKER_POSITION_BEFORE = 'before';
    const TIEBREAKER_POSITION_AFTER = 'after';
}
