<?php

declare(strict_types = 1);

/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */

namespace Pimcore\AssetMetadataClassDefinitionsBundle\Loader;

use Pimcore\AssetMetadataClassDefinitionsBundle\Model\ClassDefinition\Data\Data;
use Pimcore\Loader\ImplementationLoader\LoaderInterface;
use Pimcore\Model\Asset\MetaData\ClassDefinition\Data\DataDefinitionInterface;

interface DataLoaderInterface extends LoaderInterface
{
    /**
     * Builds a classdefinition data instance
     *
     * @param string $name
     * @param array $params
     *
     * @return Data
     */
    public function build(string $name, array $params = []): DataDefinitionInterface;
}
