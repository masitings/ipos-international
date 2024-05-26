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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\Element\Adapter;

use Pimcore\Model\DataObject\ClassDefinition\Data\Select;

trait SelectAdapterTrait
{
    protected function getOptionLabel($data, array $params)
    {
        /**
         * @var Select|\Pimcore\AssetMetadataClassDefinitionsBundle\Model\ClassDefinition\Data\Select $fieldDefinition
         */
        $fieldDefinition = $this->getFieldDefinition();

        if (method_exists($fieldDefinition, 'enrichFieldDefinition')) {
            $fieldDefinition->enrichFieldDefinition($params);
        } elseif (method_exists($fieldDefinition, 'enrichDefinition')) {
            $fieldDefinition->enrichDefinition();
        }

        if (method_exists($fieldDefinition, 'getOptions')) {
            $options = $fieldDefinition->getOptions();
        } else {
            $options = $fieldDefinition->options;
        }

        foreach ($options as $option) {
            if ($option['value'] === $data) {
                return $option['key'];
            }
        }

        return $data;
    }
}
