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
use Pimcore\Model\DataObject\Data\InputQuantityValue;
use Pimcore\Model\DataObject\Data\QuantityValue;

/**
 * Class QuantityValueAdapter
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\DataObject\FieldDefinitionAdapter
 */
class QuantityValueAdapter extends NumericAdapter implements FieldDefinitionAdapterInterface
{
    /**
     * @return array
     */
    public function getESMapping()
    {
        return [
            $this->fieldDefinition->getName(),
            [
                'properties' => [
                    'value' => [
                        'type' => ElasticSearchFields::TYPE_FLOAT,
                    ],
                    'unitAbbrevation' => [
                        'type' => ElasticSearchFields::TYPE_TEXT,
                    ]
                ]

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
        /** @var array $value */
        $value = [];
        /** @var InputQuantityValue|QuantityValue $data */
        $data = $this->doGetRawIndexDataValue($object);

        if ($data instanceof QuantityValue) {
            $value = [
                'value' => (float)$data->getValue(),
                'unitAbbreviation' => $data->getUnit() ? trim($data->getUnit()->getAbbreviation()) : ''
            ];
        }

        return $value;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return 'value';
    }

    /**
     * @return bool
     */
    public function isListable()
    {
        return false;
    }
}
