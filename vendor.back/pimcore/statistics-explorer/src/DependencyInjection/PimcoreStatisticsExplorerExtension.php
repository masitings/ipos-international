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

namespace Pimcore\Bundle\StatisticsExplorerBundle\DependencyInjection;

use Pimcore\Bundle\StatisticsExplorerBundle\Service\ConfigurationLoaderService;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class PimcoreStatisticsExplorerExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('pimcore.statistics_explorer.es_hosts', $config['es_hosts']);
        $container->setParameter('pimcore.statistics_explorer.global_configurations', $config['global_configurations']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $configurationLoaderServiceDefinition = $container->getDefinition(ConfigurationLoaderService::class);
        $configurationLoaderServiceDefinition->setArgument('$globalConfigurationsConfig', $config['global_configurations']);
    }

    /**
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        $builds = [
            'pimcoreStatisticsExplorer' => realpath(__DIR__ . '/../Resources/public/build'),
        ];

        $container->prependExtensionConfig('webpack_encore', [
            // 'output_path' => realpath(__DIR__ . '/../Resources/public/build')
            'output_path' => false,
            'builds' => $builds
        ]);

        if ($container->hasExtension('doctrine_migrations')) {
            $loader = new Loader\YamlFileLoader(
                $container,
                new FileLocator(__DIR__ . '/../Resources/config')
            );

            $loader->load('doctrine_migrations.yml');
        }
    }
}
