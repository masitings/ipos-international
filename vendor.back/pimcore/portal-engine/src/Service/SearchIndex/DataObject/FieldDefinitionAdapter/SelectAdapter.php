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
 * Class SelectAdapter
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\DataObject\FieldDefinitionAdapter
 */
class SelectAdapter extends DefaultAdapter implements FieldDefinitionAdapterInterface
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
     * @return string|null
     */
    protected function doGetIndexDataValue($object)
    {
        /** @var string|null $value */
        $value = null;
        /** @var array $options */
        $options = $this->fieldDefinition->getOptions();

        if (is_array($options)) {
            $value = $this->selectOptionsExtractor->getKeyByValue($this->doGetRawIndexDataValue($object), $options);
        }

        return $value;
    }

    /**
     * @return bool
     */
    public function isFilterable()
    {
        return true;
    }
}
