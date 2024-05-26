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

use Pimcore\Bundle\StatisticsExplorerBundle\Service\StatisticsService;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class DataSourcePass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $taggedServices = $container->findTaggedServiceIds('pimcore.statistics_explorer.data_source');

        $dataSources = [];
        if (count($taggedServices)) {
            foreach ($taggedServices as $id => $tags) {
                foreach ($tags as $attributes) {
                    $dataSources[$attributes['context']][$attributes['dataSourceName']] = new Reference($id);
                }
            }
        }

        $statisticsServiceDefinition = $container->getDefinition(StatisticsService::class);
        $statisticsServiceDefinition->setArgument('$dataSourceAdapters', $dataSources);
    }
}
