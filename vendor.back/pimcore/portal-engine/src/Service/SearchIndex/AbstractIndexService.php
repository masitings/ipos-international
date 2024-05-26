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

use Elasticsearch\Client;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use Pimcore\Bundle\PortalEngineBundle\Entity\CollectionItem;
use Pimcore\Bundle\PortalEngineBundle\Entity\PublicShareItem;
use Pimcore\Bundle\PortalEngineBundle\Enum\ElasticSearchFields;
use Pimcore\Bundle\PortalEngineBundle\Service\Element\NameExtractorService;
use Pimcore\Bundle\PortalEngineBundle\Service\Entity\EntityManagerService;
use Pimcore\Bundle\PortalEngineBundle\Traits\LoggerAware;
use Pimcore\Model\Element\ElementInterface;
use Pimcore\Model\Element\Service;
use Pimcore\Model\Element\Tag;
use Pimcore\Tool;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Class AbstractIndexService
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex
 */
abstract class AbstractIndexService implements IndexServiceInterface
{
    use LoggerAware;

    /** @var Client */
    protected $esClient;
    /** @var array */
    protected $coreFieldsConfig = [];

    /** @var LoggerInterface */
    protected $logger;
    /** @var EventDispatcherInterface */
    protected $eventDispatcher;
    /** @var ElasticSearchConfigService */
    protected $elasticSearchConfigService;
    /** @var NameExtractorService */
    protected $nameExtractorService;
    /** @var EntityManagerService */
    protected $entityManagerService;

    /** @var bool */
    protected $performIndexRefresh = false;

    /**
     * AbstractIndexService constructor.
     *
     * @param LoggerInterface $logger
     * @param EventDispatcherInterface $eventDispatcher
     * @param ElasticSearchConfigService $elasticSearchConfigService
     * @param NameExtractorService $nameExtractorService
     * @param EntityManagerService $entityManagerService
     */
    public function __construct(
        LoggerInterface $logger,
        EventDispatcherInterface $eventDispatcher,
        ElasticSearchConfigService $elasticSearchConfigService,
        NameExtractorService $nameExtractorService,
        EntityManagerService $entityManagerService
    ) {
        $this->logger = $logger;
        $this->eventDispatcher = $eventDispatcher;
        $this->elasticSearchConfigService = $elasticSearchConfigService;
        $this->nameExtractorService = $nameExtractorService;
        $this->entityManagerService = $entityManagerService;
    }

    /**
     * @param Client $esClient
     * @required
     */
    public function setEsClient(Client $esClient)
    {
        $this->esClient = $esClient;
    }

    /**
     * @param string $indexName
     *
     * @return array
     */
    public function refreshIndex(string $indexName): array
    {
        return $this->esClient
            ->indices()
            ->refresh(['index' => $indexName]);
    }

    public function getCurrentIndexFullPath(ElementInterface $element, string $indexName): ?string
    {
        $result = $this->esClient->search(
            [
                'index' => $indexName,
                'body' => [
                    '_source' => [ElasticSearchFields::SYSTEM_FIELDS . '.' . ElasticSearchFields::SYSTEM_FIELDS_FULL_PATH],
                    'query' => [
                        'term' => [
                            ElasticSearchFields::SYSTEM_FIELDS . '.' . ElasticSearchFields::SYSTEM_FIELDS_ID =>
                                $element->getId(),
                        ],
                    ],
                ],
            ]
        );

        return $result['hits']['hits'][0]['_source']['system_fields']['fullPath'] ?? null;
    }

    public function rewriteChildrenIndexPaths(ElementInterface $element, string $indexName, string $oldFullPath)
    {
        $pathLevels = explode('/', $element->getRealFullPath());

        $countResult = $this->esClient->search([
            'index' => $indexName,
            'track_total_hits' => true,
            'rest_total_hits_as_int' => true,
            'body' => [
                'query' => [
                    'term' => [
                        ElasticSearchFields::SYSTEM_FIELDS . '.' . ElasticSearchFields::SYSTEM_FIELDS_FULL_PATH
                        => $oldFullPath
                    ],
                ],
                'size' => 0,
            ]
        ]);

        $countResult = $countResult['hits']['total'] ?? 0;

        if ($countResult > $this->elasticSearchConfigService->getMaxSynchronousChildrenRenameLimit()) {
            $msg = sprintf(
                'Direct rewrite of children paths in elasticsearch was skipped as more than %s items need an update (%s items). The index will be updated asynchronously via index update queue command cronjob.',
                $this->elasticSearchConfigService->getMaxSynchronousChildrenRenameLimit(),
                $countResult
            );
            $this->logger->info(
                $msg
            );

            return;
        }

        $query = [
            'index' => $indexName,
            'refresh' => true,
            'conflicts' => 'proceed',
            'body' => [

                'script' => [
                    'lang' => 'painless',
                    'source' => '
                        String currentPath = "";
                            if(ctx._source.system_fields.path.length() >= params.currentPath.length()) {
                               currentPath = ctx._source.system_fields.path.substring(0,params.currentPath.length());
                            }
                            if(currentPath == params.currentPath) {
                                String subPath = ctx._source.system_fields.path.substring(params.currentPath.length());
                                ctx._source.system_fields.path = params.newPath + subPath;

                                String subFullPath = ctx._source.system_fields.fullPath.substring(params.currentPath.length());
                                ctx._source.system_fields.fullPath = params.newPath + subFullPath;

                                for (int i = 0; i < ctx._source.system_fields.pathLevels.length; i++) {


                                  if(ctx._source.system_fields.pathLevels[i].level == params.changePathLevel) {

                                    ctx._source.system_fields.pathLevels[i].name = params.newPathLevelName;
                                  }
                                }
                            }
                            ctx._source.system_fields.checksum = 0
                   ',

                    'params' => [
                        'currentPath' => $oldFullPath . '/',
                        'newPath' => $element->getRealFullPath() . '/',
                        'changePathLevel' => sizeof($pathLevels) - 1,
                        'newPathLevelName' => end($pathLevels),
                    ]
                ],

                'query' => [
                    'term' => [
                        ElasticSearchFields::SYSTEM_FIELDS . '.' . ElasticSearchFields::SYSTEM_FIELDS_FULL_PATH
                        => $oldFullPath
                    ],
                ]
            ]
        ];

        $this->esClient->updateByQuery($query);
    }

    /**
     * @param array $coreFieldsConfig
     */
    abstract public function setCoreFieldsConfig(array $coreFieldsConfig);

    /**
     * @param string|null $fieldName
     *
     * @return array
     */
    public function getCoreFieldsConfig($fieldName = null)
    {
        if ($fieldName !== null && array_key_exists($fieldName, $this->coreFieldsConfig)) {
            return $this->coreFieldsConfig[$fieldName];
        }

        return $this->coreFieldsConfig;
    }

    /**
     * @param string $indexName
     * @param null $mappings
     *
     * @return $this
     */
    protected function doCreateIndex($indexName, array $mappings = null)
    {
        $this->doDeleteIndex($indexName);

        try {
            $this->logger->info("Creating index $indexName");

            $body = [
                'settings' => $this->elasticSearchConfigService->getIndexSettings()
            ];

            if ($mappings) {
                $body['mappings']['properties'] = $mappings['body']['properties'];
                $body['mappings']['_source'] = $mappings['body']['_source'];
            }

            $response = $this->esClient->indices()->create(
                [
                    'index' => $indexName,
                    'include_type_name' => false,
                    'body' => $body
                ]
            );
            $this->logger->debug(json_encode($response));
        } catch (\Exception $e) {
            $this->logger->error($e);
        }

        return $this;
    }

    /**
     * @param string $indexName
     *
     * @return $this
     */
    protected function doDeleteIndex($indexName)
    {
        try {
            $this->logger->info("Deleting index $indexName");
            $response = $this->esClient->indices()->delete(['index' => $indexName]);
            $this->logger->debug(json_encode($response));
        } catch (Missing404Exception $e) {
            $this->logger->info('Cannot delete index ' . $indexName . ' from es index because not found.');
        }

        return $this;
    }

    protected function getCurrentIndexVersion(string $indexName): string
    {
        $result = $this->esClient->indices()->getAlias([
            'name' => $indexName,
        ]);

        $aliasIndexName = array_key_first($result);
        $nameParts = explode('-', $aliasIndexName);

        return end($nameParts);
    }

    protected function reindex(string $indexName, array $mapping)
    {
        $currentIndexVersion = $this->getCurrentIndexVersion($indexName);

        $oldIndexName = $indexName . '-' . $currentIndexVersion;
        $newIndexName = $indexName . '-' . ($currentIndexVersion == 'even' ? 'odd' : 'even');
        $this->doCreateIndex($indexName . '-' . ($currentIndexVersion == 'even' ? 'odd' : 'even'), $mapping);

        $body = [
            'source' => [
                'index' => $oldIndexName,

            ],
            'dest' => [
                'index' => $newIndexName,
            ],
        ];

        $this->esClient->reindex([
            'body' => $body,
        ]);

        $this->switchIndexAliasAndCleanup($indexName, $oldIndexName, $newIndexName);
    }

    protected function switchIndexAliasAndCleanup(string $aliasName, string $oldIndexName, string $newIndexName)
    {
        $params['body'] = [
            'actions' => [
                [
                    'remove' => [
                        'index' => '*',
                        'alias' => $aliasName,
                    ],
                ],
                [
                    'add' => [
                        'index' => $newIndexName,
                        'alias' => $aliasName,
                    ],
                ],
            ],
        ];
        $result = $this->esClient->indices()->updateAliases($params);
        if (!$result['acknowledged']) {
            //set current index version
            throw new \Exception('Switching Alias failed for ' . $newIndexName);
        }

        //delete old indices
        $this->doDeleteIndex($oldIndexName);
    }

    /**
     * @return array
     */
    protected function extractSystemFieldsMapping()
    {
        /** @var array $mappingProperties */
        $mappingProperties = [];

        $mappingProperties[ElasticSearchFields::SYSTEM_FIELDS]['properties'] = array_map(
            function ($fieldProperties) {
                $mapping = [
                    'type' => $fieldProperties['type'],
                ];
                if (!empty($fieldProperties['analyzer'])) {
                    $mapping['analyzer'] = $fieldProperties['analyzer'];
                }
                if (!empty($fieldProperties['properties'])) {
                    $mapping['properties'] = $fieldProperties['properties'];
                }
                if (!empty($fieldProperties['fields'])) {
                    $mapping['fields'] = $fieldProperties['fields'];
                }

                return $mapping;
            },
            $this->getCoreFieldsConfig()
        );

        $nameMapping = ['type' => 'object', 'properties' => []];
        foreach (Tool::getValidLanguages() as $language) {
            $nameMapping['properties'][$language] = [
                'type' => 'keyword'
            ];
        }

        $mappingProperties[ElasticSearchFields::SYSTEM_FIELDS]['properties'][ElasticSearchFields::SYSTEM_FIELDS_NAME] = $nameMapping;

        return $mappingProperties;
    }

    /**
     * @param string $path
     *
     * @return array
     */
    protected function extractPathLevels(string $path): array
    {
        $levels = explode('/', rtrim($path, '/'));
        unset($levels[0]);

        $result = [];
        foreach ($levels as $level => $name) {
            $result[] = [
                'level' => $level,
                'name' => $name,
            ];
        }

        return $result;
    }

    /**
     * @param ElementInterface $element
     *
     * @return array
     */
    protected function extractTagIds(ElementInterface $element): array
    {
        $tag = new Tag();
        $tags = $tag->getDao()->getTagsForElement(Service::getElementType($element), $element->getId());

        $ids = [];
        foreach ($tags as $tag) {
            $ids[] = $tag->getId();
        }

        return $ids;
    }

    /**
     * @param ElementInterface $element
     *
     * @return string[]
     */
    protected function getCollectionIdsByElement(ElementInterface $element): array
    {
        /** @var string[] $collectionIds */
        $collectionIds = [];
        /** @var CollectionItem[] $collectionItems */
        $collectionItems = $this->entityManagerService->getManager()->getRepository(CollectionItem::class)->findBy([
            'elementId' => $element->getId(),
            'elementType' => Service::getElementType($element)
        ]);

        foreach ($collectionItems as $collectionItem) {
            $collectionIds[] = (string)$collectionItem->getCollection()->getId();
        }

        return $collectionIds;
    }

    /**
     * @param ElementInterface $element
     *
     * @return string[]
     */
    protected function getPublicShareIdsByElement(ElementInterface $element): array
    {
        /** @var string[] $publicShareIds */
        $publicShareIds = [];
        /** @var PublicShareItem[] $publicShareItems */
        $publicShareItems = $this->entityManagerService->getManager()->getRepository(PublicShareItem::class)->findBy([
            'elementId' => $element->getId(),
            'elementType' => Service::getElementType($element)
        ]);

        foreach ($publicShareItems as $publicShareItem) {
            $publicShareIds[] = (string)$publicShareItem->getPublicShare()->getId();
        }

        return $publicShareIds;
    }

    /**
     * @return bool
     */
    public function isPerformIndexRefresh(): bool
    {
        return $this->performIndexRefresh;
    }

    /**
     * @param bool $performIndexRefresh
     *
     * @return $this
     */
    public function setPerformIndexRefresh(bool $performIndexRefresh)
    {
        $this->performIndexRefresh = $performIndexRefresh;

        return $this;
    }
}
