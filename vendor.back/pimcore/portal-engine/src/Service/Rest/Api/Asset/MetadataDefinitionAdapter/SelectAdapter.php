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

use Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\Element\Adapter\SelectAdapterTrait;
use Pimcore\Model\Asset;

class SelectAdapter extends AbstractMetadataDefinitionAdapter
{
    use SelectAdapterTrait;

    public function getDataForDetail(Asset $asset, $value, $parameters = [])
    {
        if (empty($value)) {
            return null;
        }

        return [
            'label' => $this->getOptionLabel($value, $parameters),
            'value' => $value
        ];
    }

    public function setDataFromDetail(Asset $asset, $value, $parameters = [])
    {
        if (!is_array($value) || empty($value['value'])) {
            return null;
        }

        return $value['value'];
    }

    public function getNormalizedData(Asset $asset, $value, $parameters = [])
    {
        $data = $this->getDataForDetail($asset, $value, $parameters);

        if (!$data) {
            return null;
        }

        return $data['label'];
    }
}
