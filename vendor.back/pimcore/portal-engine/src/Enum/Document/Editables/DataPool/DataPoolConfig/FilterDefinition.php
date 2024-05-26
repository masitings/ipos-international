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

namespace Pimcore\Bundle\PortalEngineBundle\Enum\Document\Editables\DataPool\DataPoolConfig;

use MyCLabs\Enum\Enum;

class FilterDefinition extends Enum
{
    const FILTER_TYPE = 'filter_type';
    const FILTER_ATTRIBUTE = 'filter_attribute';
    const FILTER_PARAM_NAME = 'filter_param_name';
    const FILTER_PARAM_POSTFIX = 'Filter';
}
