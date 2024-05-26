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
 * Class FilterSort
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Enum\DataPool
 */
class FilterSort extends Enum
{
    const SORT_BY_VALUE = 'sort_by_value';
    const SORT_BY_LABEL = 'sort_by_label';
}
