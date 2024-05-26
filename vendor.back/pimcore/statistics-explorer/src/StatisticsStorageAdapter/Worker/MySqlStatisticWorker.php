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

use Doctrine\DBAL\Connection;
use Pimcore\Bundle\StatisticsExplorerBundle\Entity\Configuration;
use Pimcore\Bundle\StatisticsExplorerBundle\Events\DataPreQueryEvent;
use Pimcore\Bundle\StatisticsExplorerBundle\StatisticsStorageAdapter\StatisticsStorageAdapterInterface;

class MySqlStatisticWorker extends TranslationAwareWorker
{
    protected function generateFieldSettingsForStatisticsField(array $field, bool $isLastRow = false, bool $isLastField = false): array
    {
        $fields = [];

        if (!$isLastField || ($isLastField && $isLastRow)) {
            $fields[] = [
                'type' => 'select',
                'name' => 'order',
                'label' => $this->translate('lbl_results_order'),
                'defaultValue' => '',
                'options' => [
                    ['label' => '', 'value' => ''],
                    ['label' => $this->translate('lbl_order_asc'), 'value' => 'asc'],
                    ['label' => $this->translate('lbl_order_desc'), 'value' => 'desc'],
                ]
            ];
        }

        if ($isLastField) {
            $fields[] = [
                'type' => 'select',
                'name' => 'order_count',
                'label' => $this->translate('lbl_count_order'),
                'defaultValue' => '',
                'options' => [
                    ['label' => '', 'value' => ''],
                    ['label' => $this->translate('lbl_order_asc'), 'value' => 'asc'],
                    ['label' => $this->translate('lbl_order_desc'), 'value' => 'desc'],
                ]
            ];
        }

        if ($isLastRow) {
            $fields[] = [
                'type' => 'number',
                'name' => 'max',
                'label' => $this->translate('lbl_max_results'),
                'defaultValue' => 500
            ];
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

        $lastRow = array_pop($rows);
        $lastField = $lastRow;

        if (count($columns) > 0) {
            $lastField = array_pop($columns);
        }

        foreach ($rows as $field) {
            $result[] = $this->generateFieldSettingsForStatisticsField($field);
        }

        if ($lastRow) {
            $result[] = $this->generateFieldSettingsForStatisticsField($lastRow, true, $lastField === $lastRow);
        }
        if ($lastField !== $lastRow) {
            $result[] = $this->generateFieldSettingsForStatisticsField($lastField, false, true);
        }

        return $result;
    }

    protected function flattenSubRows(array $treeData)
    {
        $mergedSubRows = [];

        foreach ($treeData as $key => $subLevelTreeData) {
            if (is_array($subLevelTreeData) && count($subLevelTreeData) > 0) {
                $subRows = $this->flattenSubRows($subLevelTreeData);
                if ($subRows) {
                    $firstSubRow = array_shift($subRows);
                    $mergedSubRows[] = array_merge([[
                        'label' => $key,
                        'value' => $key,
                        'rowspan' => count($subRows) + 1
                    ]], $firstSubRow);

                    foreach ($subRows as $subRow) {
                        $mergedSubRows[] = array_merge([[
                            'label' => null,
                            'value' => $key,
                            'rowspan' => null
                        ]], $subRow);
                    }
                }
            } else {
                $mergedSubRows[] = [[
                    'label' => $key,
                    'value' => $key,
                    'rowspan' => 1,
                ]];
            }
        }

        return $mergedSubRows;
    }

    protected function calculateRowHeaders(array $data, array $rows)
    {
        $treeData = [];
        foreach ($data as $dataRow) {
            $workingRows = $rows;
            $currentTreeLevel = &$treeData;
            while (count($workingRows) > 0) {
                $currentRow = array_shift($workingRows);

                if (!isset($currentTreeLevel[$dataRow[$currentRow]])) {
                    $currentTreeLevel[$dataRow[$currentRow]] = [];
                }
                $currentTreeLevel = &$currentTreeLevel[$dataRow[$currentRow]];
            }
            unset($currentTreeLevel);
        }

        $flattenedRows = $this->flattenSubRows($treeData);
        $rowHeadersWithDataKeys = [];
        foreach ($flattenedRows as $row) {
            $dataKey = [];
            foreach ($row as $subRowHeader) {
                $dataKey[] = $subRowHeader['value'];
            }

            $rowHeadersWithDataKeys[] = [
                'rowHeaders' => $row,
                'dataKey' => implode('_', $dataKey)
            ];
        }

        return $rowHeadersWithDataKeys;
    }

    protected function flattenSubColumns(array $levelTreeData, array &$columnHeaders, int $level = 0, string $dataKeyPrefix = '')
    {
        foreach ($levelTreeData as $key => $subColumns) {
            $dataKey = $dataKeyPrefix . $key;
            if (is_array($subColumns) && count($subColumns) > 0) {
                $this->flattenSubColumns($subColumns, $columnHeaders, $level + 1, $dataKey . '_');
            }

            $columnHeaders[$level][] = [
                'value' => $key,
                'label' => $key,
                'subColumns' => count($subColumns),
                'dataKey' => $dataKey
            ];
        }
    }

    protected function calculateColumnHeaders(array $data, array $columns): array
    {
        $treeData = [];
        foreach ($data as $dataRow) {
            $workingColumns = $columns;
            $currentTreeLevel = &$treeData;
            while (count($workingColumns) > 0) {
                $currentColumn = array_shift($workingColumns);

                if (!isset($currentTreeLevel[$dataRow[$currentColumn]])) {
                    $currentTreeLevel[$dataRow[$currentColumn]] = [];
                }
                $currentTreeLevel = &$currentTreeLevel[$dataRow[$currentColumn]];
            }
            unset($currentTreeLevel);
        }

        $columnHeaders = [];
        $this->flattenSubColumns($treeData, $columnHeaders);

        return array_reverse($columnHeaders);
    }

    /**
     * @param Connection $dbConnection
     * @param array $rows
     * @param array $columns
     * @param array $fields
     * @param array $fieldSettings
     * @param string $tableName
     * @param string $where
     * @param string $orderBy
     * @param string $dataSourceName
     * @param string $context
     * @param Configuration|null $configuration
     *
     * @return array
     */
    public function loadData(Connection $dbConnection, array $rows, array $columns, array $fields, array $fieldSettings, string $tableName, string $where, string $orderBy, string $dataSourceName, string $context, ?Configuration $configuration): array
    {
        $statement = sprintf(
            'SELECT %s, count(*) as `__COUNT` FROM %s %s GROUP BY %s %s LIMIT 1000000',
            implode(',', $fields),
            $dbConnection->quoteIdentifier($tableName),
            $where,
            implode(',', $fields),
            $orderBy
        );

        $event = new DataPreQueryEvent($context, $configuration, $dataSourceName, StatisticsStorageAdapterInterface::STATISTICS_MODE_STATISTIC, $statement);
        $this->eventDispatcher->dispatch($event);
        $statement = $event->getQuery();

        $data = $dbConnection->fetchAllAssociative($statement);

        $flattenedData = [];
        foreach ($data as $dataRow) {
            $rowValues = [];

            foreach ($rows as $row) {
                $rowValues[] = $dataRow[$row];
            }

            $rowKey = implode('_', $rowValues);

            if ($columns) {
                $columnValues = [];
                foreach ($columns as $column) {
                    $columnValues[] = $dataRow[$column];
                }
                $columnKey = implode('_', $columnValues);
                $flattenedData[$rowKey][$columnKey] = (int)$dataRow['__COUNT'];
                $flattenedData[$rowKey][$columnKey . '__label'] = implode(' ', $columnValues);
            } else {
                $flattenedData[$rowKey]['value'] = (int)$dataRow['__COUNT'];
                $flattenedData[$rowKey]['label'] = implode(' ', $rowValues);
            }
        }

        $rowHeaders = $this->calculateRowHeaders($data, $rows);

        if ($columns) {
            $columnHeaders = $this->calculateColumnHeaders($data, $columns);
        } else {
            $columnHeaders[][] = [
                'value' => 'Result',
                'label' => $this->translate('lbl_result'),
                'subColumns' => 0,
                'dataKey' => 'value'
            ];
        }

        $lastRow = end($rows);
        if (isset($fieldSettings[$lastRow]) && !empty($fieldSettings[$lastRow]['max'])) {
            $limit = $fieldSettings[$lastRow]['max'];
            $flattenedData = array_slice($flattenedData, 0, $limit);
            $rowHeaders = array_slice($rowHeaders, 0, $limit);
        }

        return [
            $flattenedData,
            $columnHeaders,
            $rowHeaders
        ];
    }
}
