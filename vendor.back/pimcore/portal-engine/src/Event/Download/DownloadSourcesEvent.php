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

namespace Pimcore\Bundle\PortalEngineBundle\Event\Download;

use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataPoolConfigInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadConfig;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Fires after the download sources got determined.
 * Can be used add or remove download providers.
 */
class DownloadSourcesEvent extends Event
{
    private $dataPoolConfig;
    private $downloadConfig;
    private $source;
    private $sources;

    public function __construct(DataPoolConfigInterface $dataPoolConfig, DownloadConfig $downloadConfig, $source, array $sources = [])
    {
        $this->dataPoolConfig = $dataPoolConfig;
        $this->downloadConfig = $downloadConfig;
        $this->source = $source;
        $this->sources = $sources;
    }

    /**
     * @return DataPoolConfigInterface
     */
    public function getDataPoolConfig(): DataPoolConfigInterface
    {
        return $this->dataPoolConfig;
    }

    /**
     * @param DataPoolConfigInterface $dataPoolConfig
     */
    public function setDataPoolConfig(DataPoolConfigInterface $dataPoolConfig): void
    {
        $this->dataPoolConfig = $dataPoolConfig;
    }

    /**
     * @return DownloadConfig
     */
    public function getDownloadConfig(): DownloadConfig
    {
        return $this->downloadConfig;
    }

    /**
     * @param DownloadConfig $downloadConfig
     */
    public function setDownloadConfig(DownloadConfig $downloadConfig): void
    {
        $this->downloadConfig = $downloadConfig;
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param mixed $source
     */
    public function setSource($source): void
    {
        $this->source = $source;
    }

    /**
     * @return array
     */
    public function getSources(): array
    {
        return $this->sources;
    }

    /**
     * @param array $sources
     */
    public function setSources(array $sources): void
    {
        $this->sources = $sources;
    }
}
