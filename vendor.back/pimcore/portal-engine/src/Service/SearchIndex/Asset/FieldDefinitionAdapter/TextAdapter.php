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

namespace Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Asset\FieldDefinitionAdapter;

/**
 * Class TextAdapter
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Asset\FieldDefinitionAdapter
 */
class TextAdapter extends DefaultAdapter implements FieldDefinitionAdapterInterface
{
    /**
     * @return bool
     */
    public function isFilterable()
    {
        return true;
    }
}
