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

use Pimcore\Bundle\PortalEngineBundle\Enum\DataPool\FilterSort;
use Pimcore\Bundle\PortalEngineBundle\Enum\DataPool\TranslatorDomain;
use Pimcore\Bundle\PortalEngineBundle\Enum\ElasticSearchFields;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Field\ExportableField;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Field\FilterableField;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Field\ListableField;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Field\SortableField;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\TranslatorService;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\ElasticSearchConfigService;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Concrete;

/**
 * Class DefaultAdapter
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Filter\FieldDefinitionAdapter\DataObject
 */
class DefaultAdapter implements FieldDefinitionAdapterInterface
{
    /** @var TranslatorService */
    protected $translatorService;

    /** @var Data */
    protected $fieldDefinition;

    /** @var ElasticSearchConfigService */
    protected $elasticSearchConfigService;

    /**
     * DefaultAdapter constructor.
     *
     * @param TranslatorService $translatorService
     * @param ElasticSearchConfigService $elasticSearchConfigService
     */
    public function __construct(TranslatorService $translatorService, ElasticSearchConfigService $elasticSearchConfigService)
    {
        $this->translatorService = $translatorService;
        $this->elasticSearchConfigService = $elasticSearchConfigService;
    }

    /**
     * @param Data $fieldDefinition
     */
    public function setFieldDefinition(Data $fieldDefinition)
    {
        $this->fieldDefinition = $fieldDefinition;

        return $this;
    }

    /**
     * @return Data
     */
    public function getFieldDefinition()
    {
        return $this->fieldDefinition;
    }

    /**
     * @return array
     */
    public function getESMapping()
    {
        $fields = [
            'raw' => [
                'type' => ElasticSearchFields::TYPE_KEYWORD
            ]
        ];

        $searchAttributes = $this->elasticSearchConfigService->getSearchSettings()['search_analyzer_attributes'][ElasticSearchFields::TYPE_KEYWORD]['fields'] ?? [];
        if (!empty($searchAttributes)) {
            $fields = array_merge($searchAttributes, $fields);
        }

        return [
            $this->fieldDefinition->getName(),
            [
                'type' => ElasticSearchFields::TYPE_TEXT,
                'fields' => $fields
            ]
        ];
    }

    /**
     * @param Concrete $object
     *
     * @return mixed
     */
    public function getIndexData($object)
    {
        $value = $this->doGetIndexDataValue($object);

        return $value ?: null;
    }

    /**
     * @param $object
     *
     * @return string
     */
    protected function doGetIndexDataValue($object)
    {
        $value = $this->fieldDefinition->getDataForSearchIndex($object);

        if (is_array($value)) {
            return json_encode($value);
        }

        return (string)$value;
    }

    /**
     * @return bool
     */
    public function isSortable()
    {
        return true;
    }

    /**
     * @param bool $fromLocalizedField
     *
     * @return SortableField[]
     */
    public function getDataForSort($fromLocalizedField = false)
    {
        /** @var string $path */
        $path = $this->getPath();
        /** @var string $name */
        $name = $this->fieldDefinition->getName();
        /** @var string $title */
        $title = $this->fieldDefinition->getTitle();

        if (!$fromLocalizedField) {
            if (empty($path)) {
                $path = $name;
            } else {
                $path = $name . '.' . $path;
            }
        }

        /** @var SortableField $sortableField */
        $sortableField = (new SortableField())
            ->setTitle($title)
            ->setName($name)
            ->setPath($path)
            ->setFieldDefinitionAdapter($this);

        return [$sortableField];
    }

    /**
     * @return bool
     */
    public function isListable()
    {
        return true;
    }

    public function isExportable()
    {
        return $this->isListable();
    }

    /**
     * @param bool $fromLocalizedField
     *
     * @return ListableField[]
     */
    public function getDataForList($fromLocalizedField = false)
    {
        /** @var string $path */
        $path = $this->getPath();
        /** @var string $name */
        $name = $this->fieldDefinition->getName();
        /** @var string $title */
        $title = $this->fieldDefinition->getTitle();
        /** @var string $type */
        $type = $this->fieldDefinition->getFieldtype();

        if (!$fromLocalizedField) {
            if (empty($path)) {
                $path = $name;
            } else {
                $path = $name . '.' . $path;
            }
        }

        /** @var ListableField $listableField */
        $listableField = (new ListableField())
            ->setType($type)
            ->setTitle($title)
            ->setName($name)
            ->setPath($path)
            ->setFieldDefinitionAdapter($this);

        return [$listableField];
    }

    /**
     * @param Concrete $object
     *
     * @return array|ExportableField[]
     */
    public function getDataForExport(Concrete $object)
    {
        $exportableFields = [];
        foreach ($this->getDataForList() as $listableField) {
            $exportableFields[] = (new ExportableField())
                ->setType($listableField->getType())
                ->setTitle($listableField->getTitle())
                ->setName($listableField->getName())
                ->setData($this->getIndexData($object))
                ->setFieldDefinitionAdapter($this);
        }

        return $exportableFields;
    }

    /**
     * @param mixed $exportData
     *
     * @return string
     */
    public function exportDataToString($exportData): string
    {
        if (is_null($exportData)) {
            return '';
        }

        if (is_scalar($exportData)) {
            return (string) $exportData;
        }

        if (is_array($exportData)) {
            return implode(', ', $exportData);
        }

        return 'DATA-TYPE-EXPORT-TO-STRING-NOT-SUPPORTED';
    }

    /**
     * @return bool
     */
    public function isFilterable()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return 'raw';
    }

    /**
     * @param bool $fromLocalizedField
     *
     * @return FilterableField[]
     */
    public function getDataForFilter($fromLocalizedField = false)
    {
        /** @var string $path */
        $path = $this->getPath();
        /** @var string $name */
        $name = $this->fieldDefinition->getName();
        /** @var string $title */
        $title = $this->fieldDefinition->getTitle();

        if (!$fromLocalizedField) {
            if (empty($path)) {
                $path = $name;
            } else {
                $path = $name . '.' . $path;
            }
        }

        /** @var FilterableField $filterableField */
        $filterableField = (new FilterableField())
            ->setTitle($title)
            ->setName($name)
            ->setPath($path)
            ->setFieldDefinitionAdapter($this);

        return [$filterableField];
    }

    /**
     * @param $object
     *
     * @return mixed
     */
    protected function doGetRawIndexDataValue($object)
    {
        /** @var $getter */
        $getter = 'get' . ucfirst($this->fieldDefinition->getName());

        return $object->$getter();
    }

    /**
     * @param string $filterLabel
     * @param string $filterDataOptionValue
     *
     * @return string
     */
    public function formatFilterDataOptionLabel(string $filterLabel, string $filterDataOptionValue): string
    {
        return $this->translatorService->translate(
            $filterDataOptionValue,
            TranslatorDomain::DOMAIN_FILTER_OPTION_LABEL . '.' . $filterLabel
        );
    }

    /**
     * @return string
     */
    public function getFilterDataOptionSort(): string
    {
        return FilterSort::SORT_BY_LABEL;
    }
}
