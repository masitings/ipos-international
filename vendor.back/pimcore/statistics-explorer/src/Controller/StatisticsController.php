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

namespace Pimcore\Bundle\StatisticsExplorerBundle\Controller;

use Pimcore\Bundle\StatisticsExplorerBundle\Entity\Configuration;
use Pimcore\Bundle\StatisticsExplorerBundle\Events\TableRenderEvent;
use Pimcore\Bundle\StatisticsExplorerBundle\Service\ConfigurationLoaderService;
use Pimcore\Bundle\StatisticsExplorerBundle\Service\StatisticsService;
use Pimcore\Bundle\StatisticsExplorerBundle\Service\UserProviderLocatorService;
use Pimcore\Bundle\StatisticsExplorerBundle\StatisticsStorageAdapter\StatisticsStorageAdapterInterface;
use Pimcore\Controller\Controller;
use Pimcore\Model\Translation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class StatisticsController extends Controller
{
    /**
     * @Route("/{context}/data-sources", requirements={"context"="\w+"})
     */
    public function dataSourcesAction(Request $request, StatisticsService $service, UserProviderLocatorService $userProviderLocatorService)
    {
        try {
            $context = strip_tags($request->get('context'));
            $userProvider = $userProviderLocatorService->getUserProvider($context);

            return $this->json([
                'success' => true,
                'dataSources' => $service->getDataSources($context),
                'otherUsers' => $userProvider->getOtherUsers()
            ]);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => 'Error loading data sources: ' . $e->getMessage()], 500);
        }
    }

    /**
     * @Route("/{context}/field-collection", requirements={"context"="\w+"})
     */
    public function fieldCollectionAction(Request $request, StatisticsService $service)
    {
        try {
            $dataSourceName = strip_tags($request->get('dataSource'));
            $context = strip_tags($request->get('context'));

            $fieldCollection = $service->getFieldsForDatasource($context, $dataSourceName);

            return $this->json([
                'success' => true,
                'fields' => $fieldCollection->getFields(),
                'operators' => $fieldCollection->getOperators()
            ]);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => 'Error loading field collection: ' . $e->getMessage()], 500);
        }
    }

    /**
     * @Route("/{context}/field-settings", requirements={"context"="\w+"})
     */
    public function fieldSettingsAction(Request $request, StatisticsService $service)
    {
        try {
            $dataSourceName = strip_tags($request->get('dataSource'));
            $context = strip_tags($request->get('context'));

            $statisticMode = strip_tags($request->get('statisticMode'));
            $rows = json_decode(strip_tags($request->get('rows')), true);
            $columns = json_decode(strip_tags($request->get('columns')), true);

            return $this->json([
                'fieldSettings' => $service->getFieldSettings($context, $dataSourceName, $statisticMode, $rows, $columns),
            ]);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => 'Error loading field settings: ' . $e->getMessage()], 500);
        }
    }

    /**
     * @Route("/{context}/data", requirements={"context"="\w+"})
     */
    public function dataAction(Request $request, StatisticsService $service, ConfigurationLoaderService $configurationLoaderService, EventDispatcherInterface $eventDispatcher)
    {
        try {
            $dataSourceName = strip_tags($request->get('dataSource'));
            $statisticMode = strip_tags($request->get('statisticMode', StatisticsStorageAdapterInterface::STATISTICS_MODE_STATISTIC));
            $rows = array_filter(explode(',', strip_tags($request->get('rows'))));
            $columns = array_filter(explode(',', strip_tags($request->get('columns'))));
            $filters = json_decode(strip_tags($request->get('filters')), true);
            $fieldSettings = json_decode(strip_tags($request->get('fieldSettings')), true) ?: [];

            $configuration = null;
            $configurationId = strip_tags($request->get('configurationId'));
            $context = strip_tags($request->get('context'));
            if ($configurationId) {
                $configuration = $configurationLoaderService->getConfigurationByIdPermissionAware($configurationId, $context);
            }

            $statisticsResult = $service->getStatisticsData($context, $dataSourceName, $statisticMode, $rows, $columns, $filters, $fieldSettings, $configuration);

            $columnHeaders = $statisticsResult->getColumnHeaders();

            if ($request->get('chartData')) {
                return $this->json([
                    'columnHeaders' => end($columnHeaders) ?: [],
                    'data' => array_values($statisticsResult->getData()),
                ]);
            } else {
                switch ($statisticMode) {
                    case StatisticsStorageAdapterInterface::STATISTICS_MODE_STATISTIC:
                        $parameters = [
                            'data' => $statisticsResult->getData(),
                            'columns' => $columns ?: ['totalCount'],
                            'columnHeaders' => $columnHeaders,
                            'lastLevelColumnHeaders' => end($columnHeaders),
                            'rowHeaders' => $statisticsResult->getRowHeaders(),
                            'rowCount' => count($rows)
                        ];

                        $template = '@PimcoreStatisticsExplorer/statistics/data.html.twig';
                        break;

                    case StatisticsStorageAdapterInterface::STATISTICS_MODE_LIST:
                        $parameters = [
                            'data' => $statisticsResult->getData(),
                            'columnHeaders' => $columnHeaders,
                            'lastLevelColumnHeaders' => end($columnHeaders),
                            'rowHeaders' => $statisticsResult->getRowHeaders(),
                            'rowCount' => count($rows)
                        ];

                        $template = '@PimcoreStatisticsExplorer/statistics/data-list.html.twig';
                        break;

                    default:
                        throw new \Exception('Invalid statistics mode');
                }

                $event = new TableRenderEvent($context, $configuration, $dataSourceName, $statisticMode, $template, $parameters);
                $eventDispatcher->dispatch($event);
                $template = $event->getTemplate();
                $parameters = $event->getParameters();

                return $this->render($template, $parameters);
            }
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => 'Error loading data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * @Route("/{context}/load-configuration-list", requirements={"context"="\w+"})
     */
    public function loadConfigurationsActions(Request $request, ConfigurationLoaderService $service)
    {
        try {
            $context = strip_tags($request->get('context'));
            $configurationCollection = $service->loadConfigurationsForActiveUser($context);

            $options = [];
            foreach ($configurationCollection as $group => $configurations) {
                $subOptions = [];
                foreach ($configurations as $configuration) {
                    $subOptions[] = [
                        'label' => $configuration->getName(),
                        'value' => $configuration->getId()
                    ];
                }

                $options[] = [
                    'label' => $group,
                    'options' => $subOptions
                ];
            }

            return $this->json([
                'options' => $options
            ]);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => 'Error loading configurations: ' . $e->getMessage()], 500);
        }
    }

    /**
     * @Route("/{context}/save-configuration", requirements={"context"="\w+"})
     */
    public function saveConfigurationAction(Request $request, ConfigurationLoaderService $service)
    {
        try {
            $configId = strip_tags($request->get('configId'));
            $context = strip_tags($request->get('context'));
            $configuration = null;
            if ($configId) {
                $configuration = $service->getConfigurationByIdOwnerAware($configId, $context);
            }

            if (!$configuration) {
                $configuration = new Configuration();
                $configuration->setContext($context);
            }

            $configuration->setName(strip_tags($request->get('name')));
            $configuration->setConfiguration(strip_tags($request->get('configuration')));

            $shareIds = [
                'user' => explode(',', $request->get('sharedUsers', '')),
                'role' => explode(',', $request->get('sharedRoles', ''))
            ];

            $configuration = $service->createOrUpdateConfiguration($configuration, $shareIds);

            return $this->json([
                'success' => true,
                'configurationId' => $configuration->getId(),
                'ownerShip' => $service->getOwnerShip($configuration)
            ]);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => 'Error saving configuration: ' . $e->getMessage()], 500);
        }
    }

    /**
     * @Route("/{context}/load", requirements={"context"="\w+"})
     */
    public function loadConfigurationAction(Request $request, ConfigurationLoaderService $service)
    {
        try {
            $configurationId = strip_tags($request->get('configurationId'));
            $context = strip_tags($request->get('context'));
            $configuration = $service->getConfigurationByIdPermissionAware($configurationId, $context);

            if ($configuration) {
                $ownerShip = $service->getOwnerShip($configuration);

                return $this->json([
                    'name' => $configuration->getName(),
                    'configurationId' => $configurationId,
                    'configuration' => $configuration->getConfiguration(),
                    'shares' => $ownerShip === ConfigurationLoaderService::OWNER_SHIP_OWNER ? $service->getSharesArray($configuration) : [],
                    'ownerShip' => $ownerShip
                ]);
            } else {
                return $this->json(['success' => false, 'message' => 'Error loading configuration: Config not found'], 404);
            }
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => 'Error loading configuration: ' . $e->getMessage()], 500);
        }
    }

    /**
     * @Route("/{context}/delete", requirements={"context"="\w+"})
     */
    public function deleteConfigurationAction(Request $request, ConfigurationLoaderService $service)
    {
        try {
            $configurationId = strip_tags($request->get('configurationId'));
            $configuration = $service->getConfigurationById($configurationId);
            $service->deleteConfiguration($configuration);

            return $this->json(['success' => true]);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => 'Error deleting configuration: ' . $e->getMessage()], 500);
        }
    }

    /**
     * @Route("/{context}/translations", requirements={"context"="\w+"})
     */
    public function translationsAction(Request $request)
    {
        $translationsList = new Translation\Listing();
        $translationsList->setDomain('messages');
        $translationsList->setCondition('`key` LIKE ?', ['statistics_container.%']);

        $locale = $request->getLocale();

        $translations = [];

        foreach ($translationsList->load() as $translation) {
            $translations[$translation->getKey()] = $translation->getTranslation($locale);
        }

        return $this->json($translations);
    }
}
