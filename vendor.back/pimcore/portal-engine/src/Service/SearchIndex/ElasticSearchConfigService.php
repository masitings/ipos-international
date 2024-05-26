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

namespace Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

/**
 * Class ElasticSearchConfigService
 */
class ElasticSearchConfigService
{
    use LoggerAwareTrait;

    /** @var string */
    protected $host = '';
    /** @var string */
    protected $index_prefix = '';
    /** @var array */
    protected $indexSettings = [];
    /** @var array */
    protected $searchSettings = [];
    /** @var array */
    protected $connectionParams = [];

    /**
     * ElasticSearchConfigService constructor.
     *
     * @param string $host
     * @param string $index_prefix
     * @param $indexSettings
     * @param $searchSettings
     * @param array $clientParams
     */
    public function __construct(string $host, string $index_prefix, $indexSettings, $searchSettings, array $connectionParams = [])
    {
        $this->host = $host;
        $this->index_prefix = $index_prefix;
        $this->indexSettings = $indexSettings;
        $this->searchSettings = $searchSettings;
        $this->connectionParams = $connectionParams;
    }

    /**
     * returns index name for given class name
     *
     * @param $name string
     *
     * @return string
     */
    public function getIndexName($name)
    {
        return $this->getIndexPrefix() . strtolower($name);
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return string
     */
    public function getIndexPrefix(): string
    {
        return $this->index_prefix;
    }

    /**
     * @return array
     */
    public function getIndexSettings()
    {
        return $this->indexSettings;
    }

    /**
     * @return array
     */
    public function getSearchSettings()
    {
        return $this->searchSettings;
    }

    /**
     * @return array
     */
    public function getConnectionParams(): array
    {
        return $this->connectionParams;
    }

    /**
     * @return int
     */
    public function getMaxSynchronousChildrenRenameLimit(): int
    {
        return $this->searchSettings['max_synchronous_children_rename_limit'] ?? 0;
    }

    /**
     * @return LoggerInterface|null
     */
    public function getLogger(): ?LoggerInterface
    {
        return $this->logger;
    }
}
