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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Download\Provider\DataObjectFieldExtractor;

use Pimcore\Bundle\PortalEngineBundle\Enum\Download\Type;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataObjectConfig;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadType;
use Pimcore\Model\DataObject\ClassDefinition\Data;

trait RelationExtractorTrait
{
    /**
     * @param Data\ManyToOneRelation|Data\ManyToManyRelation|Data\AdvancedManyToManyRelation $fieldDefinition
     *
     * @return string
     */
    protected function getAssetType(Data $fieldDefinition)
    {
        // no asset types set --> asset
        $assetTypes = $fieldDefinition->getAssetTypes();
        $type = !empty($assetTypes) ? Type::IMAGE : Type::ASSET;

        // if only images are allowed --> image
        foreach ($assetTypes as $assetType) {
            if ($assetType !== 'image') {
                $type = Type::ASSET;
                break;
            }
        }

        return $type;
    }

    protected function applyThumbnailsToDownloadType(DataObjectConfig $config, DownloadType $downloadType)
    {
        // add thumbnails if only images allowed
        if ($downloadType->getType() === Type::IMAGE) {
            foreach ($config->getAvailableDownloadThumbnails() as $thumbnail) {
                $downloadType->addFormat($thumbnail, $thumbnail);
            }
        }
    }
}
