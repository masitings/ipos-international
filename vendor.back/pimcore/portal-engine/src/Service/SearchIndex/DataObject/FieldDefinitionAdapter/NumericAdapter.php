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

namespace Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\DataObject\FieldDefinitionAdapter;

use Pimcore\Bundle\PortalEngineBundle\Enum\ElasticSearchFields;
use Pimcore\Model\DataObject\Concrete;

/**
 * Class NumericAdapter
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\DataObject\FieldDefinitionAdapter
 */
class NumericAdapter extends DefaultAdapter implements FieldDefinitionAdapterInterface
{
    /**
     * @return array
     */
    public function getESMapping()
    {
        return [
            $this->fieldDefinition->getName(),
            [
                'type' => ElasticSearchFields::TYPE_FLOAT,
            ]
        ];
    }

    /**
     * @param Concrete $object
     *
     * @return string|array
     */
    protected function doGetIndexDataValue($object)
    {
        return (float)$this->doGetRawIndexDataValue($object);
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return '';
    }

    /**
     * @return bool
     */
    public function isFilterable()
    {
        return true;
    }
}
