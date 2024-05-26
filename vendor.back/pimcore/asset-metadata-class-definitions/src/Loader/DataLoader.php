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

use Pimcore\Loader\ImplementationLoader\ImplementationLoader;
use Pimcore\Model\Asset\MetaData\ClassDefinition\Data\DataDefinitionInterface;

class DataLoader extends ImplementationLoader implements DataLoaderInterface
{
    /**
     * @inheritDoc
     */
    public function build(string $name, array $params = []): DataDefinitionInterface
    {
        return parent::build($name, $params);
    }
}
