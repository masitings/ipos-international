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

namespace Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\DataPool\FieldDefinitionAdapter;

use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Field\FilterableField;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Field\ListableField;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Field\SortableField;
use Pimcore\Model\DataObject\ClassDefinition\Data;

/**
 * Interface FieldDefinitionAdapterInterface
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Filter\FieldDefinitionAdapter
 */
interface FieldDefinitionAdapterInterface
{
    /**
     * @return array
     */
    public function getESMapping();

    /**
     * @return bool
     */
    public function isSortable();

    /**
     * @param bool $fromLocalizedField
     *
     * @return SortableField[]
     */
    public function getDataForSort($fromLocalizedField = false);

    /**
     * @return bool
     */
    public function isListable();

    /**
     * @param bool $fromLocalizedField
     *
     * @return ListableField[]
     */
    public function getDataForList($fromLocalizedField = false);

    /**
     * @return bool
     */
    public function isExportable();

    /**
     * Used by CSV export to serialize the data into a single column
     *
     * @param mixed $exportData
     *
     * @return string
     */
    public function exportDataToString($exportData): string;

    /**
     * @return bool
     */
    public function isFilterable();

    /**
     * @param bool $fromLocalizedField
     *
     * @return FilterableField[]
     */
    public function getDataForFilter($fromLocalizedField = false);

    /**
     * @return string
     */
    public function getPath();

    /**
     * @param string $filterLabel
     * @param string $filterDataOptionValue
     *
     * @return string
     */
    public function formatFilterDataOptionLabel(string $filterLabel, string $filterDataOptionValue): string;

    /**
     * @return string
     */
    public function getFilterDataOptionSort(): string;
}
