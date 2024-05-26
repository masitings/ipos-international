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

use Pimcore\Model\Asset;

/**
 * Class AssetRelationAdapter
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Asset\FieldDefinitionAdapter
 */
class AssetRelationAdapter extends ManyToOneRelationAdapter implements FieldDefinitionAdapterInterface
{
    /**
     * @param mixed $data
     *
     * @return mixed
     */
    public function castMetaData($data)
    {
        /** @var string[] $castedMetaData */
        $castedMetaData = [];
        /** @var Asset|null $asset */
        $asset = Asset::getById($data);

        if ($asset) {
            $castedMetaData = $this->getArrayValuesByElement($asset);
        }

        return $castedMetaData;
    }
}
