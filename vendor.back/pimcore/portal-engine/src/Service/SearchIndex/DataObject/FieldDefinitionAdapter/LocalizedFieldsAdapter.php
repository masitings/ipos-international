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

namespace Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\DataObject\FieldDefinitionAdapter;

use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Field\FilterableField;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Field\ListableField;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Field\SortableField;
use Pimcore\Bundle\PortalEngineBundle\Service\DataObject\SearchIndexFieldDefinitionService;
use Pimcore\Localization\LocaleServiceInterface;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Tool;

/**
 * Class LocalizedFieldsAdapter
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\DataObject\FieldDefinitionAdapter
 */
class LocalizedFieldsAdapter extends DefaultAdapter implements FieldDefinitionAdapterInterface
{
    /** @var SearchIndexFieldDefinitionService */
    protected $searchIndexFieldDefinitionService;
    /** @var LocaleServiceInterface */
    protected $localeService;

    /**
     * @param SearchIndexFieldDefinitionService $searchIndexFieldDefinitionService
     * @required
     */
    public function setSearchIndexFieldDefinitionService(SearchIndexFieldDefinitionService $searchIndexFieldDefinitionService): void
    {
        $this->searchIndexFieldDefinitionService = $searchIndexFieldDefinitionService;
    }

    /**
     * @param LocaleServiceInterface $localeService
     * @required
     *
     * @throws \Exception
     */
    public function setLocaleService(LocaleServiceInterface $localeService): void
    {
        $this->localeService = $localeService;
        if (!$localeService) {
            throw new \Exception('Locale not set');
        }
    }

    /**
     * @return array
     */
    public function getESMapping()
    {
        /** @var array $mapping */
        $mapping = [];
        /** @var string[] $languages */
        $languages = Tool::getValidLanguages();
        /** @var Data[] $childFieldDefinitions */
        $childFieldDefinitions = $this->fieldDefinition->getFieldDefinitions();

        foreach ($childFieldDefinitions as $childFieldDefinition) {
            /** @var FieldDefinitionAdapterInterface $fieldDefinitionAdapter */
            $fieldDefinitionAdapter = $this->searchIndexFieldDefinitionService->getFieldDefinitionAdapter($childFieldDefinition);
            if ($fieldDefinitionAdapter) {
                /** @var array $childFieldDefinitionESMapping */
                $childFieldDefinitionESMapping = $fieldDefinitionAdapter->getESMapping();

                list($mappingKey, $mappingStructure) = $childFieldDefinitionESMapping;

                $mapping[$mappingKey] = [
                    'properties' => []
                ];

                foreach ($languages as $language) {
                    $mapping[$mappingKey]['properties'][$language] = $mappingStructure;
                }
            }
        }

        return $mapping;
    }

    /**
     * @param Concrete $object
     *
     * @return array
     */
    public function getIndexData($object)
    {
        /** @var array $indexData */
        $indexData = [];
        /** @var string $localeBackup */
        $localeBackup = $this->localeService->getLocale();
        /** @var string[] $validLanguages */
        $validLanguages = Tool::getValidLanguages();

        if ($validLanguages) {
            foreach ($validLanguages as $language) {
                /** @var Data $fieldDefinition */
                foreach ($this->fieldDefinition->getFieldDefinitions() as $key => $fieldDefinition) {
                    $this->localeService->setLocale($language);

                    $fieldDefinitionAdapter = $this->searchIndexFieldDefinitionService->getFieldDefinitionAdapter($fieldDefinition);
                    if ($fieldDefinitionAdapter) {
                        $indexData[$key][$language] = $fieldDefinitionAdapter->getIndexData($object);
                    }
                }
            }
        }

        $this->localeService->setLocale($localeBackup);

        return $indexData;
    }

    /**
     * @param bool $fromLocalizedField
     *
     * @return SortableField[]
     */
    public function getDataForSort($fromLocalizedField = false)
    {
        /** @var SortableField[] $sortableFields */
        $sortableFields = [];
        /** @var string $locale */
        $locale = $this->localeService->getLocale();

        /** @var Data $fieldDefinition */
        foreach ($this->fieldDefinition->getFieldDefinitions() as $fieldDefinition) {
            $fieldDefinitionAdapter = $this->searchIndexFieldDefinitionService->getFieldDefinitionAdapter($fieldDefinition);
            if ($fieldDefinitionAdapter && $fieldDefinitionAdapter->isSortable()) {
                foreach ($fieldDefinitionAdapter->getDataForSort(true) as $sortableField) {
                    $sortableField->setPath($sortableField->getName() . '.' . $locale . '.' . $sortableField->getPath());

                    $sortableFields[] = $sortableField;
                }
            }
        }

        return $sortableFields;
    }

    /**
     * @param bool $fromLocalizedField
     *
     * @return ListableField[]
     */
    public function getDataForList($fromLocalizedField = false)
    {
        /** @var ListableField[] $listableFields */
        $listableFields = [];
        /** @var string $locale */
        $locale = $this->localeService->getLocale();

        /** @var Data $fieldDefinition */
        foreach ($this->fieldDefinition->getFieldDefinitions() as $fieldDefinition) {
            $fieldDefinitionAdapter = $this->searchIndexFieldDefinitionService->getFieldDefinitionAdapter($fieldDefinition);
            if ($fieldDefinitionAdapter && $fieldDefinitionAdapter->isListable()) {
                foreach ($fieldDefinitionAdapter->getDataForList(true) as $listableField) {
                    $listableField->setPath($listableField->getName() . '.' . $locale . '.' . $listableField->getPath());

                    $listableFields[] = $listableField;
                }
            }
        }

        return $listableFields;
    }

    /**
     * @return bool
     */
    public function isFilterable()
    {
        return true;
    }

    /**
     * @param bool $fromLocalizedField
     *
     * @return FilterableField[]
     */
    public function getDataForFilter($fromLocalizedField = false)
    {
        /** @var ListableField[] $filterableFields */
        $filterableFields = [];
        /** @var string $locale */
        $locale = $this->localeService->getLocale();

        /** @var Data $fieldDefinition */
        foreach ($this->fieldDefinition->getFieldDefinitions() as $fieldDefinition) {
            $fieldDefinitionAdapter = $this->searchIndexFieldDefinitionService->getFieldDefinitionAdapter($fieldDefinition);
            if ($fieldDefinitionAdapter && $fieldDefinitionAdapter->isFilterable()) {
                foreach ($fieldDefinitionAdapter->getDataForFilter(true) as $filterableField) {
                    $filterableField->setPath($filterableField->getName() . '.' . $locale . '.' . $filterableField->getPath());

                    $filterableFields[] = $filterableField;
                }
            }
        }

        return $filterableFields;
    }

    public function getDataForExport(Concrete $object)
    {
        $indexData = $this->getIndexData($object);
        $exportableFields = [];
        /** @var Data $fieldDefinition */
        foreach ($this->fieldDefinition->getFieldDefinitions() as $fieldDefinition) {
            $fieldDefinitionAdapter = $this->searchIndexFieldDefinitionService->getFieldDefinitionAdapter($fieldDefinition);
            if ($fieldDefinitionAdapter && $fieldDefinitionAdapter->isExportable()) {
                foreach ($fieldDefinitionAdapter->getDataForExport($object) as $exportableField) {
                    $exportableField
                        ->setLocalized(true)
                        ->setData($indexData[$exportableField->getName()]);

                    $exportableFields[] = $exportableField;
                }
            }
        }

        return $exportableFields;
    }
}
