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

/**
 * Class ManyToManyObjectRelationAdapter
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\DataObject\FieldDefinitionAdapter
 */
class ManyToManyObjectRelationAdapter extends ManyToManyRelationAdapter implements FieldDefinitionAdapterInterface
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
        /** @var Concrete[] $dataObjects */
        $dataObjects = $this->doGetRawIndexDataValue($object);

        if ($dataObjects) {
            foreach ($dataObjects as $dataObject) {
                if ($dataObject) {
                    $value[] = $this->getArrayValuesByElement($dataObject);
                }
            }
        }

        return $value;
    }

    public function exportDataToString($exportData): string
    {
        if (!is_array($exportData)) {
            return '';
        }

        $names = [];
        foreach ($exportData as $item) {
            if ($item['name']) {
                $names[] = $item['name'];
            }
        }

        return implode(', ', $names);
    }
}
