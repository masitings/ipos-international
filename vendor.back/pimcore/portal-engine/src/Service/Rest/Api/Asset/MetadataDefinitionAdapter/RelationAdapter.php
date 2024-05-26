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

class RelationAdapter extends AbstractMetadataDefinitionAdapter
{
    use RelationAdapterTrait;

    public function getDataForDetail(Asset $asset, $value, $parameters = [])
    {
        if (!$value instanceof ElementInterface) {
            return null;
        }

        return $this->getElementValues($this->nameExtractorService, $this->urlExtractorService, $value);
    }

    public function setDataFromDetail(Asset $asset, $value, $parameters = [])
    {
        return $this->getElementById($value);
    }

    public function getNormalizedData(Asset $asset, $value, $parameters = [])
    {
        $data = $this->getDataForDetail($asset, $value, $parameters);

        if (!$data) {
            return null;
        }

        return $data['name'];
    }
}
