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

abstract class AbstractStatisticsDataEvent
{
    /**
     * @var string
     */
    protected $context;

    /**
     * @var ?Configuration
     */
    protected $configuration;

    /**
     * @var string
     */
    protected $dataSourceName;

    /**
     * @var string
     */
    protected $statisticsMode;

    /**
     * @param string $context
     * @param Configuration|null $configuration
     * @param string $dataSourceName
     * @param string $statisticsMode
     */
    public function __construct(string $context, ?Configuration $configuration, string $dataSourceName, string $statisticsMode)
    {
        $this->context = $context;
        $this->configuration = $configuration;
        $this->dataSourceName = $dataSourceName;
        $this->statisticsMode = $statisticsMode;
    }

    /**
     * @return string
     */
    public function getContext(): string
    {
        return $this->context;
    }

    /**
     * @param string $context
     */
    public function setContext(string $context): void
    {
        $this->context = $context;
    }

    /**
     * @return Configuration|null
     */
    public function getConfiguration(): ?Configuration
    {
        return $this->configuration;
    }

    /**
     * @param Configuration|null $configuration
     */
    public function setConfiguration(?Configuration $configuration): void
    {
        $this->configuration = $configuration;
    }

    /**
     * @return string
     */
    public function getDataSourceName(): string
    {
        return $this->dataSourceName;
    }

    /**
     * @param string $dataSourceName
     */
    public function setDataSourceName(string $dataSourceName): void
    {
        $this->dataSourceName = $dataSourceName;
    }

    /**
     * @return string
     */
    public function getStatisticsMode(): string
    {
        return $this->statisticsMode;
    }
}
