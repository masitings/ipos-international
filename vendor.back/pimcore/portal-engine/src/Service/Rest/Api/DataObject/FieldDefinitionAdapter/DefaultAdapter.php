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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\DataObject\FieldDefinitionAdapter;

use Pimcore\Bundle\PortalEngineBundle\Model\Rest\Api\DataObject\VersionPreviewValue;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\ClassDefinition;

class DefaultAdapter implements FieldDefinitionAdapterInterface
{
    /**
     * @var ClassDefinition\Data
     */
    protected $fieldDefinition;

    /**
     * {@inheritDoc}
     */
    public function setFieldDefinition(ClassDefinition\Data $fieldDefinition): void
    {
        $this->fieldDefinition = $fieldDefinition;
    }

    /**
     * @return ClassDefinition\Data
     */
    public function getFieldDefinition()
    {
        return $this->fieldDefinition;
    }

    /**
     * {@inheritDoc}
     */
    public function getDataForDetail(AbstractObject $object, $data, array $params = [])
    {
        return $this->fieldDefinition->getDataForEditmode($data, $object, $params);
    }

    /**
     * {@inheritDoc}
     */
    public function getDataForVersionPreview(AbstractObject $object, $data, array $params = [])
    {
        $value = $this->fieldDefinition->getVersionPreview($data, $object, $params);

        return new VersionPreviewValue($this->fieldDefinition->getName(), $this->fieldDefinition->getTitle(), $value);
    }
}
