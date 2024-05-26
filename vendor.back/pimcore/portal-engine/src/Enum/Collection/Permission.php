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

namespace Pimcore\Bundle\PortalEngineBundle\Enum\Collection;

use MyCLabs\Enum\Enum;

/**
 * Class Permission
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Enum\Collection
 */
class Permission extends Enum
{
    const READ = 'read';
    const EDIT = 'edit';
}
