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

class LoadConfigurationEvent
{
    /**
     * @var string
     */
    protected $configurationId;

    /**
     * @var string
     */
    protected $context;

    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * LoadConfigurationEvent constructor.
     *
     * @param string $configurationId
     * @param string $context
     */
    public function __construct(string $configurationId, string $context)
    {
        $this->configurationId = $configurationId;
        $this->context = $context;
    }

    /**
     * @return string
     */
    public function getConfigurationId(): string
    {
        return $this->configurationId;
    }

    /**
     * @param string $configurationId
     */
    public function setConfigurationId(string $configurationId): void
    {
        $this->configurationId = $configurationId;
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
     * @return Configuration
     */
    public function getConfiguration(): ?Configuration
    {
        return $this->configuration;
    }

    /**
     * @param Configuration $configuration
     */
    public function setConfiguration(Configuration $configuration): void
    {
        $this->configuration = $configuration;
    }
}
