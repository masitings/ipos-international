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
 * Used to render the correct area brick within ajax requests.
 */
class ImageThumbnails extends Enum
{
    const ELEMENT_TEASER = 'portal-engine_element-teaser';
    const ELEMENT_DETAIL = 'portal-engine_element-detail';
    const DETAIL_PAGE = 'portal-engine_detail-page';
    const LOGO = 'portal-engine-logo';

    /**
     * @deprecated
     */
    const FOOTER_LOGO = 'portal-engine_footer-logo';
}
