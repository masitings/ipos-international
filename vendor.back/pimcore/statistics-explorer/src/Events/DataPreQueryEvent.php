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

class DataPreQueryEvent extends AbstractStatisticsDataEvent
{
    /**
     * @var mixed
     */
    protected $query;

    /**
     * @param string $context
     * @param Configuration|null $configuration
     * @param string $dataSourceName
     * @param string $statisticsMode
     * @param $query
     */
    public function __construct(string $context, ?Configuration $configuration, string $dataSourceName, string $statisticsMode, $query)
    {
        parent::__construct($context, $configuration, $dataSourceName, $statisticsMode);
        $this->query = $query;
    }

    /**
     * @return mixed
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param mixed $query
     */
    public function setQuery($query): void
    {
        $this->query = $query;
    }
}
