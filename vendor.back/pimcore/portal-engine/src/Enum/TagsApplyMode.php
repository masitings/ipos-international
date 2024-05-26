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
 * @package Pimcore\Bundle\PortalEngineBundle\Enum
 */
class TagsApplyMode extends Enum
{
    const ADD = 'add';
    const OVERWRITE = 'overwrite';
    const DELETE = 'delete';
}
