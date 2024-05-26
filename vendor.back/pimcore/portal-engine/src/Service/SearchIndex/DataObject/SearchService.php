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

use ONGR\ElasticsearchDSL\Search;
use Pimcore\Bundle\PortalEngineBundle\Enum\ElasticSearchFields;
use Pimcore\Bundle\PortalEngineBundle\Event\Search\FilterableFieldsEvent;
use Pimcore\Bundle\PortalEngineBundle\Event\Search\ListableFieldsEvent;
use Pimcore\Bundle\PortalEngineBundle\Event\Search\SortableFieldsEvent;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Field\FilterableField;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Field\ListableField;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Field\SortableField;
use Pimcore\Bundle\PortalEngineBundle\Service\DataObject\SearchIndexFieldDefinitionService;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Search\AbstractSearchService;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Concrete;

/**
 * Class SearchService
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service
 */
class SearchService extends AbstractSearchService
{
    /** @var SearchIndexFieldDefinitionService */
    protected $fieldDefinitionService;

    /**
     * @param SearchIndexFieldDefinitionService $fieldDefinitionService
     * @required
     */
    public function setFieldDefinitionService(SearchIndexFieldDefinitionService $fieldDefinitionService)
    {
        $this->fieldDefinitionService = $fieldDefinitionService;
    }

    /**
     * @return SortableField[]
     *
     * @throws \Exception
     */
    public function getSortableFields()
    {
        /** @var SortableField[] $sortableFields */
        $sortableFields = [];
        /** @var ClassDefinition $currentClassDefinition */
        $currentClassDefinition = $this->dataPoolConfigService->getCurrentClassDefinition();

        if ($currentClassDefinition) {
            foreach ($currentClassDefinition->getFieldDefinitions() as $fieldDefinition) {
                $fieldDefinitionAdapter = $this->fieldDefinitionService->getFieldDefinitionAdapter($fieldDefinition);
                if ($fieldDefinitionAdapter && $fieldDefinitionAdapter->isSortable()) {
                    foreach ($fieldDefinitionAdapter->getDataForSort() as $sortableField) {
                        $sortableField->setPath(ElasticSearchFields::STANDARD_FIELDS . '.' . $sortableField->getPath());

                        $sortableFields[] = $sortableField;
                    }
                }
            }
        }

        $sortableFields = $this->sortFieldsByTitle($sortableFields);

        /** @var SortableFieldsEvent $sortableFieldsEvent */
        $sortableFieldsEvent = new SortableFieldsEvent($sortableFields, $this->dataPoolConfigService->getCurrentDataPoolConfig());
        $this->eventDispatcher->dispatch($sortableFieldsEvent);
        $sortableFields = $sortableFieldsEvent->getSortableFields();

        return $sortableFields;
    }

    /**
     * @return ListableField[]
     *
     * @throws \Exception
     */
    public function getListableFields()
    {
        /** @var ListableField[] $listableFields */
        $listableFields = [];

        // add listable standard fields
        $listableFields[] = (new ListableField())
            ->setType('localized')
            ->setPath(ElasticSearchFields::SYSTEM_FIELDS . '.' . ElasticSearchFields::SYSTEM_FIELDS_NAME)
            ->setTitle('Systemfield.name')
            ->setName('name');

        /** @var ClassDefinition $currentClassDefinition */
        $currentClassDefinition = $this->dataPoolConfigService->getCurrentClassDefinition();

        if ($currentClassDefinition) {
            foreach ($currentClassDefinition->getFieldDefinitions() as $fieldDefinition) {
                $fieldDefinitionAdapter = $this->fieldDefinitionService->getFieldDefinitionAdapter($fieldDefinition);
                if ($fieldDefinitionAdapter && $fieldDefinitionAdapter->isListable()) {
                    foreach ($fieldDefinitionAdapter->getDataForList() as $listableField) {
                        $listableField->setPath(ElasticSearchFields::STANDARD_FIELDS . '.' . $listableField->getPath());

                        $listableFields[] = $listableField;
                    }
                }
            }
        }

        $listableFields = $this->sortFieldsByTitle($listableFields);

        /** @var ListableFieldsEvent $listableFieldsEvent */
        $listableFieldsEvent = new ListableFieldsEvent($listableFields, $this->dataPoolConfigService->getCurrentDataPoolConfig());
        $this->eventDispatcher->dispatch($listableFieldsEvent);
        $listableFields = $listableFieldsEvent->getListableFields();

        return $listableFields;
    }

    /**
     * @return FilterableField[]|array
     *
     * @throws \Exception
     */
    public function getFilterableFields()
    {
        /** @var FilterableField[] $filterableFields */
        $filterableFields = [];
        /** @var ClassDefinition $currentClassDefinition */
        $currentClassDefinition = $this->dataPoolConfigService->getCurrentClassDefinition();

        if ($currentClassDefinition) {
            foreach ($currentClassDefinition->getFieldDefinitions() as $fieldDefinition) {
                $fieldDefinitionAdapter = $this->fieldDefinitionService->getFieldDefinitionAdapter($fieldDefinition);
                if ($fieldDefinitionAdapter && $fieldDefinitionAdapter->isFilterable()) {
                    foreach ($fieldDefinitionAdapter->getDataForFilter() as $filterableField) {
                        $filterableField->setPath(ElasticSearchFields::STANDARD_FIELDS . '.' . $filterableField->getPath());

                        $filterableFields[] = $filterableField;
                    }
                }
            }
        }

        $filterableFields = $this->sortFieldsByTitle($filterableFields);

        /** @var FilterableFieldsEvent $filterableFieldsEvent */
        $filterableFieldsEvent = new FilterableFieldsEvent($filterableFields, $this->dataPoolConfigService->getCurrentDataPoolConfig());
        $this->eventDispatcher->dispatch($filterableFieldsEvent);
        $filterableFields = $filterableFieldsEvent->getFilterableFields();

        return $filterableFields;
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

        if ($publishedQuery = $this->publishedService->getElasticSearchPublishedQuery()) {
            $esSearch->addQuery($publishedQuery);
        }

        return $esSearch;
    }

    /**
     * @return string
     *
     * @throws \Exception
     */
    public function getESIndexName(?ClassDefinition $classDefinition = null)
    {
        /** @var string $classDefinitionName */
        $classDefinitionName = $classDefinition ? $classDefinition->getName() : $this->dataPoolConfigService->getCurrentClassDefinition()->getName();

        return $this->elasticSearchConfigService->getIndexName($classDefinitionName);
    }

    /**
     * @inheritDoc
     */
    public function isItemInDataPool($item): bool
    {
        if (!$item instanceof Concrete) {
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
            return $this->urlGenerator->generate('pimcore_portalengine_public_share_public_object_detail', [
                'publicShareHash' => $publicShare->getHash(),
                'id' => $searchResultHit['_source'][ElasticSearchFields::SYSTEM_FIELDS][ElasticSearchFields::SYSTEM_FIELDS_ID],
                'documentPath' => ltrim((string)$this->dataPoolConfigService->getCurrentDataPoolConfig()->getLanguageVariantOrDocument(), '/'),
            ]);
        } else {
            return $this->urlGenerator->generate('pimcore_portalengine_data_object_detail', [
                'id' => $searchResultHit['_source'][ElasticSearchFields::SYSTEM_FIELDS][ElasticSearchFields::SYSTEM_FIELDS_ID],
                'documentPath' => ltrim((string)$this->dataPoolConfigService->getCurrentDataPoolConfig()->getLanguageVariantOrDocument(), '/'),
            ]);
        }
    }
}
