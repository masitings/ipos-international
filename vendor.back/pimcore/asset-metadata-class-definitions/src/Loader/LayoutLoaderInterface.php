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

use Pimcore\AssetMetadataClassDefinitionsBundle\Model\ClassDefinition\Layout\Layout;
use Pimcore\Loader\ImplementationLoader\LoaderInterface;

interface LayoutLoaderInterface extends LoaderInterface
{
    /**
     * Builds a classdefinition layout instance
     *
     * @param string $name
     * @param array $params
     *
     * @return Layout
     */
    public function build(string $name, array $params = []);
}
