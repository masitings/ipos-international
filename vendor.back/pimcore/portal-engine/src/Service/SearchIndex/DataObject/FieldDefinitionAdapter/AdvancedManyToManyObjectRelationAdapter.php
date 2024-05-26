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

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Data\ObjectMetadata;

/**
 * Class AdvancedManyToManyObjectRelationAdapter
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\DataObject\FieldDefinitionAdapter
 */
class AdvancedManyToManyObjectRelationAdapter extends ManyToManyObjectRelationAdapter implements FieldDefinitionAdapterInterface
{
    /**
     * @param Concrete $object
     *
     * @return string|array
     */
    protected function doGetIndexDataValue($object)
    {
        /** @var mixed $value */
        $value = [];
        /** @var ObjectMetadata[] $objectMetadata */
        $objectMetadata = $this->doGetRawIndexDataValue($object);

        if ($objectMetadata) {
            foreach ($objectMetadata as $objectMetadataEntry) {
                /** @var Concrete $dataObject */
                $dataObject = Concrete::getById($objectMetadataEntry->getObjectId());
                if ($dataObject) {
                    $value[] = $this->getArrayValuesByElement($dataObject);
                }
            }
        }

        return $value;
    }
}
