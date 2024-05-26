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

use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Field\ExportableField;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Concrete;

/**
 * Interface FieldDefinitionAdapterInterface
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Filter\FieldDefinitionAdapter
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
     * @param Concrete $object
     *
     * @return array
     */
    public function getIndexData($object);

    /**
     * @param bool $fromLocalizedField
     *
     * @return ExportableField[]
     */
    public function getDataForExport(Concrete $object);
}
