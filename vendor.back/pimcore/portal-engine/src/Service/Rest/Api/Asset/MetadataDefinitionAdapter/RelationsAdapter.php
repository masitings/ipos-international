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

use Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\Element\Adapter\RelationAdapterTrait;
use Pimcore\Model\Asset;
use Pimcore\Model\Element\ElementInterface;

class RelationsAdapter extends AbstractMetadataDefinitionAdapter
{
    use RelationAdapterTrait;

    public function getDataForDetail(Asset $asset, $value, $parameters = [])
    {
        if (empty($value) || !is_array($value)) {
            return null;
        }

        $data = [];

        foreach ($value as $item) {
            if (!$item instanceof ElementInterface) {
                continue;
            }

            $data[] = $this->getElementValues($this->nameExtractorService, $this->urlExtractorService, $item);
        }

        return $data;
    }

    public function setDataFromDetail(Asset $asset, $value, $parameters = [])
    {
        if (!is_array($value) || empty($value)) {
            return null;
        }

        return array_filter(array_map(function ($item) {
            return $this->getElementById($item);
        }, $value));
    }

    public function getNormalizedData(Asset $asset, $value, $parameters = [])
    {
        $data = $this->getDataForDetail($asset, $value, $parameters);

        if (!$data) {
            return null;
        }

        return implode(', ', array_map(function ($value) {
            return $value['name'];
        }, $data));
    }
}
