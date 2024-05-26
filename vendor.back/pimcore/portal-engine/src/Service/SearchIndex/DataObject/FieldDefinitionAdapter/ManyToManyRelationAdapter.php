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
use Pimcore\Model\Element\ElementInterface;

/**
 * Class ManyToManyRelationAdapter
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\DataObject\FieldDefinitionAdapter
 */
class ManyToManyRelationAdapter extends ManyToOneRelationAdapter implements FieldDefinitionAdapterInterface
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
        /** @var ElementInterface[] $elements */
        $elements = $this->doGetRawIndexDataValue($object);

        if ($elements) {
            foreach ($elements as $element) {
                if ($element instanceof Concrete || $element instanceof Asset) {
                    $value[] = $this->getArrayValuesByElement($element);
                }
            }
        }

        return $value;
    }

    /**
     * @return bool
     */
    public function isSortable()
    {
        return false;
    }
}
