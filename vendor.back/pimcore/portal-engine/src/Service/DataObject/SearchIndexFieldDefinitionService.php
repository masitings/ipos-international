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

namespace Pimcore\Bundle\PortalEngineBundle\Service\DataObject;

use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\DataObject\FieldDefinitionAdapter\FieldDefinitionAdapterInterface;
use Pimcore\Model\DataObject\ClassDefinition;
use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * Class SearchIndexFieldDefinitionService
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\DataObject
 */
class SearchIndexFieldDefinitionService
{
    /**
     * @var ServiceLocator
     */
    protected $adapterLocator;

    /**
     * SearchIndexFieldDefinitionService constructor.
     *
     * @param ServiceLocator $adapterLocator
     */
    public function __construct(ServiceLocator $adapterLocator)
    {
        $this->adapterLocator = $adapterLocator;
    }

    /**
     * @param ClassDefinition\Data $fieldDefinition
     *
     * @return FieldDefinitionAdapterInterface|null
     */
    public function getFieldDefinitionAdapter(ClassDefinition\Data $fieldDefinition)
    {
        /** @var FieldDefinitionAdapterInterface|null $adapter */
        $adapter = null;

        if ($this->adapterLocator->has($fieldDefinition->getFieldtype())) {
            $adapter = $this->adapterLocator->get($fieldDefinition->getFieldtype());
            $adapter->setFieldDefinition($fieldDefinition);
        }

        return $adapter;
    }
}
