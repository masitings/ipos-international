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
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\TranslatorService;
use Pimcore\Bundle\PortalEngineBundle\Service\Download\DownloadService;
use Pimcore\Bundle\PortalEngineBundle\Service\Download\Provider\ThumbnailProviderTrait;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Data\Hotspotimage;
use Pimcore\Model\DataObject\Data\ImageGallery;

class ImageExtractor extends AbstractDataObjectFieldExtractor
{
    use ThumbnailProviderTrait;

    public function __construct(DownloadService $downloadService, TranslatorService $translatorService)
    {
        parent::__construct($downloadService);

        $this->translatorService = $translatorService;
    }

    /**
     * {@inheritDoc}
     */
    public function supports(Data $fieldDefinition): bool
    {
        return
            $fieldDefinition instanceof Data\Image ||
            $fieldDefinition instanceof Data\Hotspotimage ||
            $fieldDefinition instanceof Data\ImageGallery;
    }

    /**
     * {@inheritDoc}
     */
    public function extract(DataObjectConfig $config, Data $fieldDefinition, array $context = [])
    {
        $downloadType = $this->createBasicDownloadType($config, Type::IMAGE, $fieldDefinition, $context)->setType(Type::IMAGE);

        $this->addThumbnailsToDownloadType($config, $downloadType);

        return $downloadType;
    }

    /**
     * {@inheritDoc}
     */
    public function canTransform($data): bool
    {
        return
            $data instanceof ImageGallery ||
            $data instanceof Hotspotimage;
    }

    /**
     * {@inheritDoc}
     */
    public function transform($data)
    {
        if ($data instanceof ImageGallery) {
            if (empty($data->getItems())) {
                return [];
            }

            return array_filter(array_map(function (?Hotspotimage $hotspotimage) {
                return $hotspotimage ? $hotspotimage->getImage() : null;
            }, $data->getItems()));
        } else {
            return $data->getImage();
        }
    }
}
