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

namespace Pimcore\Bundle\StatisticsExplorerBundle\DependencyInjection\Compiler;

use Pimcore\Bundle\StatisticsExplorerBundle\Service\UserProviderLocatorService;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class UserProviderContextPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $taggedServices = $container->findTaggedServiceIds('pimcore.statistics_explorer.user_provider_context');

        $userProviders = [];
        if (count($taggedServices)) {
            foreach ($taggedServices as $id => $tags) {
                foreach ($tags as $attributes) {
                    $userProviders[$attributes['context']] = new Reference($id);
                }
            }
        }

        $userProviderLocatorServiceDefinition = $container->getDefinition(UserProviderLocatorService::class);
        $userProviderLocatorServiceDefinition->setArgument('$userProviderMap', $userProviders);
    }
}
