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

namespace Pimcore\Bundle\PortalEngineBundle\Model;

use Symfony\Component\HttpFoundation\ParameterBag;

class BasicJsonModel extends ParameterBag implements \JsonSerializable
{
    public function jsonSerialize()
    {
        return $this->all();
    }
}
