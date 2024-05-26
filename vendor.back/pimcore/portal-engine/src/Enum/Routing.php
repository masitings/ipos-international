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
 * Class UserConfig
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Enum
 */
class Routing extends Enum
{
    const PREFIX_PARAM = '_portal_engine_prefix';
    const NAVIGATION_ROOT_PROPERTY = 'portal-engine_navigation-root';
}
