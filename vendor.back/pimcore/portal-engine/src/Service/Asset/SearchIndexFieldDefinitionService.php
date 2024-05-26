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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Asset;

use Pimcore\AssetMetadataClassDefinitionsBundle\Model\ClassDefinition\Data\Data;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Asset\FieldDefinitionAdapter\FieldDefinitionAdapterInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * Class SearchIndexFieldDefinitionService
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\Asset
 */
class SearchIndexFieldDefinitionService
{
    /**
     * @var ServiceLocator
     */
    protected $adapterLocator;

    /**
     * RestApiFieldDefinitionService constructor.
     *
     * @param ServiceLocator $adapterLocator
     */
    public function __construct(ServiceLocator $adapterLocator)
    {
        $this->adapterLocator = $adapterLocator;
    }

    /**
     * @param Data $fieldDefinition
     *
     * @return FieldDefinitionAdapterInterface|null
     */
    public function getFieldDefinitionAdapter(Data $fieldDefinition)
    {
        /** @var FieldDefinitionAdapterInterface|null $adapter */
        $adapter = null;

        if ($this->adapterLocator->has($fieldDefinition->fieldtype)) {
            $adapter = $this->adapterLocator->get($fieldDefinition->fieldtype);
            $adapter->setFieldDefinition($fieldDefinition);
        }

        return $adapter;
    }
}
