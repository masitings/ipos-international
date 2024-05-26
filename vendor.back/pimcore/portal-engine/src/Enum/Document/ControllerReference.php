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

namespace Pimcore\Bundle\PortalEngineBundle\Enum\Document;

use MyCLabs\Enum\Enum;
use Pimcore\Bundle\PortalEngineBundle\Controller\DataPool\AssetController;
use Pimcore\Bundle\PortalEngineBundle\Controller\DataPool\DataObjectController;
use Pimcore\Bundle\PortalEngineBundle\Controller\LanguageVariantController;
use Pimcore\Bundle\PortalEngineBundle\Controller\PortalController;
use Pimcore\Bundle\PortalEngineBundle\Controller\SnippetController;

class ControllerReference extends Enum
{
    const PORTAL_PAGE = PortalController::class . '::pageAction';
    const PORTAL_CONTENT_PAGE = PortalController::class . '::contentAction';
    const DATA_POOL_DATA_OBJECTS_LIST = DataObjectController::class . '::listAction';
    const DATA_POOL_ASSETS_LIST = AssetController::class . '::listAction';
    const SNIPPET_FOOTER = SnippetController::class . '::footerAction';
    const LANGUAGE_VARIANT_DATA_POOL = LanguageVariantController::class . '::dataPoolLanguageVariantAction';
}
