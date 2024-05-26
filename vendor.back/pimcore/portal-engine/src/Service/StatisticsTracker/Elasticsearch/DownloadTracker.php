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

use Pimcore\Bundle\PortalEngineBundle\Enum\Download\DownloadContext;
use Pimcore\Bundle\PortalEngineBundle\Enum\ElasticSearchFields;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\Downloadable\DownloadableAsset;
use Pimcore\Bundle\PortalEngineBundle\Service\Download\Generator\OriginalAssetGenerator;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\SecurityService;

/**
 * Class DownloadTracker
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\StatisticsTracker\Elasticsearch
 */
class DownloadTracker extends \Pimcore\Bundle\StatisticsExplorerBundle\StatisticsTracker\Elasticsearch\AbstractElasticsearchTracker
{
    /**
     * @var string
     */
    protected $indexName;

    /**
     * @var SecurityService
     */
    protected $securityService;

    /**
     * @return array
     */
    protected function buildMappingArray(): array
    {
        return [
            'timestamp' => ['type' => 'date'],
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
                ],

            ],
            'path' => ['type' => 'keyword'],
            'context' => ['type' => 'keyword'],
            'thumbnail' => ['type' => 'keyword'],
            'zipId' => ['type' => 'keyword'],
            'userId' => ['type' => 'long'],
            'dataPoolId' => ['type' => 'long'],
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
        $downloadable = $parameters['downloadable'] ?? null;

        if (!$downloadable instanceof DownloadableAsset) {
            return;
        }

        /** @var array $values */
        $values = [
            'timestamp' => (new \DateTime())->format(\DateTime::ISO8601),
            ElasticSearchFields::SYSTEM_FIELDS => [
                ElasticSearchFields::SYSTEM_FIELDS_FULL_PATH => $downloadable->getAsset()->getRealFullPath(),
                ElasticSearchFields::SYSTEM_FIELDS_ID => $downloadable->getAsset()->getId(),
            ],
            'path' => $downloadable->getAsset()->getRealFullPath(),
            'thumbnail' => $downloadable->getThumbnail() ?? OriginalAssetGenerator::ORIGINAL_FORMAT,
            'context' => $parameters['context'] ?? DownloadContext::UNDEFINED,
            'zipId' => $downloadable->getDownloadUniqid(),
            'userId' => $this->securityService->getPortalUser()->isPortalShareUser() ? 0 : $this->securityService->getPortalUser()->getId(),
            'dataPoolId' => $downloadable->getDataPoolConfig() ? $downloadable->getDataPoolConfig()->getId() : 0,
        ];
        $this->doTrackEvent($values);
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

    /**
     * @param SecurityService $securityService
     * @required
     */
    public function setSecurityService(SecurityService $securityService): void
    {
        $this->securityService = $securityService;
    }
}
