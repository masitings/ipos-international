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

class DefaultAdapter extends AbstractMetadataDefinitionAdapter
{
    public function getDataForDetail(Asset $asset, $value, $parameters = [])
    {
        return $value;
    }

    public function getNormalizedData(Asset $asset, $value, $parameters = [])
    {
        $data = $this->getDataForDetail($asset, $value, $parameters);

        if (!$data) {
            return null;
        }

        return (string) $data;
    }
}
