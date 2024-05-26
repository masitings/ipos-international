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

use Pimcore\Model\DataObject;

/**
 * Class ObjectRelationAdapter
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Asset\FieldDefinitionAdapter
 */
class ObjectRelationAdapter extends ManyToOneRelationAdapter implements FieldDefinitionAdapterInterface
{
    /**
     * @param mixed $data
     *
     * @return mixed
     */
    public function castMetaData($data)
    {
        /** @var string[] $castedMetaData */
        $castedMetaData = [];
        /** @var DataObject|null $dataObject */
        $dataObject = DataObject::getById($data);

        if ($dataObject) {
            $castedMetaData = $this->getArrayValuesByElement($dataObject);
        }

        return $castedMetaData;
    }
}
