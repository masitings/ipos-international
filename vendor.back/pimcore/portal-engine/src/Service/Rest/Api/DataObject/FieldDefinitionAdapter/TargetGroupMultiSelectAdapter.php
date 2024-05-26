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
use Pimcore\Model\Tool\Targeting\TargetGroup;

class TargetGroupMultiSelectAdapter extends DefaultAdapter
{
    /**
     *
     * @param AbstractObject $object
     * @param Image $data
     * @param array $params
     *
     * @return array|mixed
     */
    public function getDataForDetail(AbstractObject $object, $data, array $params = [])
    {
        if (empty($data)) {
            return null;
        }

        $result = [];

        foreach ($data as $id) {
            if ($targetGroup = TargetGroup::getById($id)) {
                $result[] = [
                    'id' => $targetGroup->getId(),
                    'name' => $targetGroup->getName(),
                    'description' => $targetGroup->getDescription(),
                ];
            }
        }

        return $result;
    }
}
