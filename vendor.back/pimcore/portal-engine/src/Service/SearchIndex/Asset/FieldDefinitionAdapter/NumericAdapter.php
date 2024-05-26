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

use Pimcore\Bundle\PortalEngineBundle\Enum\ElasticSearchFields;

/**
 * Class NumericAdapter
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Asset\FieldDefinitionAdapter
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
     * @param mixed $data
     *
     * @return float|mixed
     */
    public function castMetaData($data)
    {
        return (float)$data;
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
