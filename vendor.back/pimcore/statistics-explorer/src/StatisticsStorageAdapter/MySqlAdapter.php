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

use Doctrine\DBAL\Connection;
use Pimcore\Bundle\StatisticsExplorerBundle\Entity\Configuration;
use Pimcore\Bundle\StatisticsExplorerBundle\Events\DataFilterModificationEvent;
use Pimcore\Bundle\StatisticsExplorerBundle\Model\FieldsCollection;
use Pimcore\Bundle\StatisticsExplorerBundle\Model\StatisticsResult;
use Pimcore\Bundle\StatisticsExplorerBundle\StatisticsStorageAdapter\Worker\MySqlListWorker;
use Pimcore\Bundle\StatisticsExplorerBundle\StatisticsStorageAdapter\Worker\MySqlStatisticWorker;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class MySqlAdapter implements StatisticsStorageAdapterInterface
{
    const TYPE = 'MYSQL';

    /**
     * @var string
     */
    protected $tableName;

    /**
     * @var Connection
     */
    protected $dbConnection;

    /**
     * @var MySqlListWorker
     */
    protected $workerList;

    /**
     * @var MySqlStatisticWorker
     */
    protected $workerStatistic;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var string
     */
    protected $label;

    /**
     * MySqlAdapter constructor.
     *
     * @param string $tableName
     * @param Connection $dbConnection
     * @param MySqlListWorker $workerList
     * @param MySqlStatisticWorker $workerStatistic
     * @param EventDispatcherInterface $eventDispatcher
     * @param string|null $label
     */
    public function __construct(string $tableName, Connection $dbConnection, MySqlListWorker $workerList, MySqlStatisticWorker $workerStatistic, EventDispatcherInterface $eventDispatcher, string $label = null)
    {
        $this->tableName = $tableName;
        $this->dbConnection = $dbConnection;
        $this->workerList = $workerList;
        $this->workerStatistic = $workerStatistic;
        $this->eventDispatcher = $eventDispatcher;
        $this->label = $label;
    }

    public function getType(): string
    {
        return self::TYPE;
    }

    /**
     * @param array $filters
     *
     * @return array
     */
    protected function buildFilters(array $filters): array
    {
        $operatorMapping = [
            '=' => '=',
            '!=' => '!=',
            '<' => '<',
            '<=' => '<=',
            '>' => '>',
            '>=' => '>=',
            'like' => 'LIKE',
        ];

        $filterParts = [];
        $connection = $this->dbConnection;

        foreach ($filters as $filter) {
            if ($filter['field']) {
                $filterParts[] = $connection->quoteIdentifier($filter['field']) . ' ' . $operatorMapping[$filter['operator']] . ' ' . $connection->quote($filter['filter']);
            }
        }

        return $filterParts;
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
        $connection = $this->dbConnection;

        $where = '';
        $filterParts = $this->buildFilters($filters);

        $event = new DataFilterModificationEvent($context, $configuration, $dataSourceName, $statisticMode, $filterParts);
        $this->eventDispatcher->dispatch($event);
        $filterParts = $event->getFilter();

        if ($filterParts) {
            $where = 'WHERE ' . implode(' AND ', $filterParts);
        }

        $fields = array_merge($rows, $columns);

        $orderByFields = [];
        foreach ($fields as $field) {
            if (isset($fieldSettings[$field])) {
                if (!empty($fieldSettings[$field]['order'])) {
                    $orderByFields[] = $connection->quoteIdentifier($field) . ' ' . $fieldSettings[$field]['order'];
                }
                if (!empty($fieldSettings[$field]['order_count'])) {
                    $orderByFields[] = $connection->quoteIdentifier('__COUNT') . ' ' . $fieldSettings[$field]['order_count'];
                }
            }
        }

        $fields = array_map(function ($value) use ($connection) {
            return $connection->quoteIdentifier($value);
        }, $fields);

        $orderBy = '';
        if ($orderByFields) {
            $orderBy = 'ORDER BY ' . implode(', ', $orderByFields) . '';
        }

        if ($statisticMode === self::STATISTICS_MODE_STATISTIC) {
            list($flattenedData, $columnHeaders, $rowHeaders) = $this->workerStatistic->loadData($connection, $rows, $columns, $fields, $fieldSettings, $this->tableName, $where, $orderBy, $dataSourceName, $context, $configuration);
        }

        if ($statisticMode === self::STATISTICS_MODE_LIST) {
            $rowHeaders = [];
            list($flattenedData, $columnHeaders) = $this->workerList->loadData($connection, $columns, $fields, $fieldSettings, $this->tableName, $where, $orderBy, $dataSourceName, $context, $configuration);
        }

        return new StatisticsResult(
            $flattenedData,
            $columnHeaders,
            $rowHeaders
        );
    }

    protected function guessTypeGroup(string $type): string
    {
        if (preg_match('#^(int|tinyint|smallint|mediumint|bigint|double|float|decimal)#', $type) === 1) {
            return 'numeric';
        }
        if (preg_match('#^(char|varchar|text|longtext)#', $type) === 1) {
            return 'text';
        }
        if (preg_match('#^(date|datetime|timestamp)#', $type) === 1) {
            return 'date';
        }

        return 'default';
    }

    public function getFieldsForDatasource(): FieldsCollection
    {
        $columns = $this->dbConnection->fetchAllAssociative("DESCRIBE {$this->dbConnection->quoteIdentifier($this->tableName)}");

        $fields = [];
        foreach ($columns as $column) {
            $fields[] = [
                'value' => $column['Field'],
                'label' => $column['Field'],
                'typeGroup' => $this->guessTypeGroup($column['Type'])
            ];
        }

        $operators = [
            'default' => [
                ['label' => '=', 'value' => '=', 'needsFilterValue' => true],
                ['label' => '!=', 'value' => '!=', 'needsFilterValue' => true]
            ],
            'text' => [
                ['label' => '=', 'value' => '=', 'needsFilterValue' => true],
                ['label' => '!=', 'value' => '!=', 'needsFilterValue' => true],
                ['label' => 'like', 'value' => 'like', 'needsFilterValue' => true]
            ],
            'numeric' => [
                ['label' => '=', 'value' => '=', 'needsFilterValue' => true],
                ['label' => '!=', 'value' => '!=', 'needsFilterValue' => true],
                ['label' => '>', 'value' => '>', 'needsFilterValue' => true],
                ['label' => '>=', 'value' => '>=', 'needsFilterValue' => true],
                ['label' => '<', 'value' => '<', 'needsFilterValue' => true],
                ['label' => '<=', 'value' => '<=', 'needsFilterValue' => true]
            ],
            'date' => [
                ['label' => '=', 'value' => '=', 'needsFilterValue' => true],
                ['label' => '!=', 'value' => '!=', 'needsFilterValue' => true],
                ['label' => '>', 'value' => '>', 'needsFilterValue' => true],
                ['label' => '>=', 'value' => '>=', 'needsFilterValue' => true],
                ['label' => '<', 'value' => '<', 'needsFilterValue' => true],
                ['label' => '<=', 'value' => '<=', 'needsFilterValue' => true]
            ]
        ];

        return new FieldsCollection($fields, $operators);
    }

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
