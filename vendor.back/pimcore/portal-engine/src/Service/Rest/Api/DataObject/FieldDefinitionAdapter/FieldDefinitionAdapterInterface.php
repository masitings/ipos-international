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

interface FieldDefinitionAdapterInterface
{
    /**
     * @param ClassDefinition\Data $fieldDefinition
     */
    public function setFieldDefinition(ClassDefinition\Data $fieldDefinition);

    /**
     * @param AbstractObject $object
     * @param $data
     * @param array $params
     *
     * @return mixed
     */
    public function getDataForDetail(AbstractObject $object, $data, array $params = []);

    /**
     * @param AbstractObject $object
     * @param $data
     * @param array $params
     *
     * @return VersionPreviewValue|VersionPreviewValue[]
     */
    public function getDataForVersionPreview(AbstractObject $object, $data, array $params = []);
}
