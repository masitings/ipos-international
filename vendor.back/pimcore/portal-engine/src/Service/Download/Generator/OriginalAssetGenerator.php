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
use Pimcore\Helper\TemporaryFileHelperTrait;
use Pimcore\Model\Asset;

class OriginalAssetGenerator implements DownloadGeneratorInterface
{
    use TemporaryFileHelperTrait;

    const ORIGINAL_FORMAT = 'original';

    /**
     * {@inheritDoc}
     */
    public function supports($source, DownloadConfig $config): bool
    {
        return
            $source instanceof Asset &&
            Type::isAssetType($config->getType()) &&
            // no format, hence original asset and no thumbnail
            (!$config->getFormat() || $config->getFormat() === self::ORIGINAL_FORMAT);
    }

    /**
     * {@inheritDoc}
     */
    public function createDownloadable($source, DownloadConfig $config): DownloadableInterface
    {
        return (new DownloadableAsset())->setGenerator($this)->setAsset($source);
    }

    /**
     * @param DownloadableAsset $downloadable
     *
     * @return GenerationResult|null
     */
    public function generate(DownloadableInterface $downloadable): ?GenerationResult
    {
        if (!$downloadable->getAsset()) {
            return null;
        }

        return new GenerationResult(
            $downloadable->getAsset()->getFilename(),
            self::getLocalFileFromStream($downloadable->getAsset()->getStream())
        );
    }
}
