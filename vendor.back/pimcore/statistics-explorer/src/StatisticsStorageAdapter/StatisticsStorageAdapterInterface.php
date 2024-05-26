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
use Pimcore\Bundle\StatisticsExplorerBundle\Model\FieldsCollection;
use Pimcore\Bundle\StatisticsExplorerBundle\Model\StatisticsResult;

interface StatisticsStorageAdapterInterface
{
    const STATISTICS_MODE_STATISTIC = 'statistic';
    const STATISTICS_MODE_LIST = 'list';

    /**
     * @return string
     */
    public function getType(): string;

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
    public function getStatisticsData(string $statisticMode, array $rows, array $columns, array $filters, array $fieldSettings, string $dataSourceName, string $context, ?Configuration $configuration): StatisticsResult;

    /**
     * @return FieldsCollection
     */
    public function getFieldsForDatasource(): FieldsCollection;

    /**
     * @param string $statisticMode
     * @param array $rows
     * @param array $columns
     *
     * @return array
     */
    public function getFieldSettings(string $statisticMode, array $rows, array $columns): array;

    /**
     * @return string|null
     */
    public function getLabel(): ?string;
}
