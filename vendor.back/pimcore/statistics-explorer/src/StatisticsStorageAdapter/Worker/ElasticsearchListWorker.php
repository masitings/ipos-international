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

class ElasticsearchListWorker extends EventDispatcherAwareWorker
{
    protected function generateFieldSettingsForListField(array $field, bool $isFirstRow = false): array
    {
        $fields = [];

        if ($isFirstRow) {
            $fields[] = [
                'type' => 'number',
                'name' => 'max',
                'label' => 'Max Results',
                'defaultValue' => 500
            ];
        }

        $fields[] = [
            'type' => 'select',
            'name' => 'order',
            'label' => 'Results Order',
            'defaultValue' => '',
            'options' => [
                ['label' => '', 'value' => ''],
                ['label' => 'asc', 'value' => 'asc'],
                ['label' => 'desc', 'value' => 'desc'],
            ]
        ];

        $fields[] = [
            'type' => 'input',
            'name' => 'label',
            'label' => 'Label',
            'defaultValue' => '',
        ];

        $fields[] = [
            'type' => 'checkbox',
            'name' => 'hidden',
            'label' => 'Hidden',
            'defaultValue' => false,
        ];

        return [
            'label' => $field['label'],
            'name' => $field['value'],
            'typeGroup' => $field['typeGroup'],
            'fields' => $fields
        ];
    }

    public function getFieldSettings(array $columns): array
    {
        $result = [];

        $firstColumn = array_shift($columns);

        if ($firstColumn) {
            $result[] = $this->generateFieldSettingsForListField($firstColumn, true);
        }

        if ($columns) {
            foreach ($columns as $field) {
                $result[] = $this->generateFieldSettingsForListField($field);
            }
        }

        return $result;
    }

    protected function extractValue($value, $fields)
    {
        $nextLevel = array_shift($fields);
        $extractedValue = [];
        if ($nextLevel) {
            if (is_array($value)) {
                if (array_key_exists($nextLevel, $value)) {
                    $extractedValue[] = $this->extractValue($value[$nextLevel], $fields);
                } else {
                    foreach ($value as $arrayEntry) {
                        if (is_array($arrayEntry) && array_key_exists($nextLevel, $arrayEntry)) {
                            $extractedValue[] = $this->extractValue($arrayEntry[$nextLevel], $fields);
                        }
                    }
                }
            }
        }

        if (empty($extractedValue) && !empty($value)) {
            if (is_array($value)) {
                $extractedValue[] = implode(', ', $value);
            } else {
                $extractedValue[] = $value;
            }
        }

        return implode(', ', $extractedValue);
    }

    protected function flattenResult(array $columns, array $result): array
    {
        $flattenData = [];

        foreach ($result as $resultEntry) {
            $dataRow = [];
            foreach ($columns as $column) {
                $columnParts = explode('.', $column);
                $value = $resultEntry['_source'];
                $value = $this->extractValue($value, $columnParts);

                $dataRow[$column] = $value;
            }

            $flattenData[] = $dataRow;
        }

        return $flattenData;
    }

    /**
     * @param array $columns
     * @param array $fieldSettings
     * @param array $query
     * @param string $indexName
     * @param Client $client
     * @param string $dataSourceName
     * @param string $context
     * @param Configuration|null $configuration
     *
     * @return array
     */
    public function loadData(array $columns, array $fieldSettings, array $query, string $indexName, Client $client, string $dataSourceName, string $context, ?Configuration $configuration): array
    {
        $firstColumn = reset($columns);
        $limit = $fieldSettings[$firstColumn]['max'];

        $sort = [];
        foreach ($columns as $field) {
            if (isset($fieldSettings[$field])) {
                if (!empty($fieldSettings[$field]['order'])) {
                    $sort[$field] = [
                        'order' => $fieldSettings[$field]['order']
                    ];
                }
            }
        }

        $esRequest = [
            'index' => $indexName,
            'body' => [
                'size' => $limit,
                'query' => $query,
                'sort' => $sort
            ]
        ];

        $event = new DataPreQueryEvent($context, $configuration, $dataSourceName, StatisticsStorageAdapterInterface::STATISTICS_MODE_LIST, $esRequest);
        $this->eventDispatcher->dispatch($event);
        $esRequest = $event->getQuery();

        $result = $client->search($esRequest);

        $flattenData = $this->flattenResult($columns, $result['hits']['hits'] ?? []);
        $columnHeaders = [];

        foreach ($columns as $column) {
            if (isset($fieldSettings[$column]) && isset($fieldSettings[$column]['hidden']) && $fieldSettings[$column]['hidden'] === true) {
                continue;
            }

            $label = $column;
            if (isset($fieldSettings[$column]) && !empty($fieldSettings[$column]['label'])) {
                $label = $fieldSettings[$column]['label'];
            }

            $columnHeaders[] = [
                'value' => $column,
                'label' => $label,
                'subColumns' => 0,
                'dataKey' => $column
            ];
        }

        return [
            $flattenData,
            $columnHeaders
        ];
    }
}
