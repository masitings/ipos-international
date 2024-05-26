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

namespace Pimcore\Bundle\StatisticsExplorerBundle\Service;

use Pimcore\Bundle\StatisticsExplorerBundle\Entity\Configuration;
use Pimcore\Bundle\StatisticsExplorerBundle\Events\DataResultEvent;
use Pimcore\Bundle\StatisticsExplorerBundle\Events\StatisticsServiceInitEvent;
use Pimcore\Bundle\StatisticsExplorerBundle\Model\FieldsCollection;
use Pimcore\Bundle\StatisticsExplorerBundle\Model\StatisticsResult;
use Pimcore\Bundle\StatisticsExplorerBundle\StatisticsStorageAdapter\StatisticsStorageAdapterInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class StatisticsService
{
    /**
     * @var ?StatisticsStorageAdapterInterface[]
     */
    protected $dataSourceAdapters;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var bool
     */
    protected $initialized = false;

    /**
     * @param StatisticsStorageAdapterInterface[]|null $dataSourceAdapters
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(?array $dataSourceAdapters, EventDispatcherInterface $eventDispatcher)
    {
        $this->dataSourceAdapters = $dataSourceAdapters;
        $this->eventDispatcher = $eventDispatcher;
    }

    protected function init()
    {
        if ($this->initialized === false) {
            $event = new StatisticsServiceInitEvent($this);
            $this->eventDispatcher->dispatch($event);

            $this->initialized = true;
        }
    }

    /**
     * @param string $dataSourceName
     * @param string $context
     * @param StatisticsStorageAdapterInterface $dataSourceAdapter
     */
    public function addDataSourceAdapter(string $dataSourceName, string $context, StatisticsStorageAdapterInterface $dataSourceAdapter)
    {
        $this->dataSourceAdapters[$context][$dataSourceName] = $dataSourceAdapter;
    }

    /**
     * @param string $context
     * @param string $dataSourceName
     * @param string $statisticMode
     * @param array $rows
     * @param array $columns
     * @param array $filters
     * @param array $fieldSettings
     * @param Configuration|null $configuration
     *
     * @return StatisticsResult
     */
    public function getStatisticsData(string $context, string $dataSourceName, string $statisticMode, array $rows, array $columns, array $filters, array $fieldSettings, ?Configuration $configuration): StatisticsResult
    {
        $this->init();
        if ($this->dataSourceAdapters[$context][$dataSourceName] ?? null) {
            $statisticsResult = $this->dataSourceAdapters[$context][$dataSourceName]->getStatisticsData($statisticMode, $rows, $columns, $filters, $fieldSettings, $dataSourceName, $context, $configuration);

            $event = new DataResultEvent($context, $configuration, $dataSourceName, $statisticMode, $statisticsResult);
            $this->eventDispatcher->dispatch($event);
            $statisticsResult = $event->getStatisticsResult();

            return $statisticsResult;
        }

        throw new \Exception('Unknown data source');
    }

    /**
     * @param string $context
     * @param string $dataSourceName
     *
     * @return FieldsCollection
     */
    public function getFieldsForDatasource(string $context, string $dataSourceName): FieldsCollection
    {
        $this->init();
        if ($this->dataSourceAdapters[$context][$dataSourceName] ?? null) {
            return $this->dataSourceAdapters[$context][$dataSourceName]->getFieldsForDatasource();
        }

        throw new \Exception('Unknown data source');
    }

    /**
     * @param string $context
     * @param string $dataSourceName
     * @param string $statisticMode
     * @param array $rows
     * @param array $columns
     *
     * @return array
     */
    public function getFieldSettings(string $context, string $dataSourceName, string $statisticMode, array $rows, array $columns): array
    {
        $this->init();
        if ($this->dataSourceAdapters[$context][$dataSourceName] ?? null) {
            return $this->dataSourceAdapters[$context][$dataSourceName]->getFieldSettings($statisticMode, $rows, $columns);
        }

        throw new \Exception('Unknown data source');
    }

    /**
     * @param string $context
     *
     * @return array
     */
    public function getDataSources(string $context): array
    {
        $this->init();

        $sources = [];

        foreach ($this->dataSourceAdapters[$context] as $name => $adapter) {
            $sources[] = [
                'value' => $name,
                'label' => $adapter->getLabel() ?: $name,
                'type' => $adapter->getType()
            ];
        }

        usort($sources, function ($item1, $item2) {
            return strcmp($item1['label'], $item2['label']);
        });

        return $sources;
    }
}
