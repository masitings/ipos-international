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

use Pimcore\Bundle\StatisticsExplorerBundle\Service\StatisticsService;

class StatisticsServiceInitEvent
{
    /**
     * @var StatisticsService
     */
    protected $statisticsService;

    /**
     * StatisticsServiceInitEvent constructor.
     *
     * @param StatisticsService $statisticsService
     */
    public function __construct(StatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
    }

    /**
     * @return StatisticsService
     */
    public function getStatisticsService(): StatisticsService
    {
        return $this->statisticsService;
    }
}
