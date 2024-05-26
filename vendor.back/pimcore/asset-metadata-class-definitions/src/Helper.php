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
use Pimcore\AssetMetadataClassDefinitionsBundle\Model\Configuration\Dao;
use Pimcore\Model\Asset;
use Pimcore\Model\Element\ValidationException;

class Helper
{
    /**
     * @param string $name
     *
     * @return Data|null
     */
    public static function getFieldDefinition($name, &$configuration = null)
    {

        //TODO this should be cached

        $fieldDefinition = null;
        if (($idx = strpos($name, '.')) !== false) {
            $prefix = substr($name, 0, $idx);
            $key = substr($name, $idx + 1);

            $configuration = Dao::getByPrefix($prefix);

            if ($configuration) {
                $definitions = [];
                $localizedDefinitions = [];
                $layoutDefinitions = $configuration->getLayoutDefinitions();
                Service::extractDataDefinitions($layoutDefinitions, false, $definitions, $localizedDefinitions);
            }

            $fieldDefinition = $definitions[$key] ?? null;
            if (!$fieldDefinition) {
                $fieldDefinition = $localizedDefinitions[$key] ?? null;
            }
        }

        return $fieldDefinition;
    }

    /**
     * @param string $name
     * @param $language
     * @param mixed $data
     * @param Data $definition
     * @param Asset $asset
     *
     * @throws ValidationException
     */
    public static function performMandatoryCheck(string $name, $language, $data, Data $definition, Asset $asset)
    {
        if ($definition->isMandatory()) {
            if ($definition->isEmpty($data, [
                'name' => $name,
                'language' => $language,
                'asset' => $asset
            ])) {
                $msg = 'Empty mandatory metadata ' . $name;
                if ($language) {
                    $msg .= '[' . $language . ']';
                }
                throw new ValidationException($msg);
            }
        }
    }

    /**
     * @param string $name
     * @param $language
     * @param mixed $data
     * @param Data $definition
     * @param Asset $asset
     *
     * @throws ValidationException
     */
    public static function performValidityCheck(string $name, $language, $data, Data $definition, Asset $asset)
    {
        if ($definition->checkValidity($data, [
            'name' => $name,
            'language' => $language,
            'asset' => $asset
        ])) {
            $msg = 'Validation failed ' . $name;
            if ($language) {
                $msg .= '[' . $language . ']';
            }
            throw new ValidationException($msg);
        }
    }
}
