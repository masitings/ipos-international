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

namespace Pimcore\Bundle\StatisticsExplorerBundle\Tools;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Psr\Log\LoggerAwareTrait;

class ElasticsearchClientFactory
{
    use LoggerAwareTrait;

    /**
     * @var Client
     */
    protected $esClient;

    /**
     * @var string[]
     */
    protected $esHosts;

    /**
     * ElasticsearchClientFactory constructor.
     *
     * @param string[] $esHosts
     */
    public function __construct(array $esHosts)
    {
        $this->esHosts = $esHosts;
    }

    /**
     * @return Client
     */
    public function getESClient()
    {
        if (empty($this->esClient)) {
            $builder = ClientBuilder::create();
            $builder->setHosts($this->esHosts);
            $builder->setLogger($this->logger);
            $builder->setConnectionParams(['client' => ['ignore' => [404]]]);
            $this->esClient = $builder->build();
        }

        return $this->esClient;
    }
}
