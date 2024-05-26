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

use Pimcore\Bundle\PortalEngineBundle\Enum\ElasticSearchFields;

/**
 * Class BooleanAdapter
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Asset\FieldDefinitionAdapter
 */
class BooleanAdapter extends DefaultAdapter implements FieldDefinitionAdapterInterface
{
    /**
     * @return array
     */
    public function getESMapping()
    {
        return [
            $this->fieldDefinition->getName(),
            [
                'type' => ElasticSearchFields::TYPE_BOOLEAN,
            ]
        ];
    }

    /**
     * @param mixed $data
     *
     * @return float|mixed
     */
    public function castMetaData($data)
    {
        //Store true/false as string in ES, its interpreted as boolean
        return (bool)$data
            ? 'true'
            : 'false';
    }

    /**
     * @return bool
     */
    public function isSortable()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function isListable()
    {
        return false;
    }
}
