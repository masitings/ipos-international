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

namespace Pimcore\AssetMetadataClassDefinitionsBundle\Event\Model\Asset;

use Pimcore\AssetMetadataClassDefinitionsBundle\Model\Configuration;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class ConfigurationEvent
 *
 * @package Pimcore\AssetMetadataClassDefinitionsBundle\Event\Model\Asset
 */
class ConfigurationEvent extends Event
{
    /** @var Configuration */
    protected $configuration;

    /**
     * ConfigurationEvent constructor.
     *
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @param Configuration $configuration
     *
     * @return ConfigurationEvent
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;

        return $this;
    }
}
