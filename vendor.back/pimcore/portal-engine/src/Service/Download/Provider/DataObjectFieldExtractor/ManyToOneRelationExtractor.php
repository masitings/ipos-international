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

use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataObjectConfig;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadType;
use Pimcore\Model\DataObject\ClassDefinition\Data;

class ManyToOneRelationExtractor extends AbstractDataObjectFieldExtractor
{
    use RelationExtractorTrait;

    /**
     * {@inheritDoc}
     */
    public function supports(Data $fieldDefinition): bool
    {
        return $fieldDefinition instanceof Data\ManyToOneRelation;
    }

    /**
     * @param DataObjectConfig $config
     * @param Data\ManyToOneRelation|Data $fieldDefinition
     * @param array $context
     *
     * @return DownloadType|DownloadType[]|null
     */
    public function extract(DataObjectConfig $config, Data $fieldDefinition, array $context = [])
    {
        if (!$fieldDefinition->getAssetsAllowed()) {
            return null;
        }

        $type = $this->getAssetType($fieldDefinition);

        $downloadType = $this->createBasicDownloadType($config, $type, $fieldDefinition, $context)->setType($type);
        $this->applyThumbnailsToDownloadType($config, $downloadType);

        return $downloadType;
    }
}
