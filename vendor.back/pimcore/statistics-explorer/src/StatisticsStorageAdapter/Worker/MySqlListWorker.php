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

class MySqlListWorker extends EventDispatcherAwareWorker
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

    /**
     * @param Connection $dbConnection
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
    public function loadData(Connection $dbConnection, array $columns, array $fields, array $fieldSettings, string $tableName, string $where, string $orderBy, string $dataSourceName, string $context, ?Configuration $configuration): array
    {
        $firstColumn = reset($columns);
        $limit = $fieldSettings[$firstColumn]['max'];

        $statement = sprintf(
            'SELECT %s FROM %s %s %s LIMIT %d',
            implode(',', $fields),
            $dbConnection->quoteIdentifier($tableName),
            $where,
            $orderBy,
            $limit
        );

        $event = new DataPreQueryEvent($context, $configuration, $dataSourceName, StatisticsStorageAdapterInterface::STATISTICS_MODE_LIST, $statement);
        $this->eventDispatcher->dispatch($event);
        $statement = $event->getQuery();

        $data = $dbConnection->fetchAllAssociative($statement);

        $columnHeaders = [];
        foreach ($columns as $column) {
            if (isset($fieldSettings[$column]) && isset($fieldSettings[$column]['hidden']) && $fieldSettings[$column]['hidden'] === true) {
                continue;
            }

            $label = $column;
            if ($fieldSettings[$column] && !empty($fieldSettings[$column]['label'])) {
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
            $data,
            $columnHeaders
        ];
    }
}
