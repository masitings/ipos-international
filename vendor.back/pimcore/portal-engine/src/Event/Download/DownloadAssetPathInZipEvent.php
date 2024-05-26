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

namespace Pimcore\Bundle\PortalEngineBundle\Event\Download;

use Pimcore\Bundle\PortalEngineBundle\Model\Download\Downloadable\DownloadableAsset;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Fires when an asset gets added into a download zip file.
 * Can be used to modify the full path (with filename) within the zip file.
 */
class DownloadAssetPathInZipEvent extends Event
{
    /**
     * @var DownloadableAsset
     */
    private $downloadable;

    /**
     * @var string
     */
    private $pathInZip;

    /**
     * @var bool
     */
    private $includeDataPoolFolder;

    public function __construct(DownloadableAsset $downloadable, string $pathInZip, bool $includeDataPoolFolder)
    {
        $this->downloadable = $downloadable;
        $this->pathInZip = $pathInZip;
        $this->includeDataPoolFolder = $includeDataPoolFolder;
    }

    /**
     * @return DownloadableAsset
     */
    public function getDownloadable(): DownloadableAsset
    {
        return $this->downloadable;
    }

    /**
     * @return string
     */
    public function getPathInZip(): string
    {
        return $this->pathInZip;
    }

    /**
     * @param string $pathInZip
     *
     * @return DownloadAssetPathInZipEvent
     */
    public function setPathInZip(string $pathInZip): self
    {
        $this->pathInZip = $pathInZip;

        return $this;
    }

    /**
     * Whether the data pool name of the item is included as first folder level or not.
     *
     * @return bool
     */
    public function isIncludeDataPoolFolder(): bool
    {
        return $this->includeDataPoolFolder;
    }
}
