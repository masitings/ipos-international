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
use Pimcore\Model\User;

class UserAdapter extends AbstractMetadataDefinitionAdapter
{
    public function getDataForDetail(Asset $asset, $value, $parameters = [])
    {
        if (!$value instanceof User) {
            return null;
        }

        return [
            'value' => $value->getId(),
            'label' => $value->getName()
        ];
    }

    public function setDataFromDetail(Asset $asset, $value, $parameters = [])
    {
        if (!is_array($value) || empty($value['value'])) {
            return null;
        }

        return User::getById($value['value']);
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
