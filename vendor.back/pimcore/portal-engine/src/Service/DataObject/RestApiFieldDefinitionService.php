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

namespace Pimcore\Bundle\PortalEngineBundle\Service\DataObject;

use Pimcore\Bundle\PortalEngineBundle\Service\Element\AbstractDefinitionService;
use Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\DataObject\FieldDefinitionAdapter\FieldDefinitionAdapterInterface;
use Pimcore\Model\DataObject\ClassDefinition\Data;

class RestApiFieldDefinitionService extends AbstractDefinitionService
{
    /**
     * @param Data $fieldDefinition
     *
     * @return FieldDefinitionAdapterInterface
     */
    public function getFieldDefinitionAdapter(Data $fieldDefinition)
    {
        return $this->getAdapter($fieldDefinition);
    }
}
