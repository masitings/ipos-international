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

class DownloadContext extends Enum
{
    const UNDEFINED = 'Undefined';
    const MULTI_DOWNLOAD = 'Multi Download';
    const SINGLE_DOWNLOAD = 'Single Download';
    const DIRECT_DOWNLOAD_SHORTCUT = 'Direct Download Shortcut';
    const CART = 'Cart';
    const COLLECTION = 'Total Collection Data Pool';
    const GUEST_SHARE = 'Total Guest Share Data Pool';
}
