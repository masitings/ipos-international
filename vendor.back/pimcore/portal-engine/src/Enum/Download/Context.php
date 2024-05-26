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

namespace Pimcore\Bundle\PortalEngineBundle\Enum\Download;

use MyCLabs\Enum\Enum;

class Context extends Enum
{
    const LOCALIZED = 'localized';
    const CONTAINER_TYPE = 'containerType';
    const CONTAINER = 'container';
    const ATTRIBUTE = 'attribute';

    const CONTAINER_TYPE_OBJECTBRICK = 'objectbricks';
    const CONTAINER_TYPE_FIELDCOLLECTIONS = 'fieldcollections';

    /**
     * @param $attribute
     *
     * @return bool
     */
    public static function isContainer($attribute)
    {
        return in_array($attribute, [
            self::CONTAINER_TYPE_FIELDCOLLECTIONS,
            self::CONTAINER_TYPE_OBJECTBRICK
        ]);
    }
}
