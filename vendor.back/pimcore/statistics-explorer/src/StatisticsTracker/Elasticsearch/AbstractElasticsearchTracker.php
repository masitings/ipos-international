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

namespace Pimcore\Bundle\StatisticsExplorerBundle\StatisticsTracker\Elasticsearch;

use Pimcore\Bundle\StatisticsExplorerBundle\Tools\ElasticsearchClientFactory;

abstract class AbstractElasticsearchTracker
{
    /**
     * @var ElasticsearchClientFactory
     */
    protected $clientFactory;

    /**
     * AbstractElasticsearchTracker constructor.
     *
     * @param ElasticsearchClientFactory $clientFactory
     */
    public function __construct(ElasticsearchClientFactory $clientFactory)
    {
        $this->clientFactory = $clientFactory;
    }

    /**
     * @return \Elasticsearch\Client
     */
    protected function getElasticSearchClient()
    {
        return $this->clientFactory->getESClient();
    }

    /**
     * @return array
     */
    abstract protected function buildMappingArray(): array;

    /**
     * @return string
     */
    abstract protected function getIndexName(): string;

    /**
     * @return string
     */
    protected function getFullIndexName(): string
    {
        return $this->getIndexName() . '__' . date('Y_m');
    }

    abstract public function trackEvent(array $parameters): void;

    /**
     * @param array $values
     */
    protected function doTrackEvent(array $values)
    {
        $client = $this->clientFactory->getESClient();

        $indexExists = $client->indices()->exists([
            'index' => $this->getFullIndexName()
        ]);
        if (!$indexExists) {
            $this->createIndex();
        }

        $client->index([
            'index' => $this->getFullIndexName(),
            'type' => '_doc',
            'body' => $values
        ]);
    }

    /**
     * @return array
     */
    protected function createIndexSettings()
    {
        return [];
    }

    protected function createIndex()
    {
        $client = $this->clientFactory->getESClient();

        $indexParams = [
            'index' => $this->getFullIndexName(),
            'include_type_name' => false,
            'body' => [
                'mappings' => [
                    'properties' => $this->buildMappingArray()
                ]
            ]
        ];

        $indexSettings = $this->createIndexSettings();
        if (!empty($indexSettings)) {
            $indexParams['body']['settings'] = $indexSettings;
        }

        $client->indices()->create($indexParams);

        $client->indices()->updateAliases([
            'body' => [
                'actions' => [
                    'add' => [
                        'index' => $this->getFullIndexName(),
                        'alias' => $this->getIndexName()
                    ]
                ]
            ]
        ]);
    }
}
