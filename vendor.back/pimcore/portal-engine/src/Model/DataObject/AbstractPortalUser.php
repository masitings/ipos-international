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

namespace Pimcore\Bundle\PortalEngineBundle\Model\DataObject;

use Pimcore\Bundle\PortalEngineBundle\Model\DataObject\Traits\PortalUserTrait;
use Pimcore\Model\DataObject\Concrete;

abstract class AbstractPortalUser extends Concrete implements PortalUserInterface
{
    use PortalUserTrait;
}
