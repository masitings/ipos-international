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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\DataObject\FieldDefinitionAdapter;

use Pimcore\Model\Asset\Image;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Data\QuantityValue;

class QuantityValueAdapter extends DefaultAdapter
{
    /**
     * Used for QuantityValue and InputQuantityValue data type
     *
     * @param AbstractObject $object
     * @param Image $data
     * @param array $params
     *
     * @return array|mixed
     */
    public function getDataForDetail(AbstractObject $object, $data, array $params = [])
    {
        if (!$data instanceof QuantityValue) {
            return null;
        }

        return [
            'value' => $data->getValue(),
            'unitAbbrevation' => $data->getUnit() ? $data->getUnit()->getAbbreviation() : ''
        ];
    }
}
