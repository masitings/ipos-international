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

namespace Pimcore\Bundle\PortalEngineBundle\Document\Areabrick;

use Pimcore\Bundle\PortalEngineBundle\Document\AbstractAreabrick;
use Pimcore\Bundle\PortalEngineBundle\Enum\Document\AreabrickGroup;

class PortalEngineTeasers extends AbstractAreabrick
{
    public function getName()
    {
        return 'Teasers';
    }

    public function getGroup()
    {
        return AreabrickGroup::TEASER;
    }

    /**
     * {@inheritdoc}
     */
    public function getIcon()
    {
        return '/bundles/pimcoreadmin/img/flat-color-icons/edit_image.svg';
    }
}
