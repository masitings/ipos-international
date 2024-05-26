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
class Wizard extends Enum
{
    const TYPE_PORTAL = 'portal';
    const TYPE_ASSET = 'asset';
    const TYPE_OBJECT = 'object';

    const FINISHED = 'finished';
    const SUCCESS = 'success';
    const ERROR = 'error';

    const WIZARD_STATUS_MESSAGE = 'status_message';
    const PORTAL_CONFIG = 'config';

    const PORT_DOCUMENT_ID = 'portalDocumentId';

    const TMP_STORE_TAG = 'portal-engine_wizard';
    const LIFETIME = 60 * 60 * 12;
}
