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

namespace Pimcore\Bundle\PortalEngineBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\VariableNodeDefinition;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('pimcore_portal_engine');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('customized_frontend_builds')
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('wizard')
                    ->children()
                        ->arrayNode('possible_portal_domains')
                            ->defaultValue([])
                            ->info('Optionally configure possible domains for portals. Will be selectable in portal wizard.')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('frontend')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('geo_tile_layer_url')->defaultValue('https://a.tile.openstreetmap.org/{z}/{x}/{y}.png')->end()
                        ->scalarNode('geo_copyright')->defaultValue('&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors')->end()
                    ->end()
                ->end()
                ->arrayNode('batch_task_queue')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('cleanup')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->integerNode('cleanup_uncompleted_tasks_after_hours')->defaultValue(24)->end()
                                ->integerNode('cleanup_finished_tasks_after_hours')->defaultValue(72)->end()
                            ->end()
                        ->end()
                        ->arrayNode('download')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->integerNode('batch_size')->defaultValue(500)->end()
                            ->end()
                        ->end()
                        ->arrayNode('update_asset_metadata')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->integerNode('batch_size')->defaultValue(500)->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('data_pool')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('select_all_max_size')->defaultValue(500)->end()
                    ->end()
                ->end()
                ->arrayNode('download')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('zip_warning_size')->defaultValue(2000000000)->end() //2GB
                        ->integerNode('zip_reject_size')->defaultValue(10000000000)->end() //10GB
                    ->end()
                ->end()
                ->arrayNode('core_fields_configuration')
                    ->children()
                        ->append($this->buildCoreFieldsConfigurationArrayNode('general'))
                        ->append($this->buildCoreFieldsConfigurationArrayNode('data_object'))
                        ->append($this->buildCoreFieldsConfigurationArrayNode('asset'))
                    ->end()
                ->end()
                ->arrayNode('login')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('fields')
                            ->defaultValue(['email'])
                            ->scalarPrototype()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('index_service')
                    ->children()
                        ->arrayNode('es_client_params')
                            ->children()
                                ->scalarNode('host')
                                    ->defaultValue('%elasticsearch.host%')
                                ->end()
                                ->scalarNode('index_prefix')
                                    ->defaultValue('pimcoreportalengine_')
                                ->end()
                                ->variableNode('connection_params')
                                    ->defaultValue([])
                                    ->treatNullLike([])
                                    ->beforeNormalization()
                                        ->castToArray()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('search_settings')
                            ->children()
                                ->scalarNode('list_page_size')
                                    ->defaultValue(60)
                                ->end()
                                ->scalarNode('list_max_filter_options')
                                    ->defaultValue(500)
                                ->end()
                                ->scalarNode('max_synchronous_children_rename_limit')
                                    ->defaultValue(500)
                                    ->info('Maximum number of direct/synchronous children path updates if asset folders get renamed. If more then the given number of children need an path update the process will be done by the asynchronous index update command. This mechanismn is needed to be able to see directly the new paths in the folder navigation.')
                                ->end()
                                ->arrayNode('search_analyzer_attributes')
                                    ->useAttributeAsKey('type')
                                        ->prototype('scalar')
                                    ->end()
                                    ->arrayPrototype()
                                        ->children()
                                            ->append($this->buildVariableNode('fields'))
                                        ->end()
                                    ->end()
                                ->end()
//                                ->append($this->buildVariableNode('search_analyzer_attributes'))
                            ->end()
                        ->end()
                        ->append($this->buildVariableNode('index_settings'))
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }

    /**
     * @param string $name
     * @param array $defaultValue
     * @param string|null $documentation
     *
     * @return NodeDefinition
     */
    private function buildVariableNode(string $name, array $defaultValue = [], string $documentation = null): NodeDefinition
    {
        $node = new VariableNodeDefinition($name);
        if ($documentation) {
            $node->info($documentation);
        }

        $node
            ->defaultValue($defaultValue)
            ->treatNullLike([])
            ->beforeNormalization()
            ->castToArray()
            ->end();

        return $node;
    }

    /**
     * @param string $name
     *
     * @return ArrayNodeDefinition
     */
    private function buildCoreFieldsConfigurationArrayNode(string $name): ArrayNodeDefinition
    {
        $node = new ArrayNodeDefinition($name);
        $node
            ->useAttributeAsKey('field')
                ->prototype('scalar')
            ->end()
            ->arrayPrototype()
                ->children()
                    ->scalarNode('type')
                        ->isRequired()
                    ->end()
                    ->scalarNode('analyzer')
                    ->end()
                    ->scalarNode('title')
                        ->isRequired()
                    ->end()
                    ->scalarNode('fieldDefinition')->end()
                    ->arrayNode('values')
                        ->children()
                            ->arrayNode('options')
                                ->arrayPrototype()
                                    ->children()
                                        ->scalarNode('key')
                                            ->isRequired()
                                        ->end()
                                        ->scalarNode('value')
                                            ->isRequired()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->append($this->buildVariableNode('properties'))
                    ->append($this->buildVariableNode('fields'))
                ->end()
            ->end();

        return $node;
    }
}
