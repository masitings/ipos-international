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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

trait TaggedAwareCompilerPass
{
    /**
     * @param ContainerBuilder $container
     * @param string $tag
     * @param string $service
     * @param string $method
     */
    protected function applyTaggedServices(ContainerBuilder $container, string $tag, string $service, string $method)
    {
        if (!$container->hasDefinition($service)) {
            return;
        }

        $taggedServices = $container->findTaggedServiceIds($tag);

        if (!empty($taggedServices)) {
            $definition = $container->getDefinition($service);

            foreach ($taggedServices as $id => $tags) {
                $definition->addMethodCall($method, [new Reference($id)]);
            }
        }
    }
}
