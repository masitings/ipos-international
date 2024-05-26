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

namespace Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Asset;

use Elasticsearch\Common\Exceptions\Missing404Exception;
use Pimcore\AssetMetadataClassDefinitionsBundle\Model\ClassDefinition\Data\Data;
use Pimcore\AssetMetadataClassDefinitionsBundle\Model\Collections;
use Pimcore\AssetMetadataClassDefinitionsBundle\Model\Configuration;
use Pimcore\AssetMetadataClassDefinitionsBundle\Service;
use Pimcore\Bundle\PortalEngineBundle\Enum\ElasticSearchFields;
use Pimcore\Bundle\PortalEngineBundle\Enum\ImageThumbnails;
use Pimcore\Bundle\PortalEngineBundle\Event\Asset\ExtractMappingEvent;
use Pimcore\Bundle\PortalEngineBundle\Event\Asset\UpdateIndexDataEvent;
use Pimcore\Bundle\PortalEngineBundle\Service\Asset\SearchIndexFieldDefinitionService;
use Pimcore\Bundle\PortalEngineBundle\Service\Asset\ThumbnailService;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Asset\FieldDefinitionAdapter\FieldDefinitionAdapterInterface;
use Pimcore\Bundle\PortalEngineBundle\Service\Workflow\WorkflowService;
use Pimcore\Model\Asset;

/**
 * Class IndexService
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Asset
 */
class IndexService extends \Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\AbstractIndexService
{
    /** @var SearchIndexFieldDefinitionService */
    protected $fieldDefinitionService;
    /** @var ThumbnailService */
    protected $thumbnailService;
    /** @var WorkflowService */
    protected $workflowService;

    /**
     * @param SearchIndexFieldDefinitionService $fieldDefinitionService
     * @required
     */
    public function setFieldDefinitionService(SearchIndexFieldDefinitionService $fieldDefinitionService)
    {
        $this->fieldDefinitionService = $fieldDefinitionService;
    }

    /**
     * @param ThumbnailService $thumbnailService
     * @required
     */
    public function setThumbnailService(ThumbnailService $thumbnailService)
    {
        $this->thumbnailService = $thumbnailService;
    }

    /**
     * @param WorkflowService $workflowService
     * @required
     */
    public function setWorkflowService(WorkflowService $workflowService)
    {
        $this->workflowService = $workflowService;
    }

    /**
     * @return string
     */
    protected function getIndexName(): string
    {
        return $this->elasticSearchConfigService->getIndexName('asset');
    }

    /**
     * @return string
     */
    protected function getCurrentFullIndexName(): string
    {
        $indexName = $this->getIndexName();
        $currentIndexVersion = $this->getCurrentIndexVersion($indexName);

        return $indexName . '-' . ($currentIndexVersion === 'even' ? 'even' : 'odd');
    }

    /**
     * @return $this
     */
    public function createIndex()
    {
        //create index
        $fullIndexName = $this->getCurrentFullIndexName();
        $this->doCreateIndex($fullIndexName);

        //update alias
        $params['body'] = [
            'actions' => [
                [
                    'add' => [
                        'index' => $fullIndexName,
                        'alias' => $this->getIndexName(),
                    ],
                ],
            ],
        ];
        $this->esClient->indices()->updateAliases($params);

        return $this;
    }

    /**
     * @return $this
     */
    public function deleteIndex()
    {
        $this->doDeleteIndex($this->getCurrentFullIndexName());

        return $this;
    }

    protected function extractSystemFieldsMapping()
    {
        $mappingProperties = parent::extractSystemFieldsMapping();
        $mappingProperties[ElasticSearchFields::SYSTEM_FIELDS]['properties'][ElasticSearchFields::SYSTEM_FIELDS_HAS_WORKFLOW_WITH_PERMISSIONS] = ['type' => 'boolean'];

        return $mappingProperties;
    }

    /**
     * @return array
     */
    public function extractMapping()
    {
        /** @var array $mappingProperties */
        $mappingProperties = $this->extractSystemFieldsMapping();

        foreach (Configuration\Dao::getList(true) as $configuration) {

            /** @var Data[] $fieldDefinitions */
            $fieldDefinitions = [];
            /** @var Data[] $localizedFieldDefinitions */
            $localizedFieldDefinitions = [];

            Service::extractDataDefinitions($configuration->getLayoutDefinitions(), false, $fieldDefinitions, $localizedFieldDefinitions);

            foreach ($fieldDefinitions as $fieldDefinition) {

                /** @var FieldDefinitionAdapterInterface|null $fieldDefinitionAdapter */
                $fieldDefinitionAdapter = $this->fieldDefinitionService->getFieldDefinitionAdapter($fieldDefinition);
                if ($fieldDefinitionAdapter) {
                    list($mappingKey, $mappingEntry) = $fieldDefinitionAdapter->getESMapping();
                    $mappingProperties[ElasticSearchFields::STANDARD_FIELDS]['properties'][$configuration->getName()]['properties'][$mappingKey] = $mappingEntry;
                }
            }

            foreach ($localizedFieldDefinitions as $fieldDefinition) {

                /** @var FieldDefinitionAdapterInterface|null $fieldDefinitionAdapter */
                $fieldDefinitionAdapter = $this->fieldDefinitionService->getFieldDefinitionAdapter($fieldDefinition);
                if ($fieldDefinitionAdapter) {
                    foreach ($fieldDefinitionAdapter->getLocalizedESMapping() as $mappingKey => $mappingEntry) {
                        $mappingProperties[ElasticSearchFields::STANDARD_FIELDS]['properties'][$configuration->getName()]['properties'][$mappingKey] = $mappingEntry;
                    }
                }
            }
        }

        $mappingProperties[ElasticSearchFields::CUSTOM_FIELDS] = [];

        /** @var ExtractMappingEvent $extractMappingEvent */
        $extractMappingEvent = new ExtractMappingEvent($mappingProperties[ElasticSearchFields::CUSTOM_FIELDS]);
        $this->eventDispatcher->dispatch($extractMappingEvent);
        $mappingProperties[ElasticSearchFields::CUSTOM_FIELDS]['properties'] = $extractMappingEvent->getCustomFieldsMapping();

        $mappingParams = [
            'index' => $this->getIndexName(),
            'include_type_name' => false,
            'body' => [
                '_source' => [
                    'enabled' => true
                ],
                'properties' => $mappingProperties
            ]
        ];

        return $mappingParams;
    }

    /**
     * @param bool $forceCreateIndex
     *
     * @return $this
     */
    public function updateMapping($forceCreateIndex = false)
    {
        if ($forceCreateIndex || !$this->esClient->indices()->existsAlias(['name' => $this->getIndexName()])) {
            $this->createIndex();
        }

        try {
            $this->doUpdateMapping();
        } catch (\Exception $e) {
            $this->logger->info($e);
            $this->reindex($this->getIndexName(), $this->extractMapping());
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function doUpdateMapping()
    {
        $mapping = $this->extractMapping();
        $response = $this->esClient->indices()->putMapping($mapping);
        $this->logger->debug(json_encode($response));

        return $this;
    }

    /**
     * @param Asset $asset
     *
     * @return array
     */
    public function getIndexData($asset)
    {
        /** @var array $systemFields */
        $systemFields = $this->getCoreFieldsIndexData($asset);
        /** @var array $standardFields */
        $standardFields = [];
        /** @var array $customFields */
        $customFields = [];

        /** @var Collections|null $collections */
        $collections = Collections::getByAssetId($asset->getId());

        foreach ($collections->getCollections() as $configurationName) {

            /** @var Configuration|null $configuration */
            $configuration = Configuration\Dao::getByName($configurationName);

            if ($configuration) {

                /** @var Data[] $fieldDefinitions */
                $fieldDefinitions = [];
                /** @var Data[] $localizedFieldDefinitions */
                $localizedFieldDefinitions = [];

                Service::extractDataDefinitions($configuration->getLayoutDefinitions(), false, $fieldDefinitions, $localizedFieldDefinitions);

                foreach ($fieldDefinitions as $key => $fieldDefinition) {

                    /** @var FieldDefinitionAdapterInterface|null $fieldDefinitionAdapter */
                    $fieldDefinitionAdapter = $this->fieldDefinitionService->getFieldDefinitionAdapter($fieldDefinition);
                    if ($fieldDefinitionAdapter) {
                        $standardFields[$configuration->getName()][$key] = $fieldDefinitionAdapter->getIndexData($asset, $configuration);
                    }
                }

                foreach ($localizedFieldDefinitions as $key => $fieldDefinition) {

                    /** @var FieldDefinitionAdapterInterface|null $fieldDefinitionAdapter */
                    $fieldDefinitionAdapter = $this->fieldDefinitionService->getFieldDefinitionAdapter($fieldDefinition);
                    if ($fieldDefinitionAdapter) {
                        $standardFields[$configuration->getName()][$key] = $fieldDefinitionAdapter->getIndexData($asset, $configuration, true);
                    }
                }
            }
        }

        //dispatch event before building checksum
        /** @var UpdateIndexDataEvent $updateIndexDataEvent */
        $updateIndexDataEvent = new UpdateIndexDataEvent($asset, $customFields);

        $this->eventDispatcher->dispatch($updateIndexDataEvent);

        $customFields = $updateIndexDataEvent->getCustomFields();

        /** @var string $checksum */
        $checksum = $checksum = crc32(json_encode([$systemFields, $standardFields, $customFields]));
        $systemFields[ElasticSearchFields::SYSTEM_FIELDS_CHECKSUM] = $checksum;

        $params = [
            'index' => $this->elasticSearchConfigService->getIndexName('asset'),
            'type' => '_doc',
            'id' => $asset->getId(),
            'refresh' => $this->performIndexRefresh,
            'body' => [
                ElasticSearchFields::SYSTEM_FIELDS => $systemFields,
                ElasticSearchFields::STANDARD_FIELDS => $standardFields,
                ElasticSearchFields::CUSTOM_FIELDS => $customFields
            ]
        ];

        return $params;
    }

    /**
     * @param Asset $asset
     *
     * @return $this
     */
    public function doUpdateIndexData($asset)
    {
        $params = [
            'index' => $this->elasticSearchConfigService->getIndexName('asset'),
            'type' => '_doc',
            'id' => $asset->getId()
        ];

        try {
            $indexDocument = $this->esClient->get($params);
            $originalChecksum = $indexDocument['_source'][ElasticSearchFields::SYSTEM_FIELDS][ElasticSearchFields::SYSTEM_FIELDS_CHECKSUM] ?? -1;
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
            $originalChecksum = -1;
        }

        $indexUpdateParams = $this->getIndexData($asset);

        if ($indexUpdateParams['body'][ElasticSearchFields::SYSTEM_FIELDS][ElasticSearchFields::SYSTEM_FIELDS_CHECKSUM] != $originalChecksum) {
            $response = $this->esClient->index($indexUpdateParams);
            $this->logger->info('Updates es index for asset ' . $asset->getId());
            $this->logger->debug(json_encode($response));
        } else {
            $this->logger->info('Not updating index for asset ' . $asset->getId() . ' - nothing has changed.');
        }

        return $this;
    }

    /**
     * @param int $elementId
     * @param string $elementIndexName
     *
     * @return $this
     */
    public function doDeleteFromIndex($elementId, $elementIndexName)
    {
        $params = [
            'index' => $this->elasticSearchConfigService->getIndexName($elementIndexName),
            'type' => '_doc',
            'id' => $elementId,
            'refresh' => $this->performIndexRefresh
        ];

        try {
            $response = $this->esClient->delete($params);
            $this->logger->info('Deleting asset ' . $elementId . ' from es index.');
            $this->logger->debug(json_encode($response));
        } catch (Missing404Exception $e) {
            $this->logger->info('Cannot delete asset ' . $elementId . ' from es index because not found.');
        }

        return $this;
    }

    /**
     * returns core fields index data array for given $asset
     *
     * @param Asset $asset
     *
     * @return array
     */
    public function getCoreFieldsIndexData($asset)
    {
        $date = new \DateTime();

        return [
            ElasticSearchFields::SYSTEM_FIELDS_ID => $asset->getId(),
            ElasticSearchFields::SYSTEM_FIELDS_CREATION_DATE => $date->setTimestamp($asset->getCreationDate())->format(\DateTime::ISO8601),
            ElasticSearchFields::SYSTEM_FIELDS_MODIFICATION_DATE => $date->setTimestamp($asset->getModificationDate())->format(\DateTime::ISO8601),
            ElasticSearchFields::SYSTEM_FIELDS_TYPE => $asset->getType(),
            ElasticSearchFields::SYSTEM_FIELDS_KEY => $asset->getKey(),
            ElasticSearchFields::SYSTEM_FIELDS_PATH => $asset->getPath(),
            ElasticSearchFields::SYSTEM_FIELDS_FULL_PATH => $asset->getRealFullPath(),
            ElasticSearchFields::SYSTEM_FIELDS_PATH_LEVELS => $this->extractPathLevels($asset->getType() === 'folder' ? $asset->getRealFullPath() : $asset->getPath()),
            ElasticSearchFields::SYSTEM_FIELDS_TAGS => $this->extractTagIds($asset),
            ElasticSearchFields::SYSTEM_FIELDS_MIME_TYPE => $asset->getMimetype(),
            ElasticSearchFields::SYSTEM_FIELDS_NAME => $this->nameExtractorService->extractAllLanguageNames($asset),
            ElasticSearchFields::SYSTEM_FIELDS_THUMBNAIL => $this->thumbnailService->getThumbnailPath($asset, ImageThumbnails::ELEMENT_TEASER),
            ElasticSearchFields::SYSTEM_FIELDS_COLLECTIONS => $this->getCollectionIdsByElement($asset),
            ElasticSearchFields::SYSTEM_FIELDS_PUBLIC_SHARES => $this->getPublicShareIdsByElement($asset),
            ElasticSearchFields::SYSTEM_FIELDS_USER_OWNER => $asset->getUserOwner(),
            ElasticSearchFields::SYSTEM_FIELDS_HAS_WORKFLOW_WITH_PERMISSIONS => $this->workflowService->hasWorkflowWithPermissions($asset),
            ElasticSearchFields::SYSTEM_FIELDS_FILE_SIZE => $asset->getFileSize()
        ];
    }

    /**
     * Called in index.yml
     *
     * @param array $coreFieldsConfig
     */
    public function setCoreFieldsConfig(array $coreFieldsConfig)
    {
        if (is_array($coreFieldsConfig['general']) && is_array($coreFieldsConfig['asset'])) {
            $this->coreFieldsConfig = array_merge($coreFieldsConfig['general'], $coreFieldsConfig['asset']);
        }
    }
}
