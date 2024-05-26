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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Element;

use Pimcore\Model\DataObject\ClassDefinition;
use Symfony\Component\DependencyInjection\ServiceLocator;

abstract class AbstractDefinitionService
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
     * @param ClassDefinition\Data|\Pimcore\AssetMetadataClassDefinitionsBundle\Model\ClassDefinition\Data\Data $fieldDefinition
     *
     * @return mixed
     */
    public function getAdapter($fieldDefinition)
    {
        $adapter = null;

        if ($this->adapterLocator->has($fieldDefinition->fieldtype)) {
            $adapter = $this->adapterLocator->get($fieldDefinition->fieldtype);
        } else {
            $adapter = $this->adapterLocator->get('default');
        }

        $adapter->setFieldDefinition($fieldDefinition);

        return $adapter;
    }
}
