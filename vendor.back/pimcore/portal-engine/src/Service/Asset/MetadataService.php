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
use Pimcore\AssetMetadataClassDefinitionsBundle\Model\Collections;
use Pimcore\AssetMetadataClassDefinitionsBundle\Model\Configuration;
use Pimcore\AssetMetadataClassDefinitionsBundle\Service;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\LanguagesService;
use Pimcore\Cache\Runtime;
use Pimcore\Model\Asset;
use Pimcore\Tool;

class MetadataService
{
    const LOCALIZED_FIELDS = 'localizedfields';
    const REMOVED_KEY = 'removed';
    const META_KEY = 'meta';
    const METADATA_KEY = 'metadata';

    protected $restApiMetadataDefinitionService;
    protected $attributeService;
    protected $languagesService;

    public function __construct(RestApiMetadataDefinitionService $restApiMetadataDefinitionService, AttributeService $attributeService, LanguagesService $languagesService)
    {
        $this->restApiMetadataDefinitionService = $restApiMetadataDefinitionService;
        $this->attributeService = $attributeService;
        $this->languagesService = $languagesService;
    }

    /**
     * @param Asset|null $asset
     *
     * @return array
     */
    public function getLayoutDefinitions(?Asset $asset = null)
    {
        $configurations = Configuration\Dao::getList(true);

        $data = [];
        foreach ($configurations as $configuration) {
            $definition = $configuration->getLayoutDefinitions();
            $this->enrichLayoutDefinition($definition, $asset);

            $data[$configuration->getPrefix()] = $definition;
        }

        return $data;
    }

    /**
     * @param $layout
     * @param Asset|null $asset
     */
    public function enrichLayoutDefinition(&$layout, ?Asset $asset = null)
    {
        $context = ['asset' => $asset];

        if (method_exists($layout, 'enrichLayoutDefinition')) {
            $layout->enrichLayoutDefinition($asset, $context);
        }

        if (method_exists($layout, 'enrichDefinition')) {
            $layout->enrichDefinition();
        }

        if (method_exists($layout, 'getChildren')) {
            $children = $layout->getChildren();
            if (is_array($children)) {
                foreach ($children as &$child) {
                    $this->enrichLayoutDefinition($child, $asset);
                }
            }
        }
    }

    /**
     * @param Asset $asset
     *
     * @return array
     */
    public function getMetadata(Asset $asset)
    {
        $collection = Collections::getByAssetId($asset->getId());

        if (!$collection || empty($collection->getCollections())) {
            return [];
        }

        $data = [];

        foreach ($collection->getCollections() as $collection) {
            $data = array_merge($data, $this->getMetadataByCollection($asset, $collection));
        }

        return $data;
    }

    /**
     * @param Asset $asset
     * @param string $collection
     *
     * @return array
     */
    protected function getMetadataByCollection(Asset $asset, string $collection)
    {
        $configuration = Configuration\Dao::getByName($collection);

        if (!$configuration) {
            return [];
        }

        $data = [];
        $fieldDefinitions = [];
        $localizedFieldDefinitions = [];

        Service::extractDataDefinitions($configuration->getLayoutDefinitions(), false, $fieldDefinitions, $localizedFieldDefinitions);

        foreach ($fieldDefinitions as $fieldDefinition) {
            $this->addValue($asset, $configuration, $fieldDefinition, $data);
        }

        foreach (Tool::getValidLanguages() as $language) {
            foreach ($localizedFieldDefinitions as $localizedFieldDefinition) {
                $this->addValue($asset, $configuration, $localizedFieldDefinition, $data, $language);
            }
        }

        return $data;
    }

    /**
     * @param Asset $asset
     * @param Configuration $configuration
     * @param Data $fieldDefinition
     * @param array $data
     * @param string|null $language
     */
    protected function addValue(Asset $asset, Configuration $configuration, Data $fieldDefinition, array &$data, ?string $language = null)
    {
        $attribute = $this->attributeService->getAttributeNameByDefinition($configuration, $fieldDefinition);
        $adapter = $this->restApiMetadataDefinitionService->getMetadataDefinitionAdapter($fieldDefinition);

        $value = $this->getRawMetadata(
            $asset,
            $attribute,
            $language
        );

        $value = $adapter->getDataForDetail($asset, $value);

        if ($language) {
            $data[$configuration->getPrefix()][self::LOCALIZED_FIELDS][$fieldDefinition->getName()][$language] = $value;
        } else {
            $data[$configuration->getPrefix()][$fieldDefinition->getName()] = $value;
        }
    }

    /**
     * @param Asset $asset
     * @param string $attribute
     * @param string|null $language
     *
     * @return mixed
     */
    protected function getRawMetadata(Asset $asset, string $attribute, ?string $language = null)
    {
        return $asset->getMetadata($attribute, $language);
    }

    /**
     * @param Asset $asset
     * @param string $attribute
     * @param $value
     * @param string|null $language
     */
    protected function setRawMetadata(Asset $asset, string $attribute, string $type, $value, ?string $language = null)
    {
        $asset->addMetadata($attribute, $type, $value, $language);
    }

    /**
     * @param Asset $asset
     * @param array $data
     * @param bool $ignoreRemove
     */
    public function setMetadata(Asset $asset, array $data, bool $ignoreRemove = false)
    {
        if (empty($data)) {
            return;
        }

        $metadata = $data[self::METADATA_KEY];
        $meta = $data[self::META_KEY] ?: [];
        $removedCollections = $data[self::REMOVED_KEY] ?: [];

        if (empty($metadata)) {
            return;
        }

        $validCollections = [];

        foreach ($metadata as $collection => $data) {
            $configuration = Configuration\Dao::getByName($collection);

            if (!$configuration) {
                continue;
            }

            if (!in_array($collection, $removedCollections)) {
                $validCollections[] = $collection;
                $this->setCollectionData($asset, $configuration, $data, $meta);
            } elseif (!$ignoreRemove) {
                $this->removeCollectionData($asset, $configuration, $meta);
            }
        }

        $collections = Collections::getByAssetId($asset->getId());
        $allCollections = array_merge($collections->getCollections() ?: [], $validCollections);

        if (!$ignoreRemove) {
            $allCollections = array_filter($allCollections, function ($collection) use ($removedCollections) {
                return !in_array($collection, $removedCollections);
            });
        }

        $collections->setCollections(array_unique($allCollections));
        $collections->applyToAsset();
    }

    /**
     * @param Asset $asset
     * @param Configuration $configuration
     * @param array $data
     * @param array $meta
     */
    protected function setCollectionData(Asset $asset, Configuration $configuration, array $data, array $meta)
    {
        $fieldDefinitions = [];
        $localizedFieldDefinitions = [];

        Service::extractDataDefinitions($configuration->getLayoutDefinitions(), false, $fieldDefinitions, $localizedFieldDefinitions);

        $this->setMetadataValues($configuration, $asset, $fieldDefinitions, $localizedFieldDefinitions, $data, $meta);
    }

    /**
     * @param Asset $asset
     * @param Configuration $configuration
     * @param array $meta
     */
    protected function removeCollectionData(Asset $asset, Configuration $configuration, array $meta = [])
    {
        $fieldDefinitions = [];
        $localizedFieldDefinitions = [];

        Service::extractDataDefinitions($configuration->getLayoutDefinitions(), false, $fieldDefinitions, $localizedFieldDefinitions);

        // prepare a data set with null values for each configuration attribute
        $data = [];

        foreach ($fieldDefinitions as $definition) {
            $data[$definition->getName()] = null;
        }

        foreach ($localizedFieldDefinitions as $definition) {
            foreach (Tool::getValidLanguages() as $language) {
                $data[self::LOCALIZED_FIELDS][$definition->getName()][$language] = null;
            }
        }

        $this->setMetadataValues($configuration, $asset, $fieldDefinitions, $localizedFieldDefinitions, $data, $meta);
    }

    /**
     * @param Configuration $configuration
     * @param Asset $asset
     * @param array $fieldDefinitions
     * @param array $localizedFieldDefinitions
     * @param array $data
     * @param array $meta
     * @param bool $localized
     */
    protected function setMetadataValues(Configuration $configuration, Asset $asset, array $fieldDefinitions, array $localizedFieldDefinitions, array $data, array $meta, bool $localized = false)
    {
        $definitions = $localized ? $localizedFieldDefinitions : $fieldDefinitions;
        $languages = $localized ? Tool::getValidLanguages() : [null];
        $editableLanguages = $this->languagesService->getEditableLanguages();

        if (empty($data) || !is_array($data)) {
            return;
        }

        foreach ($data as $key => $value) {
            if ($key === self::LOCALIZED_FIELDS) {
                $this->setMetadataValues($configuration, $asset, $fieldDefinitions, $localizedFieldDefinitions, $value, $meta, true);
            } else {
                foreach ($languages as $language) {
                    $val = $localized ? $value[$language] : $value;

                    // no edit access
                    if ($language && $editableLanguages && !in_array($language, $editableLanguages)) {
                        continue;
                    }

                    $definition = $definitions[$key];

                    if (!$definition) {
                        continue;
                    }

                    $adapter = $this->restApiMetadataDefinitionService->getMetadataDefinitionAdapter($definition);
                    $val = $adapter->setDataFromDetail($asset, $val);

                    $attribute = $this->attributeService->getAttributeNameByDefinition($configuration, $definition);

                    // already metadata existing and user wants to keep it
                    if ($this->shouldKeepExistingValue($attribute, $meta) && $asset->getMetadata($attribute, $language)) {
                        continue;
                    }

                    $this->setRawMetadata($asset, $attribute, $definition->fieldtype, $val, $language);
                }
            }
        }
    }

    /**
     * @param $attribute
     * @param $meta
     *
     * @return bool
     */
    protected function shouldKeepExistingValue($attribute, $meta)
    {
        if (!$meta || !$meta['keep'] || !$meta['keep'][$attribute]) {
            return false;
        }

        return true;
    }

    /**
     * @param Asset $asset
     * @param string $attributeName
     * @param string|null $language
     *
     * @return string|null
     *
     * @throws \Exception
     */
    public function getNormalizedMetadataValue(Asset $asset, string $attributeName, ?string $language = null)
    {
        $collection = $this->attributeService->getCollectionFromAttributeName($attributeName);
        $attribute = $this->attributeService->getAttributeFromAttributeName($attributeName);

        $configuration = Configuration\Dao::getByName($collection);

        if (!$configuration) {
            return null;
        }

        $cacheKey = preg_replace('/[^a-zA-Z0-9]/', '_', "portal_engine_configuration_definitions_{$configuration->getPrefix()}");

        if (!Runtime::isRegistered($cacheKey)) {
            $fieldDefinitions = [];
            $localizedFieldDefinitions = [];

            Service::extractDataDefinitions($configuration->getLayoutDefinitions(), false, $fieldDefinitions, $localizedFieldDefinitions);

            Runtime::set($cacheKey, [$fieldDefinitions, $localizedFieldDefinitions]);
        } else {
            list($fieldDefinitions, $localizedFieldDefinitions) = Runtime::get($cacheKey);
        }

        $fieldDefinition = ($fieldDefinitions[$attribute] ?? null) ?: ($localizedFieldDefinitions[$attribute] ?? null);

        if (!$fieldDefinition) {
            return null;
        }

        $adapter = $this->restApiMetadataDefinitionService->getMetadataDefinitionAdapter($fieldDefinition);

        if (!$adapter) {
            return null;
        }

        $value = $this->getRawMetadata($asset, $attributeName, $language);

        return $adapter->getNormalizedData($asset, $value);
    }
}
