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

namespace Pimcore\Bundle\StatisticsExplorerBundle\StatisticsStorageAdapter;

use Pimcore\Bundle\StatisticsExplorerBundle\Entity\Configuration;
use Pimcore\Bundle\StatisticsExplorerBundle\Events\DataFilterModificationEvent;
use Pimcore\Bundle\StatisticsExplorerBundle\Model\FieldsCollection;
use Pimcore\Bundle\StatisticsExplorerBundle\Model\StatisticsResult;
use Pimcore\Bundle\StatisticsExplorerBundle\StatisticsStorageAdapter\Worker\ElasticsearchListWorker;
use Pimcore\Bundle\StatisticsExplorerBundle\StatisticsStorageAdapter\Worker\ElasticsearchStatisticWorker;
use Pimcore\Bundle\StatisticsExplorerBundle\Tools\ElasticsearchClientFactory;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ElasticsearchAdapter implements StatisticsStorageAdapterInterface
{
    const TYPE = 'ELASTIC_SEARCH';

    /**
     * @var ElasticsearchClientFactory
     */
    protected $clientFactory;

    /**
     * @var string
     */
    protected $indexName;

    /**
     * @var ElasticsearchStatisticWorker
     */
    protected $workerStatistic;

    /**
     * @var ElasticsearchListWorker
     */
    protected $workerList;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var string
     */
    protected $label;

    /**
     * @param ElasticsearchClientFactory $clientFactory
     * @param string $indexName
     * @param ElasticsearchStatisticWorker $workerStatistic
     * @param ElasticsearchListWorker $workerList
     * @param EventDispatcherInterface $eventDispatcher
     * @param string|null $label
     */
    public function __construct(ElasticsearchClientFactory $clientFactory, string $indexName, ElasticsearchStatisticWorker $workerStatistic, ElasticsearchListWorker $workerList, EventDispatcherInterface $eventDispatcher, string $label = null)
    {
        $this->clientFactory = $clientFactory;
        $this->indexName = $indexName;
        $this->workerStatistic = $workerStatistic;
        $this->workerList = $workerList;
        $this->eventDispatcher = $eventDispatcher;
        $this->label = $label;
    }

    public function getType(): string
    {
        return self::TYPE;
    }

    /**
     * @return \Elasticsearch\Client
     */
    protected function getElasticSearchClient()
    {
        return $this->clientFactory->getESClient();
    }

    /**
     * @param array $requestFilterArray
     *
     * @return \array[][][]|\stdClass[]
     */
    protected function buildFilters(array $requestFilterArray): array
    {
        $filters = [];

        foreach ($requestFilterArray as $requestFilter) {
            if ($requestFilter['operator']) {
                if ($requestFilter['operator'] == 'exists') {
                    $filters['must'][] = [
                        'exists' => ['field' => $requestFilter['field']]
                    ];
                } elseif ($requestFilter['operator'] == 'not_exists') {
                    $filters['must_not'][] = [
                        'exists' => ['field' => $requestFilter['field']]
                    ];
                } elseif (in_array($requestFilter['operator'], ['must', 'must_not', 'should']) && $requestFilter['filter']) {
                    $filters[$requestFilter['operator']][] = [
                        'term' => [$requestFilter['field'] => $requestFilter['filter']]
                    ];
                } elseif (in_array($requestFilter['operator'], ['lt', 'lte', 'gte', 'gt']) && $requestFilter['filter']) {
                    $filters['must'][] = [
                        'range' => [$requestFilter['field'] => [$requestFilter['operator'] => $requestFilter['filter']]]
                    ];
                } elseif ($requestFilter['operator'] == 'eq' && $requestFilter['filter']) {
                    $filters['must'][] = [
                        'term' => [$requestFilter['field'] => $requestFilter['filter']]
                    ];
                }
            }
        }

        if ($filters) {
            return [
                'bool' => [
                    'filter' => [
                        'bool' => $filters
                    ]
                ]
            ];
        } else {
            return [
                'match_all' => new \stdClass()
            ];
        }
    }

    /**
     * @param string $statisticMode
     * @param array $rows
     * @param array $columns
     * @param array $filters
     * @param array $fieldSettings
     * @param string $dataSourceName
     * @param string $context
     * @param Configuration|null $configuration
     *
     * @return StatisticsResult
     */
    public function getStatisticsData(string $statisticMode, array $rows, array $columns, array $filters, array $fieldSettings, string $dataSourceName, string $context, ?Configuration $configuration): StatisticsResult
    {
        $query = $this->buildFilters($filters);

        $event = new DataFilterModificationEvent($context, $configuration, $dataSourceName, $statisticMode, $query);
        $this->eventDispatcher->dispatch($event);
        $query = $event->getFilter();

        $client = $this->getElasticSearchClient();

        switch ($statisticMode) {
            case self::STATISTICS_MODE_STATISTIC:
                list($fullyFlattenDataAggregationResult, $columnDefinitions, $rowHeadersWithDataKeys) =
                    $this->workerStatistic->loadData($rows, $columns, $fieldSettings, $query, $this->indexName, $client, $dataSourceName, $context, $configuration);
                break;
            case self::STATISTICS_MODE_LIST:
                $rowHeadersWithDataKeys = [];
                list($fullyFlattenDataAggregationResult, $columnDefinitions) = $this->workerList->loadData($columns, $fieldSettings, $query, $this->indexName, $client, $dataSourceName, $context, $configuration);
                break;
        }

        return new StatisticsResult(
            $fullyFlattenDataAggregationResult,
            $columnDefinitions,
            $rowHeadersWithDataKeys
        );
    }

    protected function flattenMappingAttributes(array $mappings, $attributePrefix = '')
    {
        $flattenedAttributes = [];

        foreach ($mappings as $name => $property) {
            if (isset($property['properties'])) {
                $flattenedAttributes = array_merge($flattenedAttributes, $this->flattenMappingAttributes($property['properties'], $attributePrefix . $name . '.'));
            } else {
                $aggReadyField = !in_array($property['type'] ?? '', ['text']);

                if ($aggReadyField) {
                    if (in_array($property['type'] ?? '', ['integer', 'double', 'long', 'short', 'byte', 'double', 'half_float', 'scaled_float'])) {
                        $typeGroup = 'numeric';
                    } elseif (in_array($property['type'] ?? '', ['date', 'date_nanos'])) {
                        $typeGroup = 'date';
                    } else {
                        $typeGroup = 'default';
                    }

                    $flattenedAttributes[$attributePrefix . $name] = [
                        'value' => $attributePrefix . $name,
                        'label' => $attributePrefix . $name,
                        'typeGroup' => $typeGroup,
                    ];
                }

                if (isset($property['fields'])) {
                    $flattenedAttributes = array_merge($flattenedAttributes, $this->flattenMappingAttributes($property['fields'], $attributePrefix . $name . '.'));
                }
            }
        }

        return $flattenedAttributes;
    }

    /**
     * @return FieldsCollection
     */
    public function getFieldsForDatasource(): FieldsCollection
    {
        $allAttributes = [];
        if ($this->indexName) {
            $client = $this->getElasticSearchClient();
            $params = [
                'index' => [ $this->indexName ],
                'include_type_name' => false
            ];
            $response = $client->indices()->getMapping($params);

            $allAttributes = [];
            foreach ($response as $index) {
                $allAttributes = array_merge($allAttributes, $this->flattenMappingAttributes($index['mappings']['properties']));
            }
        }

        $operators = [
            'default' => [
                ['label' => 'must', 'value' => 'must', 'needsFilterValue' => true],
                ['label' => 'should', 'value' => 'should', 'needsFilterValue' => true],
                ['label' => 'must not', 'value' => 'must_not', 'needsFilterValue' => true],
                ['label' => 'exists', 'value' => 'exists', 'needsFilterValue' => false],
                ['label' => 'not exists', 'value' => 'not_exists', 'needsFilterValue' => false],
            ],
            'numeric' => [
                ['label' => 'lower than', 'value' => 'lt', 'needsFilterValue' => true],
                ['label' => 'lower or equal', 'value' => 'lte', 'needsFilterValue' => true],
                ['label' => 'equal', 'value' => 'eq', 'needsFilterValue' => true],
                ['label' => 'greater or equal', 'value' => 'gte', 'needsFilterValue' => true],
                ['label' => 'greater than', 'value' => 'gt', 'needsFilterValue' => true],
                ['label' => 'exists', 'value' => 'exists', 'needsFilterValue' => false],
                ['label' => 'not exists', 'value' => 'not_exists', 'needsFilterValue' => false],
            ],
            'date' => [
                ['label' => 'lower than', 'value' => 'lt', 'needsFilterValue' => true],
                ['label' => 'lower or equal', 'value' => 'lte', 'needsFilterValue' => true],
                ['label' => 'equal', 'value' => 'eq', 'needsFilterValue' => true],
                ['label' => 'greater or equal', 'value' => 'gte', 'needsFilterValue' => true],
                ['label' => 'greater than', 'value' => 'gt', 'needsFilterValue' => true],
                ['label' => 'exists', 'value' => 'exists', 'needsFilterValue' => false],
                ['label' => 'not exists', 'value' => 'not_exists', 'needsFilterValue' => false],
            ]
        ];

        return new FieldsCollection(array_values($allAttributes), $operators);
    }

    /**
     * @param string $statisticMode
     * @param array $rows
     * @param array $columns
     *
     * @return array
     */
    public function getFieldSettings(string $statisticMode, array $rows, array $columns): array
    {
        switch ($statisticMode) {
            case self::STATISTICS_MODE_STATISTIC:
                return $this->workerStatistic->getFieldSettings($rows, $columns);
            case self::STATISTICS_MODE_LIST:
                return $this->workerList->getFieldSettings($columns);
            default:
                return [];
        }
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }
}
