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

namespace Pimcore\Bundle\PortalEngineBundle\Service\StatisticsTracker\Elasticsearch;

use Pimcore\Bundle\StatisticsExplorerBundle\StatisticsTracker\Elasticsearch\AbstractElasticsearchTracker;
use Pimcore\Model\Site;

/**
 * Class PortalUserLoginTracker
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\StatisticsTracker\Elasticsearch
 */
class PortalUserLoginTracker extends AbstractElasticsearchTracker
{
    /**
     * @var string
     */
    protected $indexName;

    /**
     * @return array
     */
    protected function buildMappingArray(): array
    {
        return [
            'timestamp' => ['type' => 'date'],
            'portalId' => ['type' => 'integer'],
            'user' => [
                'type' => 'object',
                'dynamic' => false,
                'properties' => [
                    'id' => ['type' => 'integer'],
                    'count' => ['type' => 'integer']
                ]
            ]
        ];
    }

    /**
     * @return string
     */
    protected function getIndexName(): string
    {
        return $this->indexName;
    }

    /**
     * @param string $indexName
     */
    public function setIndexName(string $indexName): void
    {
        $this->indexName = $indexName;
    }

    /**
     * @param array $parameters
     *
     * @throws \Exception
     */
    public function trackEvent(array $parameters): void
    {
        /** @var array $values */
        $values = [
            'timestamp' => (new \DateTime())->format(\DateTime::ISO8601),
            'portalId' => Site::getCurrentSite()->getRootId(),
            'user' => [
                'id' => $parameters['user']->getId(),
                'count' => 1
            ]
        ];
        $this->doTrackEvent($values);
    }
}
