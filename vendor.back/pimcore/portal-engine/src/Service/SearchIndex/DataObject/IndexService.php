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

namespace Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\DataObject;

use Elasticsearch\Common\Exceptions\Missing404Exception;
use Pimcore\Bundle\PortalEngineBundle\Enum\ElasticSearchFields;
use Pimcore\Bundle\PortalEngineBundle\Event\DataObject\ExtractMappingEvent;
use Pimcore\Bundle\PortalEngineBundle\Event\DataObject\UpdateIndexDataEvent;
use Pimcore\Bundle\PortalEngineBundle\Service\DataObject\MainImageExtractorService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataObject\SearchIndexFieldDefinitionService;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Class IndexService
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service
 */
class IndexService extends \Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\AbstractIndexService
{
    /** @var SearchIndexFieldDefinitionService */
    protected $fieldDefinitionService;
    /** @var MainImageExtractorService */
    protected $mainImageExtractorService;
    /** @var ParameterBagInterface */
    protected $containerParamBag;

    /**
     * @param SearchIndexFieldDefinitionService $fieldDefinitionService
     * @required
     */
    public function setFieldDefinitionService(SearchIndexFieldDefinitionService $fieldDefinitionService)
    {
        $this->fieldDefinitionService = $fieldDefinitionService;
    }

    /**
     * @param MainImageExtractorService $mainImageExtractorService
     * @required
     */
    public function setMainImageExtractorService(MainImageExtractorService $mainImageExtractorService)
    {
        $this->mainImageExtractorService = $mainImageExtractorService;
    }

    /**
     * @param ParameterBagInterface $containerParamBag
     * @required
     */
    public function setContainerParamBag(ParameterBagInterface $containerParamBag)
    {
        $this->containerParamBag = $containerParamBag;
    }

    /**
     * @param string $classDefinitionName
     *
     * @return string
     */
    protected function getIndexName(string $classDefinitionName): string
    {
        return $this->elasticSearchConfigService->getIndexName($classDefinitionName);
    }

    /**
     * @param ClassDefinition $classDefinition
     *
     * @return string
     */
    protected function getCurrentFullIndexName(ClassDefinition $classDefinition): string
    {
        $indexName = $this->getIndexName($classDefinition->getName());
        $currentIndexVersion = $this->getCurrentIndexVersion($indexName);

        return $indexName . '-' . ($currentIndexVersion === 'even' ? 'even' : 'odd');
    }

    /**
     * @param ClassDefinition $classDefinition
     *
     * @return $this
     */
    public function createIndex(ClassDefinition $classDefinition)
    {
        //create index
        $fullIndexName = $this->getCurrentFullIndexName($classDefinition);
        $this->doCreateIndex($fullIndexName);

        //update alias
        $params['body'] = [
            'actions' => [
                [
                    'add' => [
                        'index' => $fullIndexName,
                        'alias' => $this->getIndexName($classDefinition->getName()),
                    ],
                ],
            ],
        ];
        $this->esClient->indices()->updateAliases($params);

        return $this;
    }

    /**
     * @param ClassDefinition $classDefinition
     *
     * @return $this
     */
    public function deleteIndex(ClassDefinition $classDefinition)
    {
        $this->doDeleteIndex($this->getCurrentFullIndexName($classDefinition));

        return $this;
    }

    /**
     * @param ClassDefinition $classDefinition
     *
     * @return array
     */
    public function extractMapping(ClassDefinition $classDefinition)
    {
        /** @var array $mappingProperties */
        $mappingProperties = $this->extractSystemFieldsMapping();

        foreach ($classDefinition->getFieldDefinitions() as $fieldDefinition) {
            $fieldDefinitionAdapter = $this->fieldDefinitionService->getFieldDefinitionAdapter($fieldDefinition);
            if ($fieldDefinitionAdapter) {

                //localizedfields are nested with other ESMappings
                if ('localizedfields' === $fieldDefinition->fieldtype) {
                    foreach ($fieldDefinitionAdapter->getESMapping() as $mappingKey => $mappingEntry) {
                        $mappingProperties[ElasticSearchFields::STANDARD_FIELDS]['properties'][$mappingKey] = $mappingEntry;
                    }
                } else {
                    list($mappingKey, $mappingEntry) = $fieldDefinitionAdapter->getESMapping();
                    $mappingProperties[ElasticSearchFields::STANDARD_FIELDS]['properties'][$mappingKey] = $mappingEntry;
                }
            }
        }

        $mappingProperties[ElasticSearchFields::CUSTOM_FIELDS] = [];

        /** @var ExtractMappingEvent $extractMappingEvent */
        $extractMappingEvent = new ExtractMappingEvent($classDefinition, $mappingProperties[ElasticSearchFields::CUSTOM_FIELDS]);
        $this->eventDispatcher->dispatch($extractMappingEvent);
        $mappingProperties[ElasticSearchFields::CUSTOM_FIELDS]['properties'] = $extractMappingEvent->getCustomFieldsMapping();

        $mappingParams = [
            'index' => $this->getIndexName($classDefinition->getName()),
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
     * updates mapping for given Object class
     *  - update mapping without recreating index
     *  - if that fails, create new index and reindex on ES side
     *  - if that also fails, throws exception
     *
     * @param ClassDefinition $classDefinition
     * @param bool $forceCreateIndex
     *
     * @return $this
     */
    public function updateMapping(ClassDefinition $classDefinition, $forceCreateIndex = false)
    {
        if ($forceCreateIndex || !$this->esClient->indices()->existsAlias(['name' => $this->getIndexName($classDefinition->getName())])) {
            $this->createIndex($classDefinition);
        }

        //updating mapping without recreating index
        try {
            $this->doUpdateMapping($classDefinition);
        } catch (\Exception $e) {
            $this->logger->info($e);
            //try recreating index
            $this->reindex($this->getIndexName($classDefinition->getName()), $this->extractMapping($classDefinition));
        }

        return $this;
    }

    /**
     * updates mapping for index - throws exception if not successful
     *
     * @param ClassDefinition $classDefinition
     *
     * @return $this
     */
    protected function doUpdateMapping(ClassDefinition $classDefinition)
    {
        $mapping = $this->extractMapping($classDefinition);
        $response = $this->esClient->indices()->putMapping($mapping);
        $this->logger->debug(json_encode($response));

        return $this;
    }

    /**
     * @param Concrete $dataObject
     *
     * @return array
     */
    public function getIndexData($dataObject)
    {
        /** @var array $systemFields */
        $systemFields = $this->getCoreFieldsIndexData($dataObject);
        /** @var array $standardFields */
        $standardFields = [];
        /** @var array $customFields */
        $customFields = [];

        foreach ($dataObject->getClass()->getFieldDefinitions() as $key => $fieldDefinition) {
            $fieldDefinitionAdapter = $this->fieldDefinitionService->getFieldDefinitionAdapter($fieldDefinition);
            if ($fieldDefinitionAdapter) {

                /** @var array $indexData */
                $indexData = $fieldDefinitionAdapter->getIndexData($dataObject);

                //localizedfields are nested with other indexData
                if ('localizedfields' === $fieldDefinition->fieldtype) {
                    foreach ($indexData as $indexDataKey => $indexDataValue) {
                        $standardFields[$indexDataKey] = $indexDataValue;
                    }
                } else {
                    $standardFields[$key] = $indexData;
                }
            }
        }

        //dispatch event before building checksum
        /** @var UpdateIndexDataEvent $updateIndexDataEvent */
        $updateIndexDataEvent = new UpdateIndexDataEvent($dataObject, $customFields);
        $this->eventDispatcher->dispatch($updateIndexDataEvent);
        $customFields = $updateIndexDataEvent->getCustomFields();

        /** @var string $checksum */
        $checksum = crc32(json_encode([$systemFields, $standardFields, $customFields]));
        $systemFields[ElasticSearchFields::SYSTEM_FIELDS_CHECKSUM] = $checksum;

        $params = [
            'index' => $this->getIndexName($dataObject->getClassName()),
            'type' => '_doc',
            'id' => $dataObject->getId(),
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
     * Updates index for given object
     *
     * @param Concrete $dataObject
     *
     * @return $this
     */
    public function doUpdateIndexData($dataObject)
    {
        if (!$dataObject instanceof Concrete) {
            return $this;
        }

        $params = [
            'index' => $this->getIndexName($dataObject->getClassName()),
            'type' => '_doc',
            'id' => $dataObject->getId()
        ];

        try {
            $indexDocument = $this->esClient->get($params);
            $originalChecksum = $indexDocument['_source'][ElasticSearchFields::SYSTEM_FIELDS][ElasticSearchFields::SYSTEM_FIELDS_CHECKSUM] ?? -1;
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
            $originalChecksum = -1;
        }

        $indexUpdateParams = $this->getIndexData($dataObject);

        if ($indexUpdateParams['body'][ElasticSearchFields::SYSTEM_FIELDS][ElasticSearchFields::SYSTEM_FIELDS_CHECKSUM] != $originalChecksum) {
            $response = $this->esClient->index($indexUpdateParams);
            $this->logger->info('Updates es index for data object ' . $dataObject->getId());
            $this->logger->debug(json_encode($response));
        } else {
            $this->logger->info('Not updating index for data object ' . $dataObject->getId() . ' - nothing has changed.');
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
            'index' => $this->getIndexName($elementIndexName),
            'type' => '_doc',
            'id' => $elementId,
            'refresh' => $this->performIndexRefresh
        ];

        try {
            $response = $this->esClient->delete($params);
            $this->logger->info('Deleting data object ' . $elementId . ' from es index.');
            $this->logger->debug(json_encode($response));
        } catch (Missing404Exception $e) {
            $this->logger->info('Cannot delete data object ' . $elementId . ' from es index because not found.');
        }

        return $this;
    }

    /**
     * @param ClassDefinition $classDefinition
     * @param string $aliasName
     *
     * @return $this
     */
    public function addClassDefinitionToAlias(ClassDefinition $classDefinition, string $aliasName)
    {
        if (!$this->existsAliasForClassDefinition($classDefinition, $aliasName)) {
            $response = $this->esClient->indices()->putAlias([
                'name' => $this->prefixAliasName($aliasName),
                'index' => $this->getCurrentFullIndexName($classDefinition)
            ]);
            $this->logger->debug(json_encode($response));
        }

        return $this;
    }

    /**
     * @param ClassDefinition $classDefinition
     * @param string $aliasName
     *
     * @return $this
     */
    public function removeClassDefinitionFromAlias(ClassDefinition $classDefinition, string $aliasName)
    {
        if ($this->existsAliasForClassDefinition($classDefinition, $aliasName)) {
            $response = $this->esClient->indices()->deleteAlias([
                'name' => $this->prefixAliasName($aliasName),
                'index' => $this->getCurrentFullIndexName($classDefinition)
            ]);
            $this->logger->debug(json_encode($response));
        }

        return $this;
    }

    /**
     * @param ClassDefinition $classDefinition
     * @param string $aliasName
     *
     * @return bool
     */
    protected function existsAliasForClassDefinition(ClassDefinition $classDefinition, string $aliasName)
    {
        return $this->esClient->indices()->existsAlias([
            'name' => $this->prefixAliasName($aliasName),
            'index' => $this->getCurrentFullIndexName($classDefinition)
        ]);
    }

    protected function prefixAliasName(string $aliasName): string
    {
        return $this->containerParamBag->get('portal-engine.elasticsearch.index-prefix') . $aliasName;
    }

    /**
     * returns core fields index data array for given object
     *
     * @param Concrete $dataObject
     *
     * @return array
     */
    public function getCoreFieldsIndexData($dataObject)
    {
        $date = new \DateTime();

        return [
            ElasticSearchFields::SYSTEM_FIELDS_ID => $dataObject->getId(),
            ElasticSearchFields::SYSTEM_FIELDS_CREATION_DATE => $date->setTimestamp($dataObject->getCreationDate())->format(\DateTime::ISO8601),
            ElasticSearchFields::SYSTEM_FIELDS_MODIFICATION_DATE => $date->setTimestamp($dataObject->getModificationDate())->format(\DateTime::ISO8601),
            ElasticSearchFields::SYSTEM_FIELDS_PUBLISHED => $dataObject->getPublished(),
            ElasticSearchFields::SYSTEM_FIELDS_TYPE => $dataObject->getType(),
            ElasticSearchFields::SYSTEM_FIELDS_KEY => $dataObject->getKey(),
            ElasticSearchFields::SYSTEM_FIELDS_PATH => $dataObject->getPath(),
            ElasticSearchFields::SYSTEM_FIELDS_FULL_PATH => $dataObject->getRealFullPath(),
            ElasticSearchFields::SYSTEM_FIELDS_PATH_LEVELS => $this->extractPathLevels($dataObject->getPath()),
            ElasticSearchFields::SYSTEM_FIELDS_TAGS => $this->extractTagIds($dataObject),
            ElasticSearchFields::SYSTEM_FIELDS_CLASS_NAME => $dataObject->getClassName(),
            ElasticSearchFields::SYSTEM_FIELDS_NAME => $this->nameExtractorService->extractAllLanguageNames($dataObject),
            ElasticSearchFields::SYSTEM_FIELDS_THUMBNAIL => $this->mainImageExtractorService->extractThumbnail($dataObject),
            ElasticSearchFields::SYSTEM_FIELDS_COLLECTIONS => $this->getCollectionIdsByElement($dataObject),
            ElasticSearchFields::SYSTEM_FIELDS_PUBLIC_SHARES => $this->getPublicShareIdsByElement($dataObject),
            ElasticSearchFields::SYSTEM_FIELDS_USER_OWNER => $dataObject->getUserOwner()
        ];
    }

    /**
     * Called in index.yml
     *
     * @param array $coreFieldsConfig
     */
    public function setCoreFieldsConfig(array $coreFieldsConfig)
    {
        if (is_array($coreFieldsConfig['general']) && is_array($coreFieldsConfig['data_object'])) {
            $this->coreFieldsConfig = array_merge($coreFieldsConfig['general'], $coreFieldsConfig['data_object']);
        }
    }
}
