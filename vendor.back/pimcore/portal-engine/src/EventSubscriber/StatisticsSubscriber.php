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

namespace Pimcore\Bundle\PortalEngineBundle\EventSubscriber;

use Carbon\Carbon;
use ONGR\ElasticsearchDSL\Query\Compound\BoolQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\RangeQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\TermQuery;
use ONGR\ElasticsearchDSL\Search;
use Pimcore\Bundle\PortalEngineBundle\Enum\ElasticSearchFields;
use Pimcore\Bundle\PortalEngineBundle\Enum\Statistics;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\AssetConfig;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataObjectConfig;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\Element\NameExtractorService;
use Pimcore\Bundle\PortalEngineBundle\Service\Element\UrlExtractorService;
use Pimcore\Bundle\PortalEngineBundle\Service\PortalConfig\PortalConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Asset;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\DataObject;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\ElasticSearchConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Search\PreConditionService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\PermissionService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\SecurityService;
use Pimcore\Bundle\StatisticsExplorerBundle\Events\DataFilterModificationEvent;
use Pimcore\Bundle\StatisticsExplorerBundle\Events\DataResultEvent;
use Pimcore\Bundle\StatisticsExplorerBundle\Events\StatisticsServiceInitEvent;
use Pimcore\Bundle\StatisticsExplorerBundle\Events\TableRenderEvent;
use Pimcore\Bundle\StatisticsExplorerBundle\Model\StatisticsResult;
use Pimcore\Bundle\StatisticsExplorerBundle\StatisticsStorageAdapter\ElasticsearchAdapter;
use Pimcore\Bundle\StatisticsExplorerBundle\StatisticsStorageAdapter\StatisticsStorageAdapterInterface;
use Pimcore\Bundle\StatisticsExplorerBundle\StatisticsStorageAdapter\Worker\ElasticsearchListWorker;
use Pimcore\Bundle\StatisticsExplorerBundle\StatisticsStorageAdapter\Worker\ElasticsearchStatisticWorker;
use Pimcore\Bundle\StatisticsExplorerBundle\Tools\ElasticsearchClientFactory;
use Pimcore\Model\DataObject\ClassDefinition;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class StatisticsSubscriber implements EventSubscriberInterface
{
    /**
     * @var DataObject\Search\WorkspaceService
     */
    protected $dataObjectWorkspaceService;

    /**
     * @var Asset\Search\WorkspaceService
     */
    protected $assetWorkspaceService;

    /**
     * @var DataPoolConfigService $dataPoolConfigService
     */
    protected $dataPoolConfigService;

    /**
     * @var PermissionService
     */
    protected $permissionService;

    /**
     * @var SecurityService
     */
    protected $securityService;

    /**
     * @var PreConditionService
     */
    protected $preConditionService;

    /**
     * @var PortalConfigService
     */
    protected $portalConfigService;

    /**
     * @var ElasticsearchClientFactory
     */
    protected $elasticsearchClientFactory;

    /**
     * @var ElasticsearchStatisticWorker
     */
    protected $statisticWorker;

    /**
     * @var ElasticsearchListWorker
     */
    protected $listWorker;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var ElasticSearchConfigService
     */
    protected $elasticSearchConfigService;

    /**
     * @var NameExtractorService
     */
    protected $nameExtractorService;

    /**
     * @var UrlExtractorService
     */
    protected $urlExtractorService;

    /**
     * StatisticsSubscriber constructor.
     *
     * @param DataObject\Search\WorkspaceService $dataObjectWorkspaceService
     * @param Asset\Search\WorkspaceService $assetWorkspaceService
     * @param DataPoolConfigService $dataPoolConfigService
     * @param PermissionService $permissionService
     * @param SecurityService $securityService
     * @param PreConditionService $preConditionService
     * @param PortalConfigService $portalConfigService
     * @param ElasticsearchClientFactory $elasticsearchClientFactory
     * @param ElasticsearchStatisticWorker $statisticWorker
     * @param ElasticsearchListWorker $listWorker
     * @param EventDispatcherInterface $eventDispatcher
     * @param ElasticSearchConfigService $elasticSearchConfigService
     * @param NameExtractorService $nameExtractorService
     * @param UrlExtractorService $urlExtractorService
     */
    public function __construct(
        DataObject\Search\WorkspaceService $dataObjectWorkspaceService,
        Asset\Search\WorkspaceService $assetWorkspaceService,
        DataPoolConfigService $dataPoolConfigService,
        PermissionService $permissionService,
        SecurityService $securityService,
        PreConditionService $preConditionService,
        PortalConfigService $portalConfigService,
        ElasticsearchClientFactory $elasticsearchClientFactory,
        ElasticsearchStatisticWorker $statisticWorker,
        ElasticsearchListWorker $listWorker,
        EventDispatcherInterface $eventDispatcher,
        ElasticSearchConfigService $elasticSearchConfigService,
        NameExtractorService $nameExtractorService,
        UrlExtractorService $urlExtractorService
    ) {
        $this->dataObjectWorkspaceService = $dataObjectWorkspaceService;
        $this->assetWorkspaceService = $assetWorkspaceService;
        $this->dataPoolConfigService = $dataPoolConfigService;
        $this->permissionService = $permissionService;
        $this->securityService = $securityService;
        $this->preConditionService = $preConditionService;
        $this->portalConfigService = $portalConfigService;
        $this->elasticsearchClientFactory = $elasticsearchClientFactory;
        $this->statisticWorker = $statisticWorker;
        $this->listWorker = $listWorker;
        $this->eventDispatcher = $eventDispatcher;
        $this->elasticSearchConfigService = $elasticSearchConfigService;
        $this->nameExtractorService = $nameExtractorService;
        $this->urlExtractorService = $urlExtractorService;
    }

    public static function getSubscribedEvents()
    {
        return [
            DataFilterModificationEvent::class => 'modifyStatisticsFilter',
            DataResultEvent::class => 'modifyDataResult',
            StatisticsServiceInitEvent::class => 'addDataObjectClassSources',
            TableRenderEvent::class => 'renderAssetListing'
        ];
    }

    /**
     * @param DataFilterModificationEvent $event
     *
     * @throws \Exception
     */
    public function modifyStatisticsFilter(DataFilterModificationEvent $event)
    {
        if ($event->getConfiguration() && in_array($event->getConfiguration()->getName(), Statistics::ASSET_STATISTICS)) {
            $this->applyAssetDataPoolFilter($event);
        } elseif ($event->getConfiguration() && in_array($event->getConfiguration()->getName(), Statistics::DATA_OBJECT_STATISTICS)) {
            $this->applyDataObjectDataPoolFilter($event);
        } elseif ($event->getConfiguration() && $event->getConfiguration()->getName() === Statistics::ALL_LOGINS_LAST_SIX_MONTHS) {
            $this->applyLoginsFilter($event);
        }

        if ($event->getConfiguration() && in_array($event->getConfiguration()->getName(), Statistics::ADD_6_MONTHS_CONDITION)) {
            $this->addLast6MonthsFilter($event);
        }

        if ($event->getConfiguration() && in_array($event->getConfiguration()->getName(), Statistics::ADD_USER_CONDITION)) {
            $this->addUserFilter($event);
        }
    }

    public function modifyDataResult(DataResultEvent $event)
    {
        if ($event->getConfiguration() && $event->getConfiguration()->getName() === Statistics::ASSET_STORAGE_BY_TYPES) {
            $statisticResult = $event->getStatisticsResult();
            $data = $statisticResult->getData();
            foreach ($data as $key => $row) {
                $data[$key]['value'] = ceil($row['value'] / 1000 / 1000);
                $data[$key]['label'] = str_replace(' system_fields.fileSize sum', '', $row['label']);
            }
            $statisticResult = new StatisticsResult(
                $data,
                $statisticResult->getColumnHeaders(),
                $statisticResult->getRowHeaders()
            );
            $event->setStatisticsResult($statisticResult);
        }

        if ($event->getConfiguration() && in_array($event->getConfiguration()->getName(), Statistics::FORMAT_TIMESTAMP_STATISTICS)) {
            $statisticResult = $event->getStatisticsResult();
            $data = $statisticResult->getData();
            foreach ($data as $key => $row) {
                $data[$key]['timestamp'] = date('Y-m-d H:i:s', strtotime($row['timestamp']));
            }
            $statisticResult = new StatisticsResult(
                $data,
                $statisticResult->getColumnHeaders(),
                $statisticResult->getRowHeaders()
            );
            $event->setStatisticsResult($statisticResult);
        }
    }

    /**
     * @param DataFilterModificationEvent $event
     *
     * @throws \Exception
     */
    protected function applyAssetDataPoolFilter(DataFilterModificationEvent $event)
    {
        $boolQuery = new BoolQuery();

        foreach ($this->dataPoolConfigService->getDataPoolConfigsFromSite() as $dataPoolConfig) {
            if ($dataPoolConfig instanceof AssetConfig) {
                $this->dataPoolConfigService->setCurrentDataPoolConfig($dataPoolConfig);
                $mustQuery = new BoolQuery();
                $mustQuery->add($this->assetWorkspaceService->getElasticSearchWorkspaceQuery(), BoolQuery::MUST);
                $search = new Search();
                $this->preConditionService->applyElasticSearchPreConditions($search);
                if ($search->getQueries()) {
                    $mustQuery->add($search->getQueries(), BoolQuery::MUST);
                }

                $boolQuery->add($mustQuery, BoolQuery::SHOULD);
            }
        }

        $excludeFolderQuery = new TermQuery(ElasticSearchFields::SYSTEM_FIELDS . '.' . ElasticSearchFields::SYSTEM_FIELDS_TYPE, 'folder');
        $boolQuery->add($excludeFolderQuery, BoolQuery::MUST_NOT);
        $event->setFilter($boolQuery->toArray());
    }

    /**
     * @param DataFilterModificationEvent $event
     *
     * @throws \Exception
     */
    protected function applyDataObjectDataPoolFilter(DataFilterModificationEvent $event)
    {
        $boolQuery = new BoolQuery();

        foreach ($this->dataPoolConfigService->getDataPoolConfigsFromSite() as $dataPoolConfig) {
            if ($dataPoolConfig instanceof DataObjectConfig) {
                $this->dataPoolConfigService->setCurrentDataPoolConfig($dataPoolConfig);
                $mustQuery = new BoolQuery();
                $mustQuery->add($this->dataObjectWorkspaceService->getElasticSearchWorkspaceQuery(), BoolQuery::MUST);
                $classDefinition = ClassDefinition::getById($dataPoolConfig->getDataObjectClass());
                $className = $classDefinition ? $classDefinition->getName() : '-1';
                $classNameQuery = new TermQuery(ElasticSearchFields::SYSTEM_FIELDS . '.' . ElasticSearchFields::SYSTEM_FIELDS_CLASS_NAME . '.keyword', $className);
                $mustQuery->add($classNameQuery, BoolQuery::MUST);
                $search = new Search();
                $this->preConditionService->applyElasticSearchPreConditions($search);
                if ($search->getQueries()) {
                    $mustQuery->add($search->getQueries(), BoolQuery::MUST);
                }

                $boolQuery->add($mustQuery, BoolQuery::SHOULD);
            }
        }

        $event->setFilter($boolQuery->toArray());
    }

    /**
     * @param DataFilterModificationEvent $event
     *
     * @throws \Exception
     */
    protected function applyLoginsFilter(DataFilterModificationEvent $event)
    {
        $filter = new TermQuery('portalId', $this->portalConfigService->getCurrentPortalConfig()->getPortalId());
        $event->setFilter($filter->toArray());
    }

    protected function addLast6MonthsFilter(DataFilterModificationEvent $event)
    {
        $timestampQuery = new RangeQuery(
            'timestamp', [
                RangeQuery::GTE => Carbon::createFromTimestamp(strtotime(date('Y-m-1')))->subMonths(6)->format(
                    'Y-m-d\TH:i:sO'
                ),
            ]
        );

        $event->setFilter([
           'bool' => [
               'must' => [
                   $timestampQuery->toArray(),
                   $event->getFilter()
               ]
           ]
        ]);
    }

    protected function addUserFilter(DataFilterModificationEvent $event)
    {
        $timestampQuery = new TermQuery(
            'userId', $this->securityService->getPortalUser()->getId()
        );

        $event->setFilter([
           'bool' => [
               'must' => [
                   $timestampQuery->toArray(),
                   $event->getFilter()
               ]
           ]
        ]);
    }

    public function addDataObjectClassSources(StatisticsServiceInitEvent $event)
    {
        $statisticsService = $event->getStatisticsService();

        $dataPools = $this->dataPoolConfigService->getDataPoolConfigsFromSite();
        $classIds = [];
        foreach ($dataPools as $dataPool) {
            if ($dataPool instanceof DataObjectConfig) {
                $classIds[] = $dataPool->getDataObjectClass();
            }
        }

        $classes = [];
        foreach (array_unique($classIds) as $classId) {
            $classes[] = ClassDefinition::getById($classId);
        }

        foreach ($classes as $class) {
            $indexName = $this->elasticSearchConfigService->getIndexName($class->getName());
            $label = 'DataObject `' . $class->getName() . '`';
            $adapter = new ElasticsearchAdapter($this->elasticsearchClientFactory, $indexName, $this->statisticWorker, $this->listWorker, $this->eventDispatcher, $label);

            $statisticsService->addDataSourceAdapter('pimcoreportalengine_' . $class->getId(), 'portal', $adapter);
        }
    }

    public function renderAssetListing(TableRenderEvent $event)
    {
        if ($event->getConfiguration() && in_array($event->getConfiguration()->getName(), Statistics::RENDER_AS_ASSET_TABLE)) {
            $params = $event->getParameters();

            $newParams = [];

            if ($event->getStatisticsMode() === StatisticsStorageAdapterInterface::STATISTICS_MODE_LIST) {
                if (($params['columnHeaders']['0']['value'] ?? null) === 'system_fields.id') {
                    $newParams = [
                        'labels' => ['asset' => 'Asset', 'timestamp' => 'Timestamp'],
                        'data' => []
                    ];

                    foreach ($params['data'] as $dataEntry) {
                        $asset = \Pimcore\Model\Asset::getById($dataEntry['system_fields.id']);
                        if (empty($asset)) {
                            $dataEntry['asset_name'] = $dataEntry['path'];
                            $dataEntry['asset_url'] = false;
                        } else {
                            $dataEntry['asset_name'] = $this->nameExtractorService->extractName($asset);
                            $dataEntry['asset_url'] = $this->urlExtractorService->extractUrl($asset);
                        }

                        $newParams['data'][] = $dataEntry;
                    }
                }
            } else {
                $newParams = [
                    'labels' => ['asset' => 'Asset', 'result' => 'Result'],
                    'data' => []
                ];

                foreach ($params['rowHeaders'] as $row) {
                    $dataEntry = [];
                    $rowHeaders = $row['rowHeaders'];
                    $asset = \Pimcore\Model\Asset::getById($rowHeaders[0]['value']);
                    if (empty($asset)) {
                        $dataEntry['asset_name'] = $rowHeaders[1]['value'];
                        $dataEntry['asset_url'] = false;
                    } else {
                        $dataEntry['asset_name'] = $this->nameExtractorService->extractName($asset);
                        $dataEntry['asset_url'] = $this->urlExtractorService->extractUrl($asset);
                    }

                    $dataEntry['result'] = $params['data'][$row['dataKey']]['value'] ?? '-';

                    $newParams['data'][] = $dataEntry;
                }
            }

            $event->setParameters($newParams);
            $event->setTemplate('@PimcorePortalEngine/statistic_explorer/asset-data-list.html.twig');
        }
    }
}
