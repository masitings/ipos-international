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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Asset;

use Pimcore\AssetMetadataClassDefinitionsBundle\Model\ClassDefinition\Data\Data;
use Pimcore\Bundle\PortalEngineBundle\Service\Element\AbstractDefinitionService;
use Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\Asset\MetadataDefinitionAdapter\MetadataDefinitionAdapterInterface;

class RestApiMetadataDefinitionService extends AbstractDefinitionService
{
    /**
     * @param Data $fieldDefinition
     *
     * @return MetadataDefinitionAdapterInterface
     */
    public function getMetadataDefinitionAdapter(Data $fieldDefinition)
    {
        $adapter = $this->getAdapter($fieldDefinition);
        $adapter->setFieldDefinition($fieldDefinition);

        return $adapter;
    }
}
