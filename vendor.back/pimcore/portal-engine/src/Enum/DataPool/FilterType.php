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

namespace Pimcore\Bundle\PortalEngineBundle\Enum\DataPool;

use MyCLabs\Enum\Enum;

/**
 * Class DatabaseConfig
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Enum
 */
class FilterType extends Enum
{
    const SELECT = 'select';
    const MULTI_SELECT = 'multiSelect';
}
