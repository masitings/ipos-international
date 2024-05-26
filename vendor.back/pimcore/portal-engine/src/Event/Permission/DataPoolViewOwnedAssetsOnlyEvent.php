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

namespace Pimcore\Bundle\PortalEngineBundle\Event\Permission;

use Pimcore\Bundle\PortalEngineBundle\Event\Permission\AbstractEvent\DataPoolItemPermissionEvent;

/**
 * Can be used to deny or allow the "view onwned assets only" permission of an element for the current user.
 */
class DataPoolViewOwnedAssetsOnlyEvent extends DataPoolItemPermissionEvent
{
}
