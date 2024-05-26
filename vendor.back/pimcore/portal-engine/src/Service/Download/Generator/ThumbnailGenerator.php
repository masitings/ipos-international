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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Download\Generator;

use Pimcore\Bundle\PortalEngineBundle\Enum\Download\Type;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\Downloadable\DownloadableAsset;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\Downloadable\DownloadableInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\Downloadable\GenerationResult;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadConfig;
use Pimcore\Bundle\PortalEngineBundle\Service\Download\DownloadService;
use Pimcore\Helper\TemporaryFileHelperTrait;
use Pimcore\Model\Asset;

class ThumbnailGenerator implements DownloadGeneratorInterface
{
    use TemporaryFileHelperTrait;

    const CUSTOM_FORMAT = 'custom';

    /**
     * {@inheritDoc}
     */
    public function supports($source, DownloadConfig $config): bool
    {
        $thumbnailConfig = Asset\Image\Thumbnail\Config::getByName($config->getFormat());

        return
            $source instanceof Asset\Image &&
            Type::isAssetType($config->getType()) &&
            ($thumbnailConfig || $config->getFormat() === self::CUSTOM_FORMAT);
    }

    /**
     * {@inheritDoc}
     */
    public function createDownloadable($source, DownloadConfig $config): DownloadableInterface
    {
        return (new DownloadableAsset())
            ->setGenerator($this)
            ->setAsset($source)
            ->setThumbnail($config->getFormat())
            ->setSetup($config->getSetup())
            ->setDeleteAfter($config->getFormat() === self::CUSTOM_FORMAT);
    }

    /**
     * @param DownloadableInterface $downloadable
     *
     * @return GenerationResult|null
     *
     * @throws \Exception
     */
    public function generate(DownloadableInterface $downloadable): ?GenerationResult
    {
        if (!($asset = $downloadable->getAsset()) || !$asset instanceof Asset\Image) {
            return null;
        }

        $thumbnail = null;

        if ($downloadable->getThumbnail() === self::CUSTOM_FORMAT) {
            $setup = $downloadable->getSetup() ?: [];

            $config = new Asset\Image\Thumbnail\Config();
            $config->setName(DownloadService::ZOMBIE_DOWNLOAD_PREFIX . $downloadable->getDownloadUniqid());
            $config->setFormat(($setup['format'] ?? null) ?: 'jpeg');
            $config->setQuality(max(($setup['quality'] ?? null) ? intval($setup['quality']) : 100, 100));

            if ($setup['width']) {
                $config->addItem('scaleByWidth', [
                    'width' => intval($setup['width']),
                ]);
            }

            if ($setup['height']) {
                $config->addItem('scaleByHeight', [
                    'height' => intval($setup['height'])
                ]);
            }

            $thumbnail = $asset->getThumbnail($config);
            //get temporary file that gets cleaned up after process is finished
            $filePath = self::getTemporaryFileFromStream($thumbnail->getStream());

            //directly cleanup thumbnail afterwards
            $asset->clearThumbnail(DownloadService::ZOMBIE_DOWNLOAD_PREFIX . $downloadable->getDownloadUniqid());
        } else {
            $thumbnail = $asset->getThumbnail($downloadable->getThumbnail());
            $filePath = self::getLocalFileFromStream($thumbnail->getStream());
        }

        $generationResult = new GenerationResult(
            basename($thumbnail->getPath()),
            $filePath
        );

        return $generationResult;
    }
}
