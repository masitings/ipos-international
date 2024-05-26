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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Download\SizeEstimation;

use Elasticsearch\Client;
use Pimcore\Bundle\PortalEngineBundle\Enum\ElasticSearchFields;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\AssetConfig;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataObjectConfig;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataPoolConfigInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadItemInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadSize;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\DataObject\SearchService;
use Pimcore\Db;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\Element\Service;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

class PercentileDependenciesSizeEstimationStrategy implements SizeEstimationStrategyInterface
{
    use LoggerAwareTrait;

    const ASSET_DEPENDENCIES_FIELD = 'assetDependencies';

    protected $dataPoolConfigService;
    protected $sizeEstimationAdapter;
    protected $searchService;

    /**
     * @var Client
     */
    protected $esClient;

    public function __construct(
        DataPoolConfigService $dataPoolConfigService,
        SizeEstimationAdapterInterface $sizeEstimationAdapter,
        SearchService $searchService,
        LoggerInterface $logger
    ) {
        $this->dataPoolConfigService = $dataPoolConfigService;
        $this->sizeEstimationAdapter = $sizeEstimationAdapter;
        $this->searchService = $searchService;
        $this->logger = $logger;
    }

    /**
     * @return int
     */
    protected function getPercent()
    {
        return 80;
    }

    protected function getPrecision()
    {
        return 5;
    }

    /**
     * @param Client $esClient
     */
    public function setEsClient(Client $esClient)
    {
        $this->esClient = $esClient;
    }

    /**
     * {@inheritDoc}
     */
    public function getCustomDataObjectMappingForIndex(ClassDefinition $classDefinition): array
    {
        return [
            self::ASSET_DEPENDENCIES_FIELD => [
                'type' => 'integer'
            ]
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getCustomDataObjectDataForIndex(AbstractObject $dataObject): array
    {
        return [
            self::ASSET_DEPENDENCIES_FIELD => $this->getAssetDependenciesCount($dataObject)
        ];
    }

    /**
     * @param AbstractObject $dataObject
     *
     * @return int
     */
    protected function getAssetDependenciesCount(AbstractObject $dataObject)
    {
        return Db::get()->fetchOne("
            select count(*)
            from `dependencies`
            where `sourcetype` = :sourcetype AND `sourceid` = :source and `targettype` = 'asset'
        ", [
            'sourcetype' => Service::getElementType($dataObject),
            'source' => $dataObject->getId()
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getCustomAssetMappingForIndex(): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function getCustomAssetDataForIndex(Asset $asset): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function estimate(array $downloadItems): DownloadSize
    {
        if (empty($downloadItems)) {
            return DownloadSize::zero();
        }

        $groups = [];

        foreach ($downloadItems as $downloadItem) {
            $key = implode('_', [$downloadItem->getDataPoolId(), $downloadItem->getHash()]);
            $groups[$key][$downloadItem->getElementId()] = $downloadItem;
        }

        $downloadSize = DownloadSize::zero();

        foreach ($groups as $dataPoolId => $downloadItems) {
            list($dataPoolId) = explode('_', $dataPoolId);
            $this->dataPoolConfigService->setCurrentDataPoolConfigById($dataPoolId);
            $downloadSize = $downloadSize->add($this->estimateGroup($this->dataPoolConfigService->getCurrentDataPoolConfig(), $downloadItems));
        }

        return $downloadSize;
    }

    /**
     * @param DataPoolConfigInterface|null $dataPoolConfig
     * @param DownloadItemInterface[] $downloadItems
     *
     * @return DownloadSize
     */
    protected function estimateGroup(?DataPoolConfigInterface $dataPoolConfig, array $downloadItems): DownloadSize
    {
        if ($dataPoolConfig instanceof DataObjectConfig) {
            return $this->estimateDataObjectGroup($dataPoolConfig, $downloadItems);
        } elseif ($dataPoolConfig instanceof AssetConfig) {
            return $this->estimateAssetGroup($dataPoolConfig, $downloadItems);
        }

        return $this->estimateUnknownGroup($downloadItems);
    }

    /**
     * @param DataObjectConfig $dataObjectConfig
     * @param DownloadItemInterface[] $downloadItems
     *
     * @return DownloadSize
     */
    protected function estimateDataObjectGroup(DataObjectConfig $dataObjectConfig, array $downloadItems): DownloadSize
    {
        $ids = array_keys($downloadItems);

        $percentileAssetDependencies = $this->getPercentileAssetDependencies($dataObjectConfig, $ids);
        $ids = $this->getElementsByPercentileAssetDependencies($dataObjectConfig, $ids, $percentileAssetDependencies);

        if (!empty($ids)) {
            $items = array_filter(array_map(function ($id) use ($downloadItems) {
                return $downloadItems[$id];
            }, $ids));
        }

        $items = !empty($items) ? $items : array_slice($downloadItems, 0, $this->getPrecision());

        $downloadSize = DownloadSize::zero();

        foreach ($items as $item) {
            $downloadSize = $downloadSize->add($this->sizeEstimationAdapter->estimate($item));
        }

        $factor = ceil(count($downloadItems) / count($items));

        return $downloadSize->mul($factor);
    }

    /**
     * @param DataObjectConfig $dataObjectConfig
     * @param array $ids
     *
     * @return mixed
     */
    protected function getPercentileAssetDependencies(DataObjectConfig $dataObjectConfig, array $ids)
    {
        try {
            $result = $this->esClient->search([
                '_source_excludes' => [ElasticSearchFields::SYSTEM_FIELDS . '.' . ElasticSearchFields::SYSTEM_FIELDS_COLLECTIONS],
                'index' => $this->searchService->getESIndexName(ClassDefinition::getById($dataObjectConfig->getDataObjectClass())),
                'body' => [
                    'size' => 0,
                    'query' => [
                        'terms' => [
                            '_id' => $ids
                        ]
                    ],
                    'aggs' => [
                        'percentile' => [
                            'percentiles' => [
                                'field' => ElasticSearchFields::CUSTOM_FIELDS . '.' . self::ASSET_DEPENDENCIES_FIELD,
                                'percents' => [$this->getPercent()]
                            ]
                        ]
                    ]
                ]
            ]);

            return floor(reset($result['aggregations']['percentile']['values']));
        } catch (\Exception $e) {
            $this->logger->error('Could not query for percentile. ' . $e);

            return null;
        }
    }

    /**
     * @param DataObjectConfig $dataObjectConfig
     * @param array $ids
     * @param $percentileAssetDependencies
     *
     * @return mixed
     */
    protected function getElementsByPercentileAssetDependencies(DataObjectConfig $dataObjectConfig, array $ids, $percentileAssetDependencies)
    {
        try {
            $result = $this->esClient->search([
                '_source_excludes' => [ElasticSearchFields::SYSTEM_FIELDS . '.' . ElasticSearchFields::SYSTEM_FIELDS_COLLECTIONS],
                'index' => $this->searchService->getESIndexName(ClassDefinition::getById($dataObjectConfig->getDataObjectClass())),
                'body' => [
                    'size' => $this->getPrecision(),
                    'query' => [
                        'bool' => [
                            'must' => [
                                [
                                    'match' => [
                                        ElasticSearchFields::CUSTOM_FIELDS . '.' . self::ASSET_DEPENDENCIES_FIELD => $percentileAssetDependencies
                                    ]
                                ], [
                                    'terms' => [
                                        '_id' => $ids
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]);

            $hits = $result['hits']['hits'];

            return array_map(function ($hit) {
                return $hit['_id'];
            }, $hits);
        } catch (\Exception $e) {
            $this->logger->error('Could not find median element for percentile. ' . $e);
        }

        return null;
    }

    /**
     * @param AssetConfig $dataObjectConfig
     * @param DownloadItemInterface[] $downloadItems
     *
     * @return DownloadSize
     */
    protected function estimateAssetGroup(AssetConfig $dataObjectConfig, array $downloadItems): DownloadSize
    {
        // todo maybe store size in elastic and try to find percentile as well here
        $first = reset($downloadItems);
        $downloadSize = $this->sizeEstimationAdapter->estimate($first);

        return $downloadSize->mul(count($downloadItems));
    }

    /**
     * @param DownloadItemInterface[] $downloadItems
     *
     * @return DownloadSize
     */
    protected function estimateUnknownGroup(array $downloadItems): DownloadSize
    {
        $downloadSize = DownloadSize::zero();

        foreach ($downloadItems as $downloadItem) {
            $downloadSize = $downloadSize->add($this->sizeEstimationAdapter->estimate($downloadItem));
        }

        return $downloadSize;
    }
}
