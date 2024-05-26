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

use Pimcore\Model\Asset;
use Pimcore\Model\DataObject;
use Pimcore\Model\Element\ElementInterface;

/**
 * Class ManyToManyRelationAdapter
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Asset\FieldDefinitionAdapter
 */
class ManyToManyRelationAdapter extends ManyToOneRelationAdapter implements FieldDefinitionAdapterInterface
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

        if (is_array($data)) {
            foreach ($data as $dataEntry) {

                /** @var string $elementType */
                $elementType = $dataEntry[0];
                /** @var string $elementId */
                $elementId = $dataEntry[1];
                /** @var ElementInterface $element */
                $element = null;

                switch ($elementType) {
                    case 'object':
                        $element = DataObject::getById($elementId);
                        break;
                    case 'asset':
                        $element = Asset::getById($elementId);
                        break;
                }

                if ($element) {
                    $castedMetaData[] = $this->getArrayValuesByElement($element);
                }
            }
        }

        return $castedMetaData;
    }

    /**
     * @return bool
     */
    public function isSortable()
    {
        return false;
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
