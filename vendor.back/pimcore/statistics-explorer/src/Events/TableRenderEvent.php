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

class TableRenderEvent extends AbstractStatisticsDataEvent
{
    /**
     * @var string
     */
    protected $template;

    /**
     * @var array
     */
    protected $parameters;

    /**
     * @param string $context
     * @param Configuration|null $configuration
     * @param string $dataSourceName
     * @param string $statisticsMode
     * @param string $template
     * @param array $parameters
     */
    public function __construct(string $context, ?Configuration $configuration, string $dataSourceName, string $statisticsMode, string $template, array $parameters)
    {
        parent::__construct($context, $configuration, $dataSourceName, $statisticsMode);
        $this->template = $template;
        $this->parameters = $parameters;
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @param string $template
     */
    public function setTemplate(string $template): void
    {
        $this->template = $template;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     */
    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }
}
