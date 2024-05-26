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

namespace Pimcore\AssetMetadataClassDefinitionsBundle\Model\ClassDefinition\Helper;

use Pimcore\AssetMetadataClassDefinitionsBundle\Model\ClassDefinition\CalculatorClassInterface;
use Pimcore\Model\DataObject\ClassDefinition\Helper\ClassResolver;

/**
 * @internal
 */
final class CalculatorClassResolver extends ClassResolver
{
    public static function resolveCalculatorClass($calculatorClass)
    {
        return self::resolve($calculatorClass, static function ($generator) {
            return $generator instanceof CalculatorClassInterface;
        });
    }
}
