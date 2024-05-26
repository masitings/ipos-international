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

namespace Pimcore\Bundle\PortalEngineBundle\DependencyInjection\Compiler;

use Pimcore\Bundle\PortalEngineBundle\Enum\DependencyInjection\CompilerPassTag;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class DataPoolPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $taggedServices = $container->findTaggedServiceIds(CompilerPassTag::DATA_POOL_SERVICE);

        $arguments = [];
        if (sizeof($taggedServices)) {
            foreach ($taggedServices as $id => $tags) {
                foreach ($tags as $attributes) {
                    $arguments[$attributes['type']] = new Reference($id);
                }
            }
        }

        $serviceLocator = $container->getDefinition('pimcore.portal_engine.data-pool-service-locator');
        $serviceLocator->setArgument(0, $arguments);
    }
}
