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

/**
 * Class Wysiwyg
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Document\Areabrick
 */
class PortalEngineWysiwyg extends AbstractAreabrick
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'Wysiwyg';
    }

    /**
     * @return string|null
     */
    public function getGroup()
    {
        return AreabrickGroup::CONTENT;
    }

    /**
     * {@inheritdoc}
     */
    public function getIcon()
    {
        return '/bundles/pimcoreadmin/img/flat-color-icons/wysiwyg.svg';
    }
}
