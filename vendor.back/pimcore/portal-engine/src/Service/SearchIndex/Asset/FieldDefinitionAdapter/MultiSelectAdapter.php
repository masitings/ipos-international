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

use Pimcore\Bundle\PortalEngineBundle\Service\DataObject\SelectOptionsExtractor;

/**
 * Class MultiSelectAdapter
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Asset\FieldDefinitionAdapter
 */
class MultiSelectAdapter extends DefaultAdapter implements FieldDefinitionAdapterInterface
{
    /** @var SelectOptionsExtractor */
    protected $selectOptionsExtractor;

    /**
     * @param SelectOptionsExtractor $selectOptionsExtractor
     * @required
     */
    public function setSelectOptionsExtractor(SelectOptionsExtractor $selectOptionsExtractor): void
    {
        $this->selectOptionsExtractor = $selectOptionsExtractor;
    }

    /**
     * @param mixed $data
     *
     * @return string[]
     */
    public function castMetaData($data)
    {
        /** @var string[] $castedMetaData */
        $castedMetaData = [];
        /** @var array $options */
        $options = $this->fieldDefinition->getOptions();

        if (!empty($data) && is_array($options)) {
            /** @var string[] $data */
            $data = is_array($data) ? $data : explode(',', $data);

            foreach ($data as $selectValue) {
                $castedMetaData[] = $this->selectOptionsExtractor->getKeyByValue($selectValue, $options);
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

    /**
     * @return bool
     */
    public function isFilterable()
    {
        return true;
    }
}
