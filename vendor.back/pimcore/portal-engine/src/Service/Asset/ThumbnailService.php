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

use Pimcore\Bundle\PortalEngineBundle\Service\DataObject\MainImageExtractorService;
use Pimcore\Bundle\PortalEngineBundle\Service\Download\Generator\OriginalAssetGenerator;
use Pimcore\Bundle\PortalEngineBundle\Service\Download\Generator\ThumbnailGenerator;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\Element\ElementInterface;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\JsonManifestVersionStrategy;

/**
 * Class ThumbnailService
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\Asset
 */
class ThumbnailService
{
    protected $mainImageExtractorService;

    public function __construct(MainImageExtractorService $mainImageExtractorService)
    {
        $this->mainImageExtractorService = $mainImageExtractorService;
    }

    /**
     * @param ElementInterface $element
     * @param string $thumbnailConfig
     *
     * @return string|null
     */
    public function getThumbnailPathFromElement(ElementInterface $element, string $thumbnailConfig): ?string
    {
        if ($element instanceof Asset) {
            return $this->getThumbnailPath($element, $thumbnailConfig);
        } elseif ($element instanceof AbstractObject && ($image = $this->mainImageExtractorService->extractMainImage($element))) {
            return $this->getThumbnailPath($image, $thumbnailConfig);
        }

        return null;
    }

    /**
     * @param Asset $asset
     * @param string $thumbnailConfig
     * @param bool $fallBack
     *
     * @return string|null
     */
    public function getThumbnailPath(Asset $asset, string $thumbnailConfig, $fallBack = false): ?string
    {
        $thumbnailPath = null;

        if ($asset instanceof Asset\Image) {
            $thumbnailPath = (new Asset\Image\Thumbnail($asset, $thumbnailConfig))->getPath();
        }
        if ($asset instanceof Asset\Document) {
            $asset->processPageCount();
            $thumbnailPath = $asset->getImageThumbnail($thumbnailConfig)->getPath();
        }
        if ($asset instanceof Asset\Video) {
            $thumbnailPath = $asset->getImageThumbnail($thumbnailConfig)->getPath();
        }

        if (!$thumbnailPath && $fallBack) {
            return (new Package(new JsonManifestVersionStrategy(PIMCORE_WEB_ROOT . '/bundles/pimcoreportalengine/build/manifest.json')))->getUrl('bundles/pimcoreportalengine/build/images/placeholder-img.svg');
        } elseif (!empty($thumbnailPath)) {
            return '/cache-buster-' . $asset->getVersionCount() . $thumbnailPath;
        } else {
            return null;
        }
    }

    /**
     * @return array
     */
    public function getImageThumbnailSelectStore()
    {
        $thumbnails = new Asset\Image\Thumbnail\Config\Listing;
        $thumbnailNames = [];

        foreach ($thumbnails->getThumbnails() as $thumbnail) {
            $thumbnailNames[] = $thumbnail->getName();
        }

        sort($thumbnailNames);

        $result = [];
        foreach ($thumbnailNames as $name) {
            $result[] = [$name, $name];
        }

        $result = array_merge([
            [OriginalAssetGenerator::ORIGINAL_FORMAT, OriginalAssetGenerator::ORIGINAL_FORMAT],
            [ThumbnailGenerator::CUSTOM_FORMAT, ThumbnailGenerator::CUSTOM_FORMAT],
        ], $result);

        return $result;
    }
}
