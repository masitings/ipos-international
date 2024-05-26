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

namespace Pimcore\AssetMetadataClassDefinitionsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('pimcore_asset_metadata_class_definitions');
        $rootNode = $treeBuilder->getRootNode();

        //TODO not needed anymore, currently we don't need any configuration options

        $rootNode
            ->children()
                ->scalarNode('show_grid')->info('show built-in grid')->defaultValue(true)->end()
                ->scalarNode('show_gridicon')->info('show icon of built-in grid')->defaultValue(true)->end()
                ->arrayNode('class_definitions')
                    ->children()
                        ->arrayNode('layout')
                            ->children()
                                ->arrayNode('map')
                                    ->useAttributeAsKey('name')
                                    ->prototype('scalar')->end()
                                ->end()
                                ->arrayNode('prefixes')
                                    ->prototype('scalar')->end()
                                ->end()
                            ->end()
                        ->end()

                        ->arrayNode('data')
                            ->children()
                                ->arrayNode('map')
                                    ->useAttributeAsKey('name')
                                    ->prototype('scalar')->end()
                                ->end()
                                ->arrayNode('prefixes')
                                    ->prototype('scalar')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
