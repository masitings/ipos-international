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

namespace Pimcore\AssetMetadataClassDefinitionsBundle\EventListener;

use Pimcore\AssetMetadataClassDefinitionsBundle\Helper;
use Pimcore\AssetMetadataClassDefinitionsBundle\Model\ClassDefinition\Data\CalculatedValue;
use Pimcore\AssetMetadataClassDefinitionsBundle\Model\ClassDefinition\Data\Data;
use Pimcore\AssetMetadataClassDefinitionsBundle\Model\ClassDefinition\Helper\CalculatorClassResolver;
use Pimcore\AssetMetadataClassDefinitionsBundle\Model\Configuration;
use Pimcore\AssetMetadataClassDefinitionsBundle\Service;
use Pimcore\Event\AdminEvents;
use Pimcore\Event\AssetEvents;
use Pimcore\Event\Model\AssetEvent;
use Pimcore\Logger;
use Pimcore\Model\Asset;
use Pimcore\Model\Metadata\Predefined;
use Pimcore\Tool;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class MainListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            AdminEvents::ASSET_GET_PRE_SEND_DATA => 'getPreSendData',
            AdminEvents::ASSET_LIST_BEFORE_BATCH_UPDATE => 'assetListBeforeBatchUpdate',
            AdminEvents::ASSET_LIST_BEFORE_UPDATE => 'assetListBeforeUpdate',
            AdminEvents::ASSET_GET_FIELD_GRID_CONFIG => 'assetListGetFieldGridConfig',
            AdminEvents::ASSET_METADATA_PRE_SET => 'assetMetadataPreSet',
            AssetEvents::PRE_GET_METADATA => 'preGetMetadata',
            AssetEvents::PRE_UPDATE => 'preUpdate'
        ];
    }

    public function preUpdate(AssetEvent $event)
    {
        $asset = $event->getAsset();

        $collectionNames = $asset->getCustomSetting('plugin_assetmetdata_collections');

        foreach ($collectionNames ?? [] as $name) {
            $configuration = Configuration\Dao::getByName($name);
            $layoutDefinitions = $configuration->getLayoutDefinitions();
            $definitions = [];
            $localizedDefinitions = [];
            Service::extractDataDefinitions($layoutDefinitions, false, $definitions, $localizedDefinitions);

            foreach ($definitions as $name => $definition) {
                $key = $configuration->getPrefix() . '.' . $name;
                $data = $asset->getMetadata($key, '', false, true);
                $data = $data['data'] ?? null;
                if ($definition->isMandatory()) {
                    Helper::performMandatoryCheck($key, '', $data, $definition, $asset);
                }
                Helper::performValidityCheck($key, '', $data, $definition, $asset);
            }

            foreach ($localizedDefinitions as $name => $definition) {
                $key = $configuration->getPrefix() . '.' . $name;
                $validLanguages = Tool::getValidLanguages();
                foreach ($validLanguages as $language) {
                    $data = $asset->getMetadata($key, $language);
                    if ($definition->isMandatory()) {
                        Helper::performMandatoryCheck($key, $language, $data, $definition, $asset);
                    }
                    Helper::performValidityCheck($key, $language, $data, $definition, $asset);
                }
            }
        }
    }

    public function preGetMetadata(AssetEvent $event)
    {
        $asset = $event->getAsset();
        $assetId = $asset->getId();
        $metadata = $event->getArgument('metadata');
        $metadata = $metadata ?? [];

        $collectionNames = $asset->getCustomSetting('plugin_assetmetdata_collections') ?? [];

        foreach ($collectionNames as $colName) {
            $configuration = Configuration\Dao::getByName($colName);

            $definitions = [];
            $localizedDefinitions = [];
            $layoutDefinitions = $configuration->getLayoutDefinitions();
            Service::extractDataDefinitions($layoutDefinitions, false, $definitions, $localizedDefinitions);
            /** @var Data $fd */
            foreach ($definitions as $fd) {
                if ($fd instanceof CalculatedValue) {
                    $this->evaluateField($asset, $configuration, $fd, '', $metadata);
                }
            }

            foreach ($localizedDefinitions as $fd) {
                if ($fd instanceof CalculatedValue) {
                    $validLanguages = Tool::getValidLanguages();
                    foreach ($validLanguages as $language) {
                        $this->evaluateField($asset, $configuration, $fd, $language, $metadata);
                    }
                }
            }
        }

        $event->setArgument('metadata', $metadata);
    }

    public function assetMetadataPreSet(GenericEvent $event)
    {
        $assetId = $event->getArgument('id');
        $metadata = $event->getArgument('metadata');
        $collectionsData = $metadata['collections'] ?? [];

        $asset = Asset::getById($assetId);
        $asset->setCustomSetting('plugin_assetmetdata_collections', $collectionsData);
    }

    public function assetListGetFieldGridConfig(GenericEvent $event)
    {
        $keyPrefix = $event->getArgument('keyPrefix');
        $field = $event->getArgument('field');
        $language = $event->getArgument('language');

        $defaulMetadataFields = ['copyright', 'alt', 'title'];
        $predefined = null;

        if (isset($field['fieldConfig']['layout']['name'])) {
            $predefined = Predefined::getByName($field['fieldConfig']['layout']['name']);
        }

        $key = $field['name'];
        if ($keyPrefix) {
            $key = $keyPrefix . $key;
        }

        $fieldDef = explode('~', $field['name']);
        $field['name'] = $fieldDef[0];

        if (isset($fieldDef[1]) && $fieldDef[1] === 'system') {
            $type = 'system';
        } elseif (in_array($fieldDef[0], $defaulMetadataFields)) {
            $type = 'input';
        } else {
            //check if predefined metadata exists, otherwise ignore
//                    if (empty($predefined) || ($predefined->getType() != $field['fieldConfig']['type'])) {
//                        return null;
//                    }
            $type = $field['fieldConfig']['type'];
            if (isset($fieldDef[1])) {
                $field['fieldConfig']['label'] = $field['fieldConfig']['layout']['title'] = $fieldDef[0] . ' (' . $fieldDef[1] . ')';
                $field['fieldConfig']['layout']['icon'] = Tool::getLanguageFlagFile($fieldDef[1], true);
            }
        }

        $result = [
            'key' => $key,
            'type' => $type,
            'label' => $field['fieldConfig']['label'] ?? $key,
            'width' => $field['width'],
            'position' => $field['position'],
            'language' => $field['fieldConfig']['language'] ?? null,
            'layout' => $field['fieldConfig']['layout'] ?? null,
        ];

        if (isset($field['locked'])) {
            $result['locked'] = $field['locked'];
        }

        if ($type === 'select') {
            $field['fieldConfig']['layout']['config'] = $predefined->getConfig();
            $result['layout'] = $field['fieldConfig']['layout'];
        } elseif ($type === 'document' || $type === 'asset' || $type === 'object') {
            $result['layout']['fieldtype'] = 'manyToOneRelation';
            $result['layout']['subtype'] = $type;
        }

        $event->setArgument('processed', true);
        $event->setArgument('result', $result);
    }

    public function assetListBeforeUpdate(GenericEvent $event)
    {
        $loader = \Pimcore::getContainer()->get('pimcore_asset_metadata_classdefinitions.implementation_loader.data');

        $data = $event->getArgument('data');
        $assetId = $data['id'];
        $asset = Asset::getById($data['id']);

        $collectionNames = $asset->getCustomSetting('plugin_assetmetdata_collections');

        $collectionChanged = false;

        $metadata = $asset->getMetadata(null, null, false, true);
        $dirty = false;

        unset($data['id']);
        foreach ($data as $key => $value) {
            $fieldDef = explode('~', $key);
            $language = null;
            $key = $fieldDef[0];
            if (isset($fieldDef[1])) {
                $language = ($fieldDef[1] == 'none' ? '' : $fieldDef[1]);
            }

            foreach ($metadata as $idx => &$em) {
                if ($em['name'] == $key && $em['language'] == $language) {
                    $dataImpl = $loader->build($em['type']);
                    $value = $dataImpl->getDataFromListfolderGrid($value, $em);
                    $em['data'] = $value;
                    $dirty = true;
                    break;
                }
            }

            if (!$dirty) {
                $configuration = null;
                $fieldDefinition = Helper::getFieldDefinition($key, $configuration);
                if ($fieldDefinition) {
                    $dataImpl = $loader->build($fieldDefinition->getFieldtype());
                    $newData = [
                        'name' => $key,
                        'language' => $language,
                        'type' => $fieldDefinition->getFieldtype(),
                        'data' => $value
                    ];
                    $newData['data'] = $dataImpl->getDataFromListfolderGrid($value, $newData);
                    $metadata[] = $newData;
                    $dirty = true;

                    $configurationName = $configuration->getName();
                    if (!in_array($configuration, $collectionNames)) {
                        $collectionNames[] = $configurationName;
                        $collectionChanged = true;
                    }
                } else {
                    $defaulMetadata = ['title', 'alt', 'copyright'];
                    if (in_array($key, $defaulMetadata)) {
                        $metadata[] = [
                            'name' => $key,
                            'language' => $language,
                            'type' => 'input',
                            'data' => $value
                        ];
                        $dirty = true;
                    } else {
                        $predefined = Predefined::getByName($key);
                        if ($predefined && (empty($predefined->getTargetSubtype())
                                || $predefined->getTargetSubtype() == $asset->getType())) {
                            $metadata[] = [
                                'name' => $key,
                                'language' => $language,
                                'type' => $predefined->getType(),
                                'data' => $value
                            ];
                            $dirty = true;
                        }
                    }
                }
            }
        }

        if ($dirty || $collectionChanged) {
            $asset->setCustomSetting('plugin_assetmetdata_collections', $collectionNames);
            $asset->setMetadataRaw($metadata);
            $asset->save();
        }

        $event->setArgument('processed', true);
    }

    public function getPreSendData(GenericEvent $event)
    {
        $config = \Pimcore::getContainer()->getParameter('pimcore_asset_metadata_classdefinitions');
        $assetData = $event->getArgument('data');
        $assetData['asset_metadata_class_definitions_bundle_showgrid'] = $config['show_grid'] ?? false;
        $assetData['asset_metadata_class_definitions_bundle_showgridicon'] = $config['show_gridicon'] ?? false;

        if (is_array($assetData['metadata'])) {
            foreach ($assetData['metadata'] as &$item) {
                $type = $item['type'];
                $loader = \Pimcore::getContainer()->get('pimcore_asset_metadata_classdefinitions.implementation_loader.data');
                $dataImpl = $loader->build($type);
                $dataImpl->addGridConfig($item);
            }
        }

        $assetId = $assetData['id'];
        $asset = Asset::getById($assetId);
        $collectionNames = $asset->getCustomSetting('plugin_assetmetdata_collections') ?? [];

        $assetData['asset_metadata_class_definitions_bundle_activeDefinitions'] = array_values($collectionNames);
        $event->setArgument('data', $assetData);
    }

    public function assetListBeforeBatchUpdate(GenericEvent $event)
    {
        $loader = \Pimcore::getContainer()->get('pimcore_asset_metadata_classdefinitions.implementation_loader.data');

        $data = $event->getArgument('data');
        $assetId = $data['job'];
        $asset = Asset::getById($assetId);

        $collectionNames = $asset->getCustomSetting('plugin_assetmetdata_collections');

        $collectionChanged = false;

        $language = null;
        if (isset($data['language'])) {
            $language = $data['language'] != 'default' ? $data['language'] : null;
        }

        if ($asset) {
            if (!$asset->isAllowed('publish')) {
                throw new \Exception("Permission denied. You don't have the rights to save this asset.");
            }

            $metadata = $asset->getMetadata(null, null, false, true);
            $dirty = false;

            $name = $data['name'];
            $value = $data['value'];

            if ($data['valueType'] == 'object') {
                $value = json_decode($value, true);
            }

            $fieldDef = explode('~', $name);
            $name = $fieldDef[0];
            if (count($fieldDef) > 1) {
                $language = ($fieldDef[1] == 'none' ? '' : $fieldDef[1]);
            }

            foreach ($metadata as $idx => &$em) {
                if ($em['name'] == $name && $em['language'] == $language) {
                    $dataImpl = $loader->build($em['type']);
                    $value = $dataImpl->getDataFromListfolderGrid($value, $em);

                    $em['data'] = $value;
                    $dirty = true;
                    break;
                }
            }

            if (!$dirty) {
                $configuration = null;
                $fieldDefinition = Helper::getFieldDefinition($name, $configuration);
                if ($fieldDefinition) {
                    $dataImpl = $loader->build($fieldDefinition->getFieldtype());
                    $newData = [
                        'name' => $name,
                        'language' => $language,
                        'type' => $fieldDefinition->getFieldtype(),
                        'data' => $value
                    ];
                    $newData['data'] = $dataImpl->getDataFromListfolderGrid($value, $newData);
                    $metadata[] = $newData;
                    $dirty = true;

                    $configurationName = $configuration->getName();
                    if (!in_array($configuration, $collectionNames)) {
                        $collectionNames[] = $configurationName;
                        $collectionChanged = true;
                    }
                } else {
                    $defaulMetadata = ['title', 'alt', 'copyright'];
                    if (in_array($name, $defaulMetadata)) {
                        $metadata[] = [
                            'name' => $name,
                            'language' => $language,
                            'type' => 'input',
                            'data' => $value,
                        ];
                        $dirty = true;
                    } else {
                        $predefined = Predefined::getByName($name);
                        if ($predefined && (empty($predefined->getTargetSubtype())
                                || $predefined->getTargetSubtype() == $asset->getType())) {
                            $metadata[] = [
                                'name' => $name,
                                'language' => $language,
                                'type' => $predefined->getType(),
                                'data' => $value,
                            ];
                            $dirty = true;
                        }
                    }
                }
            }

            if ($dirty || $collectionChanged) {
                $asset->setCustomSetting('plugin_assetmetdata_collections', $collectionNames);
                $asset->setMetadataRaw($metadata);
                $asset->save();
            }
        }

        $event->setArgument('processed', true);
    }

    public function evaluateField(Asset $asset, Configuration $configuration, Data $fd, $language, &$metadata)
    {
        $className = $fd->getCalculatorClass();
        $calculator = CalculatorClassResolver::resolveCalculatorClass($className);
        if (!$className || $calculator === null) {
            Logger::error('Class does not exist: ' . $className);

            return;
        }
        $params = [
            'name' => $configuration->getPrefix() . '.' . $fd->getName(),
            'configuration' => $configuration,
            'fieldDefinition' => $fd
        ];
        $result = call_user_func([$calculator, 'compute'], $asset, $params);

        $key = $configuration->getPrefix() . '.' . $fd->getName();
        $found = false;
        foreach ($metadata as &$item) {
            if ($item['name'] == $key && $item['language'] == $language) {
                $item['data'] = $result;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $metadata[] = [
                'name' => $key,
                'type' => $fd->getFieldtype(),
                'data' => $result,
                'language' => $language
            ];
        }
    }
}
