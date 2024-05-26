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

use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Data\ElementMetadata;
use Pimcore\Model\Element\ElementInterface;

/**
 * Class AdvancedManyToManyRelationAdapter
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\DataObject\FieldDefinitionAdapter
 */
class AdvancedManyToManyRelationAdapter extends ManyToManyObjectRelationAdapter implements FieldDefinitionAdapterInterface
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
        /** @var ElementMetadata[] $elementMetadata */
        $elementMetadata = $this->doGetRawIndexDataValue($object);

        if ($elementMetadata) {
            foreach ($elementMetadata as $elementMetadataEntry) {
                /** @var ElementInterface $element */
                $element = $elementMetadataEntry->getElement();
                if ($element instanceof Concrete || $element instanceof Asset) {
                    $value[] = $this->getArrayValuesByElement($element);
                }
            }
        }

        return $value;
    }
}
