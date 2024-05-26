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

namespace Pimcore\Bundle\PortalEngineBundle\Tools;

use Elasticsearch\Client;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\ElasticSearchConfigService;

/**
 * Class EsClientFactory
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Tools
 */
class EsClientFactory
{
    /**
     * @var Client
     */
    protected static $esClient = null;

    /**
     * @return Client
     */
    public static function getESClient(ElasticSearchConfigService $esConfigService)
    {
        if (null === static::$esClient) {

            /** @var string $elasticSearchHost */
            $elasticSearchHost = $esConfigService->getHost();

            $connectionParams = $esConfigService->getConnectionParams();
            $connectionParams['client']['ignore'][] = 404;

            static::$esClient = \Elasticsearch\ClientBuilder::create()
                ->setHosts([$elasticSearchHost])
                ->setLogger($esConfigService->getLogger())
                ->setConnectionParams($connectionParams)
                ->build();
        }

        return static::$esClient;
    }
}
