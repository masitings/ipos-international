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

use Pimcore\Bundle\PortalEngineBundle\Service\Asset\DirectEditConnector;
use Pimcore\Bundle\PortalEngineBundle\Service\Asset\DirectEditConnectorInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class DirectEditCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');

        // direct edit bundle needs to be active and installed
        if (
            !class_exists('\\Pimcore\\Bundle\\DirectEditBundle\\PimcoreDirectEditBundle') ||
            !array_key_exists('PimcoreDirectEditBundle', $bundles)
        ) {
            return;
        }

        // if the bundle is active, override the dummy connector with the actual implementation
        $definition = new Definition(DirectEditConnector::class);
        $definition->setAutowired(true);
        $definition->setPublic(false);
        $definition->setLazy(true);

        $container->setDefinition(DirectEditConnectorInterface::class, $definition);
    }
}
