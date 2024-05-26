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

use Pimcore\Bundle\PortalEngineBundle\Enum\ElasticSearchFields;
use Pimcore\Bundle\PortalEngineBundle\Model\DataObject\PortalUserInterface;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\SecurityService;
use Pimcore\Bundle\StatisticsExplorerBundle\StatisticsTracker\Elasticsearch\AbstractElasticsearchTracker;
use Pimcore\Model\User;

/**
 * Class AssetUpdateTracker
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\StatisticsTracker\Elasticsearch
 */
class AssetUpdateTracker extends AbstractElasticsearchTracker
{
    /**
     * @var string
     */
    protected $indexName;

    /** @var SecurityService */
    protected $securityService;

    /**
     * @param SecurityService $securityService
     * @required
     */
    public function setSecurityService(SecurityService $securityService)
    {
        $this->securityService = $securityService;
    }

    /**
     * @return array
     */
    protected function buildMappingArray(): array
    {
        return [
            'timestamp' => ['type' => 'date'],
            'pimcoreUserId' => ['type' => 'long'],
            'userId' => ['type' => 'long'],
            'path' => ['type' => 'keyword'],
            ElasticSearchFields::SYSTEM_FIELDS => [
                'type' => 'object',
                'properties' => [
                    ElasticSearchFields::SYSTEM_FIELDS_FULL_PATH => [
                        'type' => 'text',
                        'analyzer' => 'portal_engine_path_analyzer',
                    ],
                    ElasticSearchFields::SYSTEM_FIELDS_ID => [
                        'type' => 'long',
                    ],
                    ElasticSearchFields::SYSTEM_FIELDS_KEY => [
                        'type' => 'keyword',
                    ],
                ],

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
        /** @var User|null $pimcoreUser */
        $pimcoreUser = $this->securityService->getPimcoreUser();
        /** @var PortalUserInterface|null $portalUser */
        $portalUser = $this->securityService->getPortalUser();

        if ($pimcoreUser || $portalUser) {
            /** @var array $values */
            $values = [
                'timestamp' => (new \DateTime())->format(\DateTime::ISO8601),
                'pimcoreUserId' => $pimcoreUser ? $pimcoreUser->getId() : null,
                'userId' => $portalUser ? $portalUser->getId() : null,
                'path' => $parameters['asset']->getRealFullPath(),
                ElasticSearchFields::SYSTEM_FIELDS => [
                    ElasticSearchFields::SYSTEM_FIELDS_FULL_PATH => $parameters['asset']->getRealFullPath(),
                    ElasticSearchFields::SYSTEM_FIELDS_ID => $parameters['asset']->getId(),
                    ElasticSearchFields::SYSTEM_FIELDS_KEY => $parameters['asset']->getKey(),
                ]
            ];
            $this->doTrackEvent($values);
        }
    }

    /**
     * @return array
     */
    protected function createIndexSettings()
    {
        return [
            'analysis' => [
                'tokenizer' => [
                    'portal_engine_path_tokenizer' => [
                        'type' => 'path_hierarchy',
                    ],
                ],
                'analyzer' => [
                    'portal_engine_path_analyzer' => [
                        'tokenizer' => 'portal_engine_path_tokenizer',
                    ],
                ],
            ],
        ];
    }
}
