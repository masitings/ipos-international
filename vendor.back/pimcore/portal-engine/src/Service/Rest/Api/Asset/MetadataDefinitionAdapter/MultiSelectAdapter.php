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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\Asset\MetadataDefinitionAdapter;

use Pimcore\Model\Asset;
use Pimcore\Model\Asset\Image;
use Pimcore\Model\DataObject\AbstractObject;

class MultiSelectAdapter extends SelectAdapter
{
    /**
     * @param AbstractObject $object
     * @param Image $data
     * @param array $params
     *
     * @return array|mixed
     */
    public function getDataForDetail(Asset $asset, $value, $parameters = [])
    {
        if (empty($value)) {
            return null;
        }

        $result = [];
        foreach ($value as $item) {
            $result[] = [
                'label' => $this->getOptionLabel($item, $parameters),
                'value' => $item
            ];
        }

        return $result;
    }

    public function setDataFromDetail(Asset $asset, $value, $parameters = [])
    {
        if (!is_array($value)) {
            return null;
        }

        return array_filter(array_map(function ($item) {
            if (!is_array($item) || empty($item['value'])) {
                return null;
            }

            return $item['value'];
        }, $value));
    }

    public function getNormalizedData(Asset $asset, $value, $parameters = [])
    {
        $values = $this->getDataForDetail($asset, $value, $parameters);

        if (!$values) {
            return null;
        }

        return implode(', ', array_map(function ($value) {
            return $value['label'];
        }, $values));
    }
}
