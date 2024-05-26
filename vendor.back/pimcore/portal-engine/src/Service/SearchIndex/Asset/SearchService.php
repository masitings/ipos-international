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

use ONGR\ElasticsearchDSL\Query\Compound\BoolQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\TermQuery;
use ONGR\ElasticsearchDSL\Search;
use Pimcore\AssetMetadataClassDefinitionsBundle\Model\ClassDefinition\Data\Data;
use Pimcore\AssetMetadataClassDefinitionsBundle\Model\ClassDefinition\Data\Input;
use Pimcore\AssetMetadataClassDefinitionsBundle\Model\Configuration;
use Pimcore\AssetMetadataClassDefinitionsBundle\Model\Configuration\Dao;
use Pimcore\AssetMetadataClassDefinitionsBundle\Service;
use Pimcore\Bundle\PortalEngineBundle\Enum\ElasticSearchFields;
use Pimcore\Bundle\PortalEngineBundle\Enum\Permission;
use Pimcore\Bundle\PortalEngineBundle\Event\Search\FilterableFieldsEvent;
use Pimcore\Bundle\PortalEngineBundle\Event\Search\ListableFieldsEvent;
use Pimcore\Bundle\PortalEngineBundle\Event\Search\SortableFieldsEvent;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Field\FilterableField;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Field\ListableField;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Field\SortableField;
use Pimcore\Bundle\PortalEngineBundle\Service\Asset\SearchIndexFieldDefinitionService;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Asset\FieldDefinitionAdapter\FieldDefinitionAdapterInterface;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Search\AbstractSearchService;
use Pimcore\Model\Asset;

/**
 * Class SearchService
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Asset
 */
class SearchService extends AbstractSearchService
{
    /** @var SearchIndexFieldDefinitionService */
    protected $fieldDefinitionService;

    const PARAM_INCLUDE_FOLDERS = 'include-folders';

    /**
     * @param SearchIndexFieldDefinitionService $fieldDefinitionService
     * @required
     */
    public function setFieldDefinitionService(SearchIndexFieldDefinitionService $fieldDefinitionService)
    {
        $this->fieldDefinitionService = $fieldDefinitionService;
    }

    /**
     * @param array $params
     *
     * @return Search
     *
     * @throws \Exception
     */
    public function getSearchByParams(array $params = [])
    {
        $esSearch = parent::getSearchByParams($params);

        if ($this->checkIncludeFoldersParam($params)) {
            $esSearch->addQuery($this->getEditableOrNotEmptyFoldersQuery());
        } else {
            $esSearch->addQuery($this->getExcludeFoldersQuery());
        }

        return $esSearch;
    }

    protected function getExcludeFoldersQuery(): BoolQuery
    {
        $typeQuery = new TermQuery(ElasticSearchFields::SYSTEM_FIELDS . '.' . ElasticSearchFields::SYSTEM_FIELDS_TYPE, 'folder');
        $boolQuery = new BoolQuery();
        $boolQuery->add($typeQuery, BoolQuery::MUST_NOT);

        $resultBoolQuery = new BoolQuery();
        $resultBoolQuery->add($boolQuery, BoolQuery::FILTER);

        return $resultBoolQuery;
    }

    protected function getEditableOrNotEmptyFoldersQuery(): BoolQuery
    {
        $boolQuery = new BoolQuery();
        $boolQuery->add($this->getExcludeFoldersQuery(), BoolQuery::SHOULD);
        $boolQuery->add($this->workspaceService->getElasticSearchWorkspaceQuery(Permission::CREATE), BoolQuery::SHOULD);

        $resultBoolQuery = new BoolQuery();
        $resultBoolQuery->add($boolQuery, BoolQuery::FILTER);

        return $resultBoolQuery;
    }

    private function checkIncludeFoldersParam(array $params): bool
    {
        return isset($params[self::PARAM_INCLUDE_FOLDERS]) && $params[self::PARAM_INCLUDE_FOLDERS];
    }

    /**
     * @inheritDoc
     */
    public function getSortableFields()
    {
        /** @var SortableField[] $sortableFields */
        $sortableFields = [];

        $defaultAdapter = $this->fieldDefinitionService->getFieldDefinitionAdapter(new Input());

        // add listable standard fields

        $sortableFields[] = (new SortableField())
            ->setPath(ElasticSearchFields::SYSTEM_FIELDS . '.' . ElasticSearchFields::SYSTEM_FIELDS_ID)
            ->setTitle('Systemfield.id')
            ->setName('id');

        $sortableFields[] = (new SortableField())
            ->setPath(ElasticSearchFields::SYSTEM_FIELDS . '.' . ElasticSearchFields::SYSTEM_FIELDS_CREATION_DATE)
            ->setTitle('Systemfield.creationDate')
            ->setName('creationDate');

        $sortableFields[] = (new SortableField())
            ->setPath(ElasticSearchFields::SYSTEM_FIELDS . '.' . ElasticSearchFields::SYSTEM_FIELDS_MODIFICATION_DATE)
            ->setTitle('Systemfield.modificationDate')
            ->setName('modificationDate');

        $sortableFields[] = (new SortableField())
            ->setPath(ElasticSearchFields::SYSTEM_FIELDS . '.' . ElasticSearchFields::SYSTEM_FIELDS_KEY)
            ->setTitle('Systemfield.filename')
            ->setName('filename')
            ->setFieldDefinitionAdapter($defaultAdapter);

        $sortableFields[] = (new SortableField())
            ->setPath(ElasticSearchFields::SYSTEM_FIELDS . '.' . ElasticSearchFields::SYSTEM_FIELDS_TYPE)
            ->setTitle('Systemfield.type')
            ->setName('type')
            ->setFieldDefinitionAdapter($defaultAdapter);

        $sortableFields[] = (new SortableField())
            ->setPath(ElasticSearchFields::SYSTEM_FIELDS . '.' . ElasticSearchFields::SYSTEM_FIELDS_MIME_TYPE)
            ->setTitle('Systemfield.mimetype')
            ->setName('mimetype')
            ->setFieldDefinitionAdapter($defaultAdapter);

        foreach (Dao::getList(true) as $configuration) {

            /** @var SortableField[] $configurationSortableFields */
            $configurationSortableFields = [];
            /** @var Data[] $fieldDefinitions */
            $fieldDefinitions = [];
            /** @var Data[] $localizedFieldDefinitions */
            $localizedFieldDefinitions = [];

            Service::extractDataDefinitions($configuration->getLayoutDefinitions(), false, $fieldDefinitions, $localizedFieldDefinitions);

            foreach ($fieldDefinitions as $fieldDefinition) {

                /** @var FieldDefinitionAdapterInterface|null $fieldDefinitionAdapter */
                $fieldDefinitionAdapter = $this->fieldDefinitionService->getFieldDefinitionAdapter($fieldDefinition);
                if ($fieldDefinitionAdapter && $fieldDefinitionAdapter->isSortable()) {
                    $configurationSortableFields[] = $fieldDefinitionAdapter->getDataForSort();
                }
            }

            foreach ($localizedFieldDefinitions as $fieldDefinition) {

                /** @var FieldDefinitionAdapterInterface|null $fieldDefinitionAdapter */
                $fieldDefinitionAdapter = $this->fieldDefinitionService->getFieldDefinitionAdapter($fieldDefinition);
                if ($fieldDefinitionAdapter && $fieldDefinitionAdapter->isSortable()) {
                    $configurationSortableFields[] = $fieldDefinitionAdapter->getDataForSort(true);
                }
            }

            $sortableFields = array_merge($sortableFields, $this->applyConfigurationToFields($configuration, $configurationSortableFields));
        }

        /** @var SortableFieldsEvent $sortableFieldsEvent */
        $sortableFieldsEvent = new SortableFieldsEvent($sortableFields, $this->dataPoolConfigService->getCurrentDataPoolConfig());
        $this->eventDispatcher->dispatch($sortableFieldsEvent);
        $sortableFields = $sortableFieldsEvent->getSortableFields();

        return $sortableFields;
    }

    /**
     * @inheritDoc
     */
    public function getListableFields()
    {
        /** @var ListableField[] $listableFields */
        $listableFields = [];

        // add listable standard fields
        $listableFields[] = (new ListableField())
            ->setType('input')
            ->setPath(ElasticSearchFields::SYSTEM_FIELDS . '.' . ElasticSearchFields::SYSTEM_FIELDS_ID)
            ->setTitle('Systemfield.id')
            ->setName('id');

        // add listable standard fields
        $listableFields[] = (new ListableField())
            ->setType('localized')
            ->setPath(ElasticSearchFields::SYSTEM_FIELDS . '.' . ElasticSearchFields::SYSTEM_FIELDS_NAME)
            ->setTitle('Systemfield.name')
            ->setName('name');

        $listableFields[] = (new ListableField())
            ->setType('date')
            ->setPath(ElasticSearchFields::SYSTEM_FIELDS . '.' . ElasticSearchFields::SYSTEM_FIELDS_CREATION_DATE)
            ->setTitle('Systemfield.creationDate')
            ->setName('creationDate');

        $listableFields[] = (new ListableField())
            ->setType('date')
            ->setPath(ElasticSearchFields::SYSTEM_FIELDS . '.' . ElasticSearchFields::SYSTEM_FIELDS_MODIFICATION_DATE)
            ->setTitle('Systemfield.modificationDate')
            ->setName('modificationDate');

        $listableFields[] = (new ListableField())
            ->setType('input')
            ->setPath(ElasticSearchFields::SYSTEM_FIELDS . '.' . ElasticSearchFields::SYSTEM_FIELDS_KEY)
            ->setTitle('Systemfield.filename')
            ->setName('filename');

        $listableFields[] = (new ListableField())
            ->setType('input')
            ->setPath(ElasticSearchFields::SYSTEM_FIELDS . '.' . ElasticSearchFields::SYSTEM_FIELDS_TYPE)
            ->setTitle('Systemfield.type')
            ->setName('type');

        $listableFields[] = (new ListableField())
            ->setType('input')
            ->setPath(ElasticSearchFields::SYSTEM_FIELDS . '.' . ElasticSearchFields::SYSTEM_FIELDS_MIME_TYPE)
            ->setTitle('Systemfield.mimetype')
            ->setName('mimetype');

        foreach (Dao::getList(true) as $configuration) {

            /** @var ListableField[] $configurationListableFields */
            $configurationListableFields = [];
            /** @var Data[] $fieldDefinitions */
            $fieldDefinitions = [];
            /** @var Data[] $localizedFieldDefinitions */
            $localizedFieldDefinitions = [];

            Service::extractDataDefinitions($configuration->getLayoutDefinitions(), false, $fieldDefinitions, $localizedFieldDefinitions);

            foreach ($fieldDefinitions as $fieldDefinition) {

                /** @var FieldDefinitionAdapterInterface|null $fieldDefinitionAdapter */
                $fieldDefinitionAdapter = $this->fieldDefinitionService->getFieldDefinitionAdapter($fieldDefinition);
                if ($fieldDefinitionAdapter && $fieldDefinitionAdapter->isListable()) {
                    $configurationListableFields[] = $fieldDefinitionAdapter->getDataForList();
                }
            }

            foreach ($localizedFieldDefinitions as $fieldDefinition) {

                /** @var FieldDefinitionAdapterInterface|null $fieldDefinitionAdapter */
                $fieldDefinitionAdapter = $this->fieldDefinitionService->getFieldDefinitionAdapter($fieldDefinition);
                if ($fieldDefinitionAdapter && $fieldDefinitionAdapter->isListable()) {
                    $configurationListableFields[] = $fieldDefinitionAdapter->getDataForList(true);
                }
            }

            $listableFields = array_merge($listableFields, $this->applyConfigurationToFields($configuration, $configurationListableFields));
        }

        /** @var ListableFieldsEvent $listableFieldsEvent */
        $listableFieldsEvent = new ListableFieldsEvent($listableFields, $this->dataPoolConfigService->getCurrentDataPoolConfig());
        $this->eventDispatcher->dispatch($listableFieldsEvent);
        $listableFields = $listableFieldsEvent->getListableFields();

        return $listableFields;
    }

    /**
     * @inheritDoc
     */
    public function getFilterableFields()
    {
        /** @var FilterableField[] $filterableFields */
        $filterableFields = [];

        $defaultAdapter = $this->fieldDefinitionService->getFieldDefinitionAdapter(new Input());

        // add listable standard fields
        $filterableFields[] = (new FilterableField())
            ->setPath(ElasticSearchFields::SYSTEM_FIELDS . '.' . ElasticSearchFields::SYSTEM_FIELDS_KEY)
            ->setTitle('Systemfield.filename')
            ->setName('filename')
            ->setFieldDefinitionAdapter($defaultAdapter);

        $filterableFields[] = (new FilterableField())
            ->setPath(ElasticSearchFields::SYSTEM_FIELDS . '.' . ElasticSearchFields::SYSTEM_FIELDS_TYPE)
            ->setTitle('Systemfield.type')
            ->setName('type')
            ->setFieldDefinitionAdapter($defaultAdapter);

        $filterableFields[] = (new FilterableField())
            ->setPath(ElasticSearchFields::SYSTEM_FIELDS . '.' . ElasticSearchFields::SYSTEM_FIELDS_MIME_TYPE)
            ->setTitle('Systemfield.mimetype')
            ->setName('mimetype')
            ->setFieldDefinitionAdapter($defaultAdapter);

        foreach (Dao::getList(true) as $configuration) {

            /** @var FilterableField[] $configurationFilterableFields */
            $configurationFilterableFields = [];
            /** @var Data[] $fieldDefinitions */
            $fieldDefinitions = [];
            /** @var Data[] $localizedFieldDefinitions */
            $localizedFieldDefinitions = [];

            Service::extractDataDefinitions($configuration->getLayoutDefinitions(), false, $fieldDefinitions, $localizedFieldDefinitions);

            foreach ($fieldDefinitions as $fieldDefinition) {

                /** @var FieldDefinitionAdapterInterface|null $fieldDefinitionAdapter */
                $fieldDefinitionAdapter = $this->fieldDefinitionService->getFieldDefinitionAdapter($fieldDefinition);
                if ($fieldDefinitionAdapter && $fieldDefinitionAdapter->isFilterable()) {
                    $configurationFilterableFields[] = $fieldDefinitionAdapter->getDataForFilter();
                }
            }

            foreach ($localizedFieldDefinitions as $fieldDefinition) {

                /** @var FieldDefinitionAdapterInterface|null $fieldDefinitionAdapter */
                $fieldDefinitionAdapter = $this->fieldDefinitionService->getFieldDefinitionAdapter($fieldDefinition);
                if ($fieldDefinitionAdapter && $fieldDefinitionAdapter->isFilterable()) {
                    $configurationFilterableFields[] = $fieldDefinitionAdapter->getDataForFilter(true);
                }
            }

            $filterableFields = array_merge($filterableFields, $this->applyConfigurationToFields($configuration, $configurationFilterableFields));
        }

        /** @var FilterableFieldsEvent $filterableFieldsEvent */
        $filterableFieldsEvent = new FilterableFieldsEvent($filterableFields, $this->dataPoolConfigService->getCurrentDataPoolConfig());
        $this->eventDispatcher->dispatch($filterableFieldsEvent);
        $filterableFields = $filterableFieldsEvent->getFilterableFields();

        return $filterableFields;
    }

    /**
     * @return string
     */
    public function getESIndexName()
    {
        return $this->elasticSearchConfigService->getIndexName('asset');
    }

    public function isItemInDataPool($item): bool
    {
        if (!$item instanceof Asset) {
            return false;
        }

        return parent::isItemInDataPool($item);
    }

    /**
     * @param array $searchResultHit
     *
     * @return string
     */
    protected function generateDetailLink(array $searchResultHit): string
    {
        if ($publicShare = $this->publicShareService->getCurrentPublicShare()) {
            return $this->urlGenerator->generate('pimcore_portalengine_public_share_public_asset_detail', [
                'publicShareHash' => $publicShare->getHash(),
                'id' => $searchResultHit['_source'][ElasticSearchFields::SYSTEM_FIELDS][ElasticSearchFields::SYSTEM_FIELDS_ID],
                'documentPath' => ltrim((string)$this->dataPoolConfigService->getCurrentDataPoolConfig()->getLanguageVariantOrDocument(), '/'),
            ]);
        } else {
            return $this->urlGenerator->generate('pimcore_portalengine_asset_detail', [
                'id' => $searchResultHit['_source'][ElasticSearchFields::SYSTEM_FIELDS][ElasticSearchFields::SYSTEM_FIELDS_ID],
                'documentPath' => ltrim((string)$this->dataPoolConfigService->getCurrentDataPoolConfig()->getLanguageVariantOrDocument(), '/'),
            ]);
        }
    }

    /**
     * @param Configuration $configuration
     * @param FilterableField[] $fields
     *
     * @return FilterableField[]
     */
    protected function applyConfigurationToFields(Configuration $configuration, $fields = [])
    {
        foreach ($fields as $field) {
            //add standard_fields and configuration/metaDataClassDefinition name as prefix to ES path, e.g. notes.de.raw -> standard_fields.Copyright.notes.de.raw
            $field->setPath(ElasticSearchFields::STANDARD_FIELDS . '.' . $configuration->getPrefix() . '.' . $field->getPath());
            $field->setTitle($configuration->getName() . '.' . $field->getTitle());
        }

        $fields = $this->sortFieldsByTitle($fields);

        return $fields;
    }
}
