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

namespace Pimcore\Bundle\PortalEngineBundle\Document;

use Pimcore\Bundle\PortalEngineBundle\Enum\Document\AreabrickPlace;
use Pimcore\Extension\Document\Areabrick\AbstractTemplateAreabrick;
use Pimcore\Model\Document\Editable\Area\Info;

abstract class AbstractAreabrick extends AbstractTemplateAreabrick
{
    /**
     * @return string
     */
    public function getTemplateLocation()
    {
        return static::TEMPLATE_LOCATION_BUNDLE;
    }

    public function getTemplateSuffix()
    {
        return static::TEMPLATE_SUFFIX_TWIG;
    }

    public function getHtmlTagOpen(Info $info)
    {
        return '<div class="pimcore_area_' . $info->getId() . ' pimcore_area_content my-4">';
    }

    public function forceEditInView()
    {
        return false;
    }

    public function getGroup()
    {
        return null;
    }

    public function getAllowedPlaces()
    {
        return [
            AreabrickPlace::PORTAL
        ];
    }
}
