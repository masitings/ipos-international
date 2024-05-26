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

namespace Pimcore\Bundle\StatisticsExplorerBundle\StatisticsStorageAdapter\Worker;

use Elasticsearch\Client;
use Pimcore\Bundle\StatisticsExplorerBundle\Entity\Configuration;
use Pimcore\Bundle\StatisticsExplorerBundle\Events\DataPreQueryEvent;
use Pimcore\Bundle\StatisticsExplorerBundle\StatisticsStorageAdapter\StatisticsStorageAdapterInterface;

class ElasticsearchStatisticWorker extends TranslationAwareWorker
{
    protected function getOrderByOptions(bool $includeCount): array
    {
        $options = [
            ['label' => $this->translate('lbl_order_key_asc'), 'value' => '_key||asc'],
            ['label' => $this->translate('lbl_order_key_desc'), 'value' => '_key||desc'],
        ];

        if ($includeCount) {
            $options[] = ['label' => $this->translate('lbl_order_count_asc'), 'value' => '_count||asc'];
            $options[] = ['label' => $this->translate('lbl_order_count_desc'), 'value' => '_count||desc'];
        }

        return $options;
    }

    protected function generateNumericFieldSettings(bool $includeOrderByCount = false)
    {
        return [
            [
                'type' => 'select',
                'name' => 'agg',
                'label' => $this->translate('lbl_metric'),
                'defaultValue' => 'count',
                'options' => [
                    ['label' => $this->translate('lbl_metric_count'), 'value' => 'count'],
                    ['label' => $this->translate('lbl_metric_avg'), 'value' => 'avg'],
                    ['label' => $this->translate('lbl_metric_max'), 'value' => 'max'],
                    ['label' => $this->translate('lbl_metric_min'), 'value' => 'min'],
                    ['label' => $this->translate('lbl_metric_sum'), 'value' => 'sum']
                ]
            ],
            [
                'type' => 'select',
                'name' => 'order',
                'label' => $this->translate('lbl_results_order'),
                'defaultValue' => '_key||asc',
                'options' => $this->getOrderByOptions($includeOrderByCount),
                'visibleCheckField' => 'agg',
                'visibleCheckValue' => 'count'
            ],
            [
                'type' => 'number',
                'name' => 'max',
                'label' => $this->translate('lbl_max_results'),
                'defaultValue' => 200,
                'visibleCheckField' => 'agg',
                'visibleCheckValue' => 'count'
            ]
        ];
    }

    protected function generateDateFieldSettings(bool $includeOrderByCount = false)
    {
        return [
            [
                'type' => 'select',
                'name' => 'order',
                'label' => $this->translate('lbl_results_order'),
                'defaultValue' => '_key||asc',
                'options' => $this->getOrderByOptions($includeOrderByCount)
            ],
            [
                'type' => 'input',
                'name' => 'interval',
                'label' => $this->translate('lbl_interval'),
                'defaultValue' => 'year',
            ],
            [
                'type' => 'input',
                'name' => 'format',
                'label' => $this->translate('lbl_format'),
                'defaultValue' => 'yyyy-MM-dd',
            ],
            [
                'type' => 'number',
                'name' => 'min_doc_count',
                'label' => $this->translate('lbl_minimal_document_count'),
                'defaultValue' => 0,
            ],
            [
                'type' => 'input',
                'name' => 'bounds_max',
                'label' => $this->translate('lbl_bounds_max'),
                'defaultValue' => 'now/d',
            ]
        ];
    }

    protected function generateStandardFieldSettings(bool $includeOrderByCount = false)
    {
        return [
            [
                'type' => 'select',
                'name' => 'order',
                'label' => $this->translate('lbl_results_order'),
                'defaultValue' => '_key||asc',
                'options' => $this->getOrderByOptions($includeOrderByCount)
            ],
            [
                'type' => 'number',
                'name' => 'max',
                'label' => $this->translate('lbl_max_results'),
                'defaultValue' => 200,
            ]
        ];
    }

    protected function generateFieldSettingsForField(array $field, bool $includeOrderByCount = false, bool $isLastField = false): array
    {
        if ($isLastField && $field['typeGroup'] === 'numeric') {
            $fields = $this->generateNumericFieldSettings($includeOrderByCount);
        } elseif ($field['typeGroup'] === 'date') {
            $fields = $this->generateDateFieldSettings($includeOrderByCount);
        } else {
            $fields = $this->generateStandardFieldSettings($includeOrderByCount);
        }

        return [
            'label' => $field['label'],
            'name' => $field['value'],
            'typeGroup' => $field['typeGroup'],
            'fields' => $fields
        ];
    }

    public function getFieldSettings(array $rows, array $columns): array
    {
        $result = [];

        $lastField = null;
        $allowCountSortingLastField = false;

        if (count($columns) < 1) {
            $lastField = array_pop($rows);
            $allowCountSortingLastField = true;
        } else {
            $lastField = array_pop($columns);
        }

        foreach ($rows as $field) {
            $result[] = $this->generateFieldSettingsForField($field, true);
        }
        foreach ($columns as $field) {
            $result[] = $this->generateFieldSettingsForField($field, false);
        }

        if ($lastField) {
            $result[] = $this->generateFieldSettingsForField($lastField, $allowCountSortingLastField, true);
        }

        return $result;
    }

    /**
     * @param array $rawFieldSettings
     *
     * @return array
     */
    protected function parseFieldSettings(array $rawFieldSettings): array
    {
        $fieldSettings = [];
        foreach ($rawFieldSettings as $fieldName => $configs) {
            foreach ($configs as $configName => $value) {
                if ($configName == 'order') {
                    $valueParts = explode('||', $value);

                    $fieldSettings[$fieldName]['orderKey'] = $valueParts[0];
                    $fieldSettings[$fieldName]['orderDir'] = $valueParts[1];
                } else {
                    $fieldSettings[$fieldName][$configName] = $value;
                }
            }
        }

        return $fieldSettings;
    }

    /**
     * @param string $field
     * @param array $aggs
     * @param array $fieldSettings
     *
     * @return \array[][]|\string[][][]
     */
    protected function buildAggs(string $field, array $aggs, array $fieldSettings): array
    {
        $key = $field;

        $size = $fieldSettings[$key]['max'] ?? 200 ?: 200;
        $orderKey = $fieldSettings[$key]['orderKey'] ?? '_key' ?: '_key';
        $orderDir = $fieldSettings[$key]['orderDir'] ?? 'asc' ?: 'asc';

        $aggType = $fieldSettings[$key]['agg'] ?? null;

        if (isset($fieldSettings[$key]) && $fieldSettings[$key]['typeGroup'] == 'numeric' && in_array($aggType, ['avg', 'max', 'min', 'sum'])) {
            $agg = [
                $key => [
                    'stats' => [
                        'field' => $field,
                    ]
                ]
            ];
        } elseif (isset($fieldSettings[$key]) && $fieldSettings[$key]['typeGroup'] == 'date') {
            $aggConfig = [
                'field' => $field,
                'interval' => $fieldSettings[$key]['interval'] ?? 'year' ?: 'year',
                'format' => $fieldSettings[$key]['format'] ?? 'yyyy-MM-dd' ?: 'yyyy-MM-dd',
                'min_doc_count' => $fieldSettings[$key]['min_doc_count'] ?? 0 ?: 0,
                'time_zone' => '+01:00',
                'extended_bounds' => [
                    'max' => $fieldSettings[$key]['bounds_max'] ?? 'now/d' ?: 'now/d'
                ],
                'order' => [
                    $orderKey => $orderDir
                ]
            ];
            $agg = [
                $key => [
                    'date_histogram' => $aggConfig
                ]
            ];
        } else {
            $agg = [
                $key => [
                    'terms' => [
                        'field' => $field,
                        'size' => $size,
                        'shard_size' => 10000000,
                        'order' => [
                            $orderKey => $orderDir
                        ]
                    ]
                ]
            ];
        }

        if ($aggs) {
            $agg[$key]['aggs'] = $aggs;
        }

        return $agg;
    }

    /**
     * @param array $aggregationResult
     * @param array $levelsToMerge
     * @param array $fieldSettings
     * @param bool $preserveSubLevels
     * @param array $bucketKeys
     * @param string $keyPrefix
     *
     * @return array
     */
    protected function parseAggregationResult(array $aggregationResult, array $levelsToMerge, array $fieldSettings, bool $preserveSubLevels = false, $bucketKeys = [], $keyPrefix = ''): array
    {
        $parseResult = [];

        $currentLevel = array_shift($levelsToMerge);
        $key = $currentLevel;

        if ($key) {
            if (isset($aggregationResult[$key]['buckets'])) {
                $buckets = $aggregationResult[$key]['buckets'] ?? [];

                foreach ($buckets as $bucket) {
                    $currentBucketKeys = $bucketKeys;
                    $currentBucketKeys[] = isset($bucket['key_as_string']) ? $bucket['key_as_string'] : $bucket['key'];

                    if ($levelsToMerge) {
                        $subLevels = $this->parseAggregationResult($bucket, $levelsToMerge, $fieldSettings, $preserveSubLevels, $currentBucketKeys, $keyPrefix . $bucket['key'] . '_');
                        if ($subLevels) {
                            $parseResult = array_merge($parseResult, $subLevels);
                        }
                    } else {
                        if ($preserveSubLevels) {
                            $parseResult[$keyPrefix . $bucket['key']] = [
                                'value' => $bucket['doc_count'],
                                'subLevels' => $bucket,
                                'label' => implode(' ', $currentBucketKeys)
                            ];
                        } else {
                            $parseResult[$keyPrefix . $bucket['key']] = $bucket['doc_count'];
                            $parseResult[$keyPrefix . $bucket['key'] . '__label'] = implode(' ', $currentBucketKeys);
                        }
                    }
                }
            } else {
                if (isset($fieldSettings[$key]['agg'])) {
                    $agg = $fieldSettings[$key]['agg'];
                    $currentBucketKeys = array_merge($bucketKeys, [$key, $agg]);
                    $value = $aggregationResult[$key][$agg];

                    if ($preserveSubLevels) {
                        $parseResult[$keyPrefix . $key] = [
                            'label' => implode(' ', $currentBucketKeys),
                            'value' => $value
                        ];
                    } else {
                        $parseResult[$keyPrefix . $key] = $value;
                        $parseResult[$keyPrefix . $key . '__label'] = implode(' ', $currentBucketKeys);
                    }
                }
            }
        }

        return $parseResult;
    }

    /**
     * @param array $aggregationResult
     * @param array $levels
     * @param array $levelInformation
     * @param array $fieldSettings
     * @param int $currentLevelIndex
     * @param array $bucketKeys
     *
     * @return int
     */
    protected function extractColumnDefinition(array $aggregationResult, array $levels, array &$levelInformation, array $fieldSettings, int $currentLevelIndex = 0, array $bucketKeys = []): int
    {
        $totalSubColumns = 0;

        $currentLevel = array_shift($levels);
        $key = $currentLevel;

        if ($key) {
            if (isset($aggregationResult[$key]['buckets'])) {
                $buckets = $aggregationResult[$key]['buckets'];
                foreach ($buckets as $bucket) {
                    $currentBucketKeys = $bucketKeys;
                    $currentBucketKeys[] = isset($bucket['key_as_string']) ? $bucket['key_as_string'] : $bucket['key'];

                    $subColumns = $this->extractColumnDefinition($bucket, $levels, $levelInformation, $fieldSettings, $currentLevelIndex + 1, $currentBucketKeys);

                    $levelInformation[$currentLevelIndex][] = [
                        'value' => $bucket['key'],
                        'dataKey' => implode('_', $currentBucketKeys),
                        'label' => implode(' ', $currentBucketKeys),
                        'subColumns' => $subColumns
                    ];

                    $totalSubColumns += ($subColumns ? $subColumns : 1);
                }
            } else {
                if (isset($fieldSettings[$key]['agg'])) {
                    $agg = $fieldSettings[$key]['agg'];
                    $currentBucketKeys = array_merge($bucketKeys, [$key]);

                    $levelInformation[$currentLevelIndex][] = [
                        'value' => $agg . ' ' . $key,
                        'dataKey' => implode('_', $currentBucketKeys),
                        'label' => implode(' ', $currentBucketKeys) . ' ' . $agg,
                        'subColumns' => 0
                    ];
                    $totalSubColumns = 1;
                }
            }
        }

        return $totalSubColumns;
    }

    /**
     * @param array $aggregationResult
     * @param array $levels
     * @param array $fieldSettings
     *
     * @return array|null
     */
    protected function extractRowHeaderDefinition(array $aggregationResult, array $levels, array $fieldSettings): ?array
    {
        $mergedSubRows = [];

        $currentLevel = array_shift($levels);
        if (empty($currentLevel)) {
            return null;
        } else {
            $key = $currentLevel;

            if (isset($aggregationResult[$key]['buckets'])) {
                $buckets = $aggregationResult[$key]['buckets'] ?? [];
                foreach ($buckets as $bucket) {
                    $subRows = $this->extractRowHeaderDefinition($bucket, $levels, $fieldSettings);
                    if ($subRows) {
                        $firstSubRow = array_shift($subRows);
                        $mergedSubRows[] = array_merge([[
                            'label' => isset($bucket['key_as_string']) ? $bucket['key_as_string'] : $bucket['key'],
                            'value' => $bucket['key'],
                            'rowspan' => count($subRows) + 1
                        ]], $firstSubRow);

                        foreach ($subRows as $subRow) {
                            $mergedSubRows[] = array_merge([[
                                'label' => null,
                                'value' => $bucket['key'],
                                'rowspan' => null
                            ]], $subRow);
                        }
                    } else {
                        $mergedSubRows[] = [[
                            'label' => isset($bucket['key_as_string']) ? $bucket['key_as_string'] : $bucket['key'],
                            'value' => $bucket['key'],
                            'rowspan' => 1
                        ]];
                    }
                }
            } else {
                if (isset($fieldSettings[$key]['agg'])) {
                    $agg = $fieldSettings[$key]['agg'];

                    $mergedSubRows[] = [[
                        'label' => $agg . ' ' . $key,
                        'value' => $key,
                        'rowspan' => 1
                    ]];
                }
            }
        }

        return $mergedSubRows;
    }

    /**
     * @param array $rows
     * @param array $columns
     * @param array $fieldSettings
     * @param array $query
     * @param string $indexName
     * @param Client $client
     * @param string $dataSourceName
     * @param string $context
     * @param Configuration|null $configuration
     *
     * @return array[]
     */
    public function loadData(array $rows, array $columns, array $fieldSettings, array $query, string $indexName, Client $client, string $dataSourceName, string $context, ?Configuration $configuration): array
    {
        $fieldSettings = $this->parseFieldSettings($fieldSettings);

        $aggs = [];

        $reverseColumns = array_reverse($columns);
        foreach ($reverseColumns as $column) {
            $aggs = $this->buildAggs($column, $aggs, $fieldSettings);
        }

        foreach (array_reverse($rows) as $row) {
            $aggs = $this->buildAggs($row, $aggs, $fieldSettings);
        }

        $colAggs = [];
        foreach ($reverseColumns as $column) {
            $colAggs = $this->buildAggs($column, $colAggs, $fieldSettings);
        }

        $esRequest = [
            'index' => $indexName,
            'body' => [
                'size' => 0,
                'query' => $query,
                'aggs' => array_merge($aggs, $colAggs)
            ]
        ];

        $event = new DataPreQueryEvent($context, $configuration, $dataSourceName, StatisticsStorageAdapterInterface::STATISTICS_MODE_STATISTIC, $esRequest);
        $this->eventDispatcher->dispatch($event);
        $esRequest = $event->getQuery();

        $result = $client->search($esRequest);

        $fullyFlattenDataAggregationResult = [];
        $columnDefinitions = [];
        $rowHeadersWithDataKeys = [];

        if (!empty($result['aggregations'])) {
            $flattenDataAggregationResult = $this->parseAggregationResult($result['aggregations'], $rows, $fieldSettings, true);

            foreach ($flattenDataAggregationResult as $key => $flattenRow) {
                if (is_array($flattenRow)) {
                    if (isset($flattenRow['subLevels'])) {
                        $flattenColumns = $this->parseAggregationResult($flattenRow['subLevels'], $columns, $fieldSettings);
                    } else {
                        $flattenColumns = [];
                    }

                    $flattenColumns['value'] = $flattenRow['value'];
                    $flattenColumns['label'] = $flattenRow['label'];

                    $fullyFlattenDataAggregationResult[$key] = $flattenColumns;
                }
            }

            if ($columns) {
                $this->extractColumnDefinition($result['aggregations'], $columns, $columnDefinitions, $fieldSettings);
            } else {
                $columns[] = 'totalCount';

                $columnDefinitions[][] = [
                    'value' => $this->translate('lbl_result'),
                    'label' => $this->translate('lbl_result'),
                    'subColumns' => 0,
                    'dataKey' => 'value'
                ];
            }

            ksort($columnDefinitions);

            $rowHeaders = $this->extractRowHeaderDefinition($result['aggregations'], $rows, $fieldSettings);

            foreach ($rowHeaders as $rowHeader) {
                $dataKey = [];
                foreach ($rowHeader as $subRowHeader) {
                    $dataKey[] = $subRowHeader['value'];
                }

                $rowHeadersWithDataKeys[] = [
                    'rowHeaders' => $rowHeader,
                    'dataKey' => implode('_', $dataKey)
                ];
            }
        }

        return [
            $fullyFlattenDataAggregationResult,
            $columnDefinitions,
            $rowHeadersWithDataKeys
        ];
    }
}
