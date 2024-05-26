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

namespace Pimcore\Bundle\StatisticsExplorerBundle\Events;

use Pimcore\Bundle\StatisticsExplorerBundle\Entity\Configuration;

class DataFilterModificationEvent extends AbstractStatisticsDataEvent
{
    /**
     * @var array
     */
    protected $filter;

    /**
     * @param string $context
     * @param Configuration|null $configuration
     * @param string $dataSourceName
     * @param string $statisticsMode
     * @param array $filter
     */
    public function __construct(string $context, ?Configuration $configuration, string $dataSourceName, string $statisticsMode, array $filter)
    {
        parent::__construct($context, $configuration, $dataSourceName, $statisticsMode);
        $this->filter = $filter;
    }

    /**
     * @return array
     */
    public function getFilter(): array
    {
        return $this->filter;
    }

    /**
     * @param array $filter
     */
    public function setFilter(array $filter): void
    {
        $this->filter = $filter;
    }
}
