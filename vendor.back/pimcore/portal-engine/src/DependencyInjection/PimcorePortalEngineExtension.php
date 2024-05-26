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

use Pimcore\Bundle\PortalEngineBundle\Enum\DependencyInjection\ContainerParameter;
use Pimcore\Bundle\PortalEngineBundle\EventSubscriber\AdminSettingsSubscriber;
use Pimcore\Bundle\PortalEngineBundle\Form\LoginForm;
use Pimcore\Bundle\PortalEngineBundle\MessageHandler\BatchTask\Download;
use Pimcore\Bundle\PortalEngineBundle\MessageHandler\BatchTask\UpdateAssetMetadata;
use Pimcore\Bundle\PortalEngineBundle\Service\BatchTask\BatchTaskService;
use Pimcore\Bundle\PortalEngineBundle\Service\Download\SizeEstimation\AsyncSizeEstimationService;
use Pimcore\Bundle\PortalEngineBundle\Service\Frontend\FrontendConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\PortalConfig\FrontendBuildService;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\ElasticSearchConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\Authentication\UserProvider;
use Pimcore\Bundle\PortalEngineBundle\Twig\WebpackExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class PimcorePortalEngineExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $this
            ->registerCustomizedFrontendBuilds($container, $config['customized_frontend_builds'])
            ->registerFrontendParams($container, $config['frontend'])
            ->registerPossiblePortalDomains($container, $config['wizard']['possible_portal_domains'] ?? [])
            ->registerElasticSearchClientParams($container, $config['index_service'])
            ->registerCoreFields($container, $config['core_fields_configuration'])
            ->registerBatchTaskQueue($container, $config['batch_task_queue'])
            ->registerDataPool($container, $config['data_pool'])
            ->registerDownload($container, $config['download'])
            ->registerLogin($container, $config['login']);
    }

    /**
     * @param ContainerBuilder $container
     * @param array $customizedFrontendBuilds
     *
     * @return $this
     */
    protected function registerCustomizedFrontendBuilds(ContainerBuilder $container, array $customizedFrontendBuilds)
    {
        $definition = $container->getDefinition(FrontendBuildService::class);
        $definition->setArgument('$customizedFrontendBuilds', $customizedFrontendBuilds);

        return $this;
    }

    protected function registerPossiblePortalDomains(ContainerBuilder $container, array $possiblePortalDomains)
    {
        $definition = $container->getDefinition(AdminSettingsSubscriber::class);
        $definition->setArgument('$possiblePortalDomains', $possiblePortalDomains);

        return $this;
    }

    protected function registerFrontendParams(ContainerBuilder $container, array $config)
    {
        $definition = $container->getDefinition(FrontendConfigService::class);
        $definition->addMethodCall('setConfig', ['geo.tileLayerUrl', $config['geo_tile_layer_url']]);
        $definition->addMethodCall('setConfig', ['geo.copyright', $config['geo_copyright']]);

        return $this;
    }

    /**
     * @param ContainerBuilder $container
     * @param array $esClientParams
     *
     * @return $this
     */
    protected function registerElasticSearchClientParams(ContainerBuilder $container, array $esClientParams)
    {
        $definition = $container->getDefinition(ElasticSearchConfigService::class);
        $definition->setArgument('$host', $esClientParams['es_client_params']['host']);
        $definition->setArgument('$index_prefix', $esClientParams['es_client_params']['index_prefix']);
        $definition->setArgument('$indexSettings', $esClientParams['index_settings']);
        $definition->setArgument('$searchSettings', $esClientParams['search_settings']);
        $definition->setArgument('$connectionParams', $esClientParams['es_client_params']['connection_params']);

        $container->setParameter('portal-engine.elasticsearch.index-prefix', $esClientParams['es_client_params']['index_prefix']);

        return $this;
    }

    /**
     * @param ContainerBuilder $container
     * @param array $coreFields
     *
     * @return $this
     */
    protected function registerCoreFields(ContainerBuilder $container, array $coreFields)
    {
        $container->setParameter(
            'pimcore_portal_engine.core_fields_configuration',
            $coreFields
        );

        return $this;
    }

    /**
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        $portals = [];
        $portalsJsonFile = FrontendBuildService::APP_FRONTEND_PORTALS_JSON;
        if (file_exists($portalsJsonFile)) {
            $portals = json_decode(file_get_contents($portalsJsonFile), true);
        }

        $builds = [
            'portalEngineBundle' => realpath(__DIR__ . '/../Resources/public/build/bundle'),
        ];

        foreach ($portals as $portal) {
            $portalId = $portal['id'];
            $builds[WebpackExtension::BUILD_PORTAL_CONFIG . '_' . $portalId] = FrontendBuildService::APP_FRONTEND_ROOT . '/build/portal_' . $portalId;
        }

        $customizedFrontendBuildsJsonFile = FrontendBuildService::APP_FRONTEND_CUSTOMIZED_FRONTEND_BUILDS_JSON;
        if (file_exists($customizedFrontendBuildsJsonFile)) {
            $frontendBuilds = json_decode(file_get_contents($customizedFrontendBuildsJsonFile), true);
            foreach ($frontendBuilds as $frontendBuild) {
                $builds[WebpackExtension::BUILD_CUSTOMIZED . '_' . $frontendBuild] = FrontendBuildService::APP_FRONTEND_CUSTOMIZED_FRONTEND_BUILDS . '/build/' . $frontendBuild;
            }
        }

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

        $yamlParser = new Yaml();
        $filename = __DIR__ . '/../Resources/config/doctrine.yml';
        try {
            $config = $yamlParser->parse(
                file_get_contents($filename)
            );
        } catch (ParseException $e) {
            throw new \InvalidArgumentException(sprintf('The file "%s" does not contain valid YAML.', $filename), 0, $e);
        }

        $container->prependExtensionConfig('doctrine', $config['doctrine']);
    }

    protected function registerBatchTaskQueue(ContainerBuilder $container, array $config)
    {
        $startHandler = $container->getDefinition(Download\StartHandler::class);
        $startHandler->setArgument('$batchSize', $config['download']['batch_size']);

        $startHandler = $container->getDefinition(UpdateAssetMetadata\StartHandler::class);
        $startHandler->setArgument('$batchSize', $config['update_asset_metadata']['batch_size']);

        $batchTaskService = $container->getDefinition(BatchTaskService::class);
        $batchTaskService->setArgument('$cleanupUncompletedTasksAfterHours', $config['cleanup']['cleanup_uncompleted_tasks_after_hours']);
        $batchTaskService->setArgument('$cleanupFinishedTasksAfterHours', $config['cleanup']['cleanup_finished_tasks_after_hours']);

        return $this;
    }

    protected function registerDataPool(ContainerBuilder $container, array $config)
    {
        $searchServices = [
            \Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\DataObject\ListHandler::class,
            \Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\Asset\ListHandler::class,
        ];

        $container->setParameter(ContainerParameter::SELECT_ALL_MAX_SIZE, $config['select_all_max_size']);

        foreach ($searchServices as $service) {
            $searchService = $container->getDefinition($service);
            $searchService->setArgument('$selectAllMaxSize', $config['select_all_max_size']);
        }

        return $this;
    }

    protected function registerDownload(ContainerBuilder $container, array $config)
    {
        $asyncSizeEstimationService = $container->getDefinition(AsyncSizeEstimationService::class);
        $asyncSizeEstimationService->setArgument('$zipWarningSize', $config['zip_warning_size']);
        $asyncSizeEstimationService->setArgument('$zipRejectSize', $config['zip_reject_size']);

        return $this;
    }

    protected function registerLogin(ContainerBuilder $container, array $config)
    {
        $loginFormService = $container->getDefinition(LoginForm::class);
        $loginFormService->setArgument('$fields', $config['fields']);

        $userProviderService = $container->getDefinition(UserProvider::class);
        $userProviderService->setArgument('$fields', $config['fields']);

        return $this;
    }
}
