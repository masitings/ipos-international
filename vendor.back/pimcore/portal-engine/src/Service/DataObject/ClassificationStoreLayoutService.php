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

use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Classificationstore\KeyConfig;

class ClassificationStoreLayoutService
{
    protected $usedKeyConfigs = [];

    /**
     * @param string $className
     * @param KeyConfig|null $keyConfig
     */
    public function addUsedKeyConfig(string $className, ?KeyConfig $keyConfig)
    {
        if (!$keyConfig) {
            return;
        }

        if (!array_key_exists($className, $this->usedKeyConfigs)) {
            $this->usedKeyConfigs[$className] = [];
        }

        $this->usedKeyConfigs[$className][$keyConfig->getId()] = json_decode($keyConfig->getDefinition(), true);
    }

    /**
     * @param ClassDefinition $classDefinition
     *
     * @return array
     */
    public function getUsedKeyDefinitionsForClassDefinition(ClassDefinition $classDefinition)
    {
        if (!array_key_exists($classDefinition->getName(), $this->usedKeyConfigs)) {
            return [];
        }

        return $this->usedKeyConfigs[$classDefinition->getName()];
    }
}
