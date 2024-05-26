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
use Pimcore\Bundle\StatisticsExplorerBundle\Model\StatisticsResult;

class DataResultEvent extends AbstractStatisticsDataEvent
{
    /**
     * @var StatisticsResult
     */
    protected $statisticsResult;

    /**
     * @param string $context
     * @param Configuration|null $configuration
     * @param string $dataSourceName
     * @param string $statisticsMode
     * @param StatisticsResult $statisticsResult
     */
    public function __construct(string $context, ?Configuration $configuration, string $dataSourceName, string $statisticsMode, StatisticsResult $statisticsResult)
    {
        parent::__construct($context, $configuration, $dataSourceName, $statisticsMode);
        $this->statisticsResult = $statisticsResult;
    }

    /**
     * @return StatisticsResult
     */
    public function getStatisticsResult(): StatisticsResult
    {
        return $this->statisticsResult;
    }

    /**
     * @param StatisticsResult $statisticsResult
     */
    public function setStatisticsResult(StatisticsResult $statisticsResult): void
    {
        $this->statisticsResult = $statisticsResult;
    }
}
