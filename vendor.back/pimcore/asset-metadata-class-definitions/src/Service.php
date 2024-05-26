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

namespace Pimcore\AssetMetadataClassDefinitionsBundle;

use Pimcore\AssetMetadataClassDefinitionsBundle\Model\ClassDefinition\Data\Data;
use Pimcore\AssetMetadataClassDefinitionsBundle\Model\ClassDefinition\Data\Localizedfields;
use Pimcore\AssetMetadataClassDefinitionsBundle\Model\ClassDefinition\Layout\Layout;

class Service
{
    /**
     * @param array $array
     * @param bool $throwException
     *
     * @return Data|Layout|false
     *
     * @throws \Exception
     */
    public static function generateLayoutTreeFromArray($array, $throwException = false)
    {
        if (is_array($array) && count($array) > 0) {
            /** @var LoaderInterface $loader */
            $loader = \Pimcore::getContainer()->get('pimcore_asset_metadata_classdefinitions.implementation_loader.' . $array['datatype']);

            if ($loader->supports($array['fieldtype'])) {
                /** @var Data|Layout $item */
                $item = $loader->build($array['fieldtype']);

                if (method_exists($item, 'addChild')) { // allows childs
                    $item->setValues($array, ['childs']);
                    $childs = $array['childs'] ?? [];

                    if (!empty($childs['datatype'])) {
                        $childO = self::generateLayoutTreeFromArray($childs, $throwException);
                        $item->addChild($childO);
                    } elseif (is_array($childs) && count($childs) > 0) {
                        foreach ($childs as $child) {
                            $childO = self::generateLayoutTreeFromArray($child, $throwException);
                            if ($childO !== false) {
                                $item->addChild($childO);
                            } else {
                                if ($throwException) {
                                    throw new \Exception('Could not add child ' . var_export($child, true));
                                }

                                Logger::err('Could not add child ' . var_export($child, true));

                                return false;
                            }
                        }
                    }
                } else {
                    $item->setValues($array);
                }

                return $item;
            }
        }
        if ($throwException) {
            throw new \Exception('Could not add child ' . var_export($array, true));
        }

        return false;
    }

    public static function enrichDefinitions($definitions)
    {
        /** @var Data $definition */
        foreach ($definitions as $definition) {
            $definition->enrichDefinition();
        }
    }

    /**
     * @param array $item
     * @param Data $definition
     */
    public static function enrichListfolderDefinition(&$item, $definition)
    {
        $definition->addListFolderConfig($item);
    }

    public static function getFieldDefinitions($configuration)
    {
        //TODO cache the list
        $definitions = [];
        $localizedDefinitions = [];
        $layoutDefinitions = $configuration->getLayoutDefinitions();
        self::extractDataDefinitions($layoutDefinitions, false, $definitions, $localizedDefinitions);
        $fieldDefinitions = array_merge($definitions, $localizedDefinitions);

        return $fieldDefinitions;
    }

    /**
     * @param mixed $item
     * @param bool $insideLocalizedField
     * @param array $definitions
     * @param array $localizedDefinitions
     */
    public static function extractDataDefinitions($item, $insideLocalizedField, &$definitions = [], &$localizedDefinitions = [])
    {
        if (is_object($item) && method_exists($item, 'hasChildren')) {
            $insideLocalizedField = $item instanceof Localizedfields;
            if ($item->hasChildren()) {
                foreach ($item->getChildren() as $child) {
                    self::extractDataDefinitions($child, $insideLocalizedField, $definitions, $localizedDefinitions);
                }
            }
        }

        if ($item instanceof Data && !$item instanceof Localizedfields) {
            if ($insideLocalizedField) {
                $localizedDefinitions[$item->getName()] = $item;
            } else {
                $definitions[$item->getName()] = $item;
            }
        }
    }
}
