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

namespace Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool;

use Pimcore\Bundle\PortalEngineBundle\Enum\Document\Editables;
use Pimcore\Bundle\PortalEngineBundle\Enum\ElasticSearchFields;
use Pimcore\Bundle\PortalEngineBundle\Model\ElementDataAware;
use Pimcore\Model\Document\Editable\Block;
use Pimcore\Model\Document\Page;
use Pimcore\Model\Document\PageSnippet;
use Pimcore\Tool;

abstract class AbstractDataPoolConfig implements DataPoolConfigInterface
{
    use ElementDataAware;

    /**
     * @var Page|PageSnippet|null
     */
    protected $languageVariant;

    /**
     * AbstractDataPoolConfig constructor.
     *
     * @param Page $document
     * @param Page|null $languageVariant
     */
    public function __construct(PageSnippet $document, PageSnippet $languageVariant = null)
    {
        $this->document = $document;
        $this->languageVariant = $languageVariant;
    }

    public function getId(): int
    {
        return $this->document->getId();
    }

    public function getDataPoolName(): string
    {
        $document = $this->getLanguageVariantOrDocument();

        return $document->getProperty('navigation_name') ?? $document->getKey();
    }

    public function getLanguage(): string
    {
        if ($language = $this->getLanguageVariantOrDocument()->getProperty('language')) {
            return $language;
        }

        if ($language = $this->getDocument()->getProperty('language')) {
            return $language;
        }

        return Tool::getDefaultLanguage();
    }

    /**
     * @return string[]
     */
    public function getVisibleLanguages(): array
    {
        return (array)$this->getElementData(Editables\DataPool\DataPoolConfig::VISIBLE_LANGUAGES);
    }

    /**
     * @return string[]
     */
    public function getEditableLanguages(): array
    {
        return (array)$this->getElementData(Editables\DataPool\DataPoolConfig::EDITABLE_LANGUAGES);
    }

    /**
     * @return FilterDefinitionConfig[]
     */
    public function getFilterDefinitions(): array
    {
        $result = [];

        /**
         * @var Block $block
         */
        if ($block = $this->document->getEditable(Editables\DataPool\DataPoolConfig::GRID_CONFIGURATION_FILTERS)) {
            foreach ($block->getElements() as $blockItem) {
                $filterType = $this->getBlockItemElementData($blockItem, Editables\DataPool\DataPoolConfig\FilterDefinition::FILTER_TYPE);
                $filterAttribute = $this->getBlockItemElementData($blockItem, Editables\DataPool\DataPoolConfig\FilterDefinition::FILTER_ATTRIBUTE);
                $filterParamName = $this->getBlockItemElementData($blockItem, Editables\DataPool\DataPoolConfig\FilterDefinition::FILTER_PARAM_NAME);

                if (empty($filterType) || empty($filterAttribute) || empty($filterParamName)) {
                    continue;
                }

                $result[] = new FilterDefinitionConfig($filterType, $filterAttribute, $filterParamName);
            }
        }

        return $result;
    }

    /**
     * @return SortOptionConfig[]
     */
    public function getSortOptions(): array
    {
        $result = [];

        /**
         * @var Block $block
         */
        if ($block = $this->document->getEditable(Editables\DataPool\DataPoolConfig::GRID_CONFIGURATION_SORT_OPTIONS)) {
            foreach ($block->getElements() as $blockItem) {
                $direction = $this->getBlockItemElementData($blockItem, Editables\DataPool\DataPoolConfig\SortOption::DIRECTION);
                $field = $this->getBlockItemElementData($blockItem, Editables\DataPool\DataPoolConfig\SortOption::FIELD);
                $paramName = $this->getBlockItemElementData($blockItem, Editables\DataPool\DataPoolConfig\SortOption::PARAM_NAME);

                if (empty($direction) || empty($field) || empty($paramName)) {
                    continue;
                }

                $result[] = new SortOptionConfig($direction, $field, $paramName);
            }
        }

        return $result;
    }

    public function getPreconditionServiceId(): ?string
    {
        return $this->getElementData(Editables\DataPool\DataPoolConfig::PRECONDITION_SERVICE_ID);
    }

    public function getEnableFolderNavigation(): bool
    {
        return (bool)$this->getElementData(Editables\DataPool\DataPoolConfig::ENABLE_FOLDER_NAVIGATION);
    }

    public function getEnableTagNavigation(): bool
    {
        return (bool)$this->getElementData(Editables\DataPool\DataPoolConfig::ENABLE_TAG_NAVIGATION);
    }

    public function getEnableVersionHistory(): bool
    {
        return (bool)$this->getElementData(Editables\DataPool\DataPoolConfig::ENABLE_VERSION_HISTORY);
    }

    /**
     * @return string[]
     */
    public function getAvailableDownloadThumbnails(): array
    {
        return (array)$this->getElementData(Editables\DataPool\DataPoolConfig::AVAILABLE_DOWNLOAD_THUMBNAILS);
    }

    /**
     * @return string[]
     */
    public function getGridConfigurationAttributes(): array
    {
        return (array)$this->getElementData(Editables\DataPool\DataPoolConfig::GRID_CONFIGURATION_ATTRIBUTES);
    }

    /**
     * @return string
     */
    public function getGridConfigurationNameAttribute(): string
    {
        return $this->getElementData(Editables\DataPool\DataPoolConfig::GRID_CONFIGURATION_NAME_ATTRIBUTE)
            ?? ElasticSearchFields::SYSTEM_FIELDS . '.' . ElasticSearchFields::SYSTEM_FIELDS_NAME;
    }

    /**
     * @return ?int
     */
    public function getRootTag(): ?int
    {
        return $this->getElementData(Editables\DataPool\DataPoolConfig::ROOT_TAG);
    }

    /**
     * @return string
     */
    public function getCssClass(): string
    {
        return 'data_pool_' . $this->document->getId();
    }

    /**
     * @return string[]
     */
    public function getAvailableDownloadFormats(): array
    {
        return (array)$this->getElementData(Editables\DataPool\DataPoolConfig::AVAILABLE_DOWNLOAD_FORMATS);
    }

    public function getLanguageVariantOrDocument(): Page
    {
        return $this->languageVariant ?? $this->document;
    }

    public function getLanguageVariantDataPoolId(): int
    {
        return $this->getLanguageVariantOrDocument()->getId();
    }
}
