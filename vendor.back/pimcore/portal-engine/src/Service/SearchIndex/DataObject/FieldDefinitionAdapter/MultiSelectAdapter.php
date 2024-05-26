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

use Pimcore\Bundle\PortalEngineBundle\Service\DataObject\SelectOptionsExtractor;
use Pimcore\Model\DataObject\Concrete;

/**
 * Class MultiSelectAdapter
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\DataObject\FieldDefinitionAdapter
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
     * @param Concrete $object
     *
     * @return array
     */
    protected function doGetIndexDataValue($object)
    {
        /** @var array $values */
        $values = [];
        /** @var array $options */
        $options = $this->fieldDefinition->getOptions();
        /** @var array $selectValues */
        $selectValues = $this->doGetRawIndexDataValue($object);

        if (is_array($selectValues) && is_array($options)) {
            foreach ($selectValues as $selectValue) {
                $values[] = $this->selectOptionsExtractor->getKeyByValue($selectValue, $options);
            }
        }

        return $values;
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
