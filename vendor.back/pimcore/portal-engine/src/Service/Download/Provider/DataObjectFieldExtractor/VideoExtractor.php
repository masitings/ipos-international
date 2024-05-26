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
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Data\Video;

class VideoExtractor extends AbstractDataObjectFieldExtractor
{
    /**
     * {@inheritDoc}
     */
    public function supports(Data $fieldDefinition): bool
    {
        return $fieldDefinition instanceof Data\Video;
    }

    /**
     * {@inheritDoc}
     */
    public function extract(DataObjectConfig $config, Data $fieldDefinition, array $context = [])
    {
        return $this->createBasicDownloadType($config, Type::ASSET, $fieldDefinition, $context)->setType(Type::ASSET);
    }

    /**
     * {@inheritDoc}
     */
    public function canTransform($data): bool
    {
        return $data instanceof Video;
    }

    /**
     * @param Video $data
     */
    public function transform($data)
    {
        if ($data->getType() === 'asset') {
            return $data->getData();
        }

        return null;
    }
}
