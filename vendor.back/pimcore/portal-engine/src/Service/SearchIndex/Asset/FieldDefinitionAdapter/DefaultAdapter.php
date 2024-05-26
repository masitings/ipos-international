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

namespace Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Asset\FieldDefinitionAdapter;

use Pimcore\AssetMetadataClassDefinitionsBundle\Model\ClassDefinition\Data\Data;
use Pimcore\AssetMetadataClassDefinitionsBundle\Model\Configuration;
use Pimcore\Bundle\PortalEngineBundle\Enum\DataPool\FilterSort;
use Pimcore\Bundle\PortalEngineBundle\Enum\DataPool\TranslatorDomain;
use Pimcore\Bundle\PortalEngineBundle\Enum\ElasticSearchFields;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Field\ExportableField;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Field\FilterableField;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Field\ListableField;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Field\SortableField;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\TranslatorService;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\ElasticSearchConfigService;
use Pimcore\Localization\LocaleServiceInterface;
use Pimcore\Model\Asset;
use Pimcore\Tool;

/**
 * Class DefaultAdapter
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Asset\FieldDefinitionAdapter
 */
class DefaultAdapter implements FieldDefinitionAdapterInterface
{
    /** @var LocaleServiceInterface */
    protected $localeService;
    /** @var TranslatorService */
    protected $translatorService;

    /** @var Data */
    protected $fieldDefinition;

    /** @var ElasticSearchConfigService */
    protected $elasticSearchConfigService;

    /**
     * DefaultAdapter constructor.
     *
     * @param LocaleServiceInterface $localeService
     * @param TranslatorService $translatorService
     * @param ElasticSearchConfigService $elasticSearchConfigService
     *
     * @throws \Exception
     */
    public function __construct(LocaleServiceInterface $localeService, TranslatorService $translatorService, ElasticSearchConfigService $elasticSearchConfigService)
    {
        $this->localeService = $localeService;
        $this->translatorService = $translatorService;
        $this->elasticSearchConfigService = $elasticSearchConfigService;

        if (!$localeService) {
            throw new \Exception('Locale not set');
        }
    }

    /**
     * @param Data $fieldDefinition
     *
     * @return $this
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
     * @return array
     */
    public function getLocalizedESMapping()
    {
        list($mappingKey, $mappingStructure) = $this->getESMapping();

        /** @var array $mapping */
        $mapping = [
            $mappingKey => [
                'properties' => []
            ]
        ];

        foreach (Tool::getValidLanguages() as $language) {
            $mapping[$mappingKey]['properties'][$language] = $mappingStructure;
        }

        return $mapping;
    }

    /**
     * @param Asset $asset
     * @param Configuration $configuration
     * @param bool $localized
     *
     * @return mixed
     */
    public function getIndexData(Asset $asset, Configuration $configuration, bool $localized = false)
    {
        /** @var $indexData */
        $indexData = null;
        /** @var string $metaDataName */
        $metaDataName = $configuration->getPrefix() . '.' . $this->fieldDefinition->getName();

        if ($localized) {
            $indexData = [];
            foreach (Tool::getValidLanguages() as $language) {

                /** @var array|null $assetMetaData */
                $assetMetaData = $asset->getMetadata($metaDataName, $language, false, true);
                if (is_array($assetMetaData) && array_key_exists('data', $assetMetaData)) {
                    $indexData[$language] = $this->castMetaData($assetMetaData['data']);
                }
            }
        } else {
            /** @var array|null $assetMetaData */
            $assetMetaData = $asset->getMetadata($metaDataName, '', false, true);

            if (is_array($assetMetaData) && array_key_exists('data', $assetMetaData)) {
                $indexData = $this->castMetaData($assetMetaData['data']);
            }
        }

        return $indexData;
    }

    /**
     * @param mixed $data
     *
     * @return mixed
     */
    protected function castMetaData($data)
    {
        return $data;
    }

    /**
     * @return bool
     */
    public function isSortable()
    {
        return true;
    }

    /**
     * @param bool $localized
     *
     * @return SortableField
     */
    public function getDataForSort($localized = false)
    {
        /** @var string $name */
        $name = $this->fieldDefinition->getName();
        /** @var string $title */
        $title = $this->fieldDefinition->getTitle();
        /** @var string $pathPostfix */
        $pathPostfix = $this->getPath();

        /** @var string $path */
        $path = $name;
        if ($localized) {
            $path .= '.' . $this->localeService->getLocale();
        }
        if (!empty($pathPostfix)) {
            $path .= '.' . $pathPostfix;
        }

        return (new SortableField())
            ->setTitle($title)
            ->setName($name)
            ->setPath($path)
            ->setFieldDefinitionAdapter($this);
    }

    /**
     * @return bool
     */
    public function isListable()
    {
        return true;
    }

    /**
     * @param bool $localized
     *
     * @return ListableField
     */
    public function getDataForList($localized = false)
    {
        /** @var string $name */
        $name = $this->fieldDefinition->getName();
        /** @var string $title */
        $title = $this->fieldDefinition->getTitle();
        /** @var string $type */
        $type = $this->fieldDefinition->getFieldtype();
        /** @var string $pathPostfix */
        $pathPostfix = $this->getPath();

        /** @var string $path */
        $path = $name;
        if ($localized) {
            $path .= '.' . $this->localeService->getLocale();
        }
        if (!empty($pathPostfix)) {
            $path .= '.' . $pathPostfix;
        }

        return (new ListableField())
            ->setType($type)
            ->setTitle($title)
            ->setName($name)
            ->setPath($path)
            ->setFieldDefinitionAdapter($this);
    }

    /**
     * @return bool
     */
    public function isFilterable()
    {
        return false;
    }

    /**
     * @param bool $localized
     *
     * @return FilterableField
     */
    public function getDataForFilter($localized = false)
    {
        /** @var string $name */
        $name = $this->fieldDefinition->getName();
        /** @var string $title */
        $title = $this->fieldDefinition->getTitle();
        /** @var string $pathPostfix */
        $pathPostfix = $this->getPath();

        /** @var string $path */
        $path = $name;
        if ($localized) {
            $path .= '.' . $this->localeService->getLocale();
        }
        if (!empty($pathPostfix)) {
            $path .= '.' . $pathPostfix;
        }

        return (new FilterableField())
            ->setTitle($title)
            ->setName($name)
            ->setPath($path)
            ->setFieldDefinitionAdapter($this);
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return 'raw';
    }

    /**
     * @return bool
     */
    public function isExportable()
    {
        return $this->isListable();
    }

    /**
     * @param Asset $asset
     * @param Configuration $configuration
     * @param bool $localized
     *
     * @return ExportableField
     */
    public function getDataForExport(Asset $asset, Configuration $configuration, $localized = false): ExportableField
    {
        $listableField = $this->getDataForList();

        $data = $this->getIndexData($asset, $configuration, $localized);

        if ($localized) {
            foreach (Tool::getValidLanguages() as $language) {
                $data[$language] = $data[$language] ?? null;
            }
        }

        return (new ExportableField())
                ->setType($listableField->getType())
                ->setTitle($configuration->getPrefix() . '.' . $listableField->getTitle())
                ->setName($configuration->getPrefix() . '.' . $listableField->getName())
                ->setData($data)
                ->setFieldDefinitionAdapter($this);
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
