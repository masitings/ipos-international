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
use Pimcore\AssetMetadataClassDefinitionsBundle\Model\Configuration;
use Pimcore\AssetMetadataClassDefinitionsBundle\Service;

class AttributeService
{
    public function getAllAttributesStore()
    {
        $attributes = $this->getAllAttributes();

        return array_map(function ($attribute) {
            return [$attribute, $attribute];
        }, $attributes);
    }

    public function getAllAttributes()
    {
        $attributes = [];

        $configurations = Configuration\Dao::getList(true);

        if (!empty($configurations)) {
            foreach ($configurations as $configuration) {
                $definitions = [];
                $localizedDefinitions = [];

                /**
                 * @var Data[] $definitions
                 */
                Service::extractDataDefinitions($configuration->getLayoutDefinitions(), false, $definitions, $localizedDefinitions);

                $definitions = array_merge($definitions, $localizedDefinitions);

                foreach ($definitions as $definition) {
                    $attributes[] = $this->getAttributeNameByDefinition($configuration, $definition);
                }
            }
        }

        return $attributes;
    }

    /**
     * @param Configuration $configuration
     * @param string $attribute
     *
     * @return string
     */
    public function getAttributeName(Configuration $configuration, string $attribute)
    {
        return "{$configuration->getPrefix()}.{$attribute}";
    }

    /**
     * @param string $attributeName
     *
     * @return string
     */
    public function getCollectionFromAttributeName(string $attributeName)
    {
        list($collection) = explode('.', $attributeName);

        return $collection;
    }

    /**
     * @param string $attributeName
     *
     * @return string
     */
    public function getAttributeFromAttributeName(string $attributeName)
    {
        list($collection, $attribute) = explode('.', $attributeName);

        return $attribute;
    }

    /**
     * @param Configuration $configuration
     * @param Data $definition
     *
     * @return string
     */
    public function getAttributeNameByDefinition(Configuration $configuration, Data $definition)
    {
        return $this->getAttributeName($configuration, $definition->getName());
    }
}
