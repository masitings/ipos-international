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
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Field\ExportableField;
use Pimcore\Model\Asset;

/**
 * Interface FieldDefinitionAdapterInterface
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Asset\FieldDefinitionAdapter
 */
interface FieldDefinitionAdapterInterface extends \Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\DataPool\FieldDefinitionAdapter\FieldDefinitionAdapterInterface
{
    /**
     * @param Data $fieldDefinition
     *
     * @return $this
     */
    public function setFieldDefinition(Data $fieldDefinition);

    /**
     * @return Data
     */
    public function getFieldDefinition();

    /**
     * @param Asset $asset
     * @param Configuration $configuration
     * @param bool $localized
     *
     * @return mixed
     */
    public function getIndexData(Asset $asset, Configuration $configuration, bool $localized = false);

    public function getDataForExport(Asset $asset, Configuration $configuration, $localized = false): ExportableField;
}
