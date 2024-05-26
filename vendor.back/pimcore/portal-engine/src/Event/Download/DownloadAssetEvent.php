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
 * Fires while an asset gets downloaded within the portal engine frontend.
 * For multi downloads/zip generations this happens when the assets gets added into the zip.
 * Can be used for example to track the downloads.
 */
class DownloadAssetEvent extends Event
{
    /**
     * @var DownloadableAsset
     */
    private $downloadable;

    /**
     * @var string
     */
    private $downloadContext;

    /**
     * DownloadAssetEvent constructor.
     *
     * @param DownloadableAsset $downloadable
     * @param string $downloadContext
     */
    public function __construct(DownloadableAsset $downloadable, string $downloadContext)
    {
        $this->downloadable = $downloadable;
        $this->downloadContext = $downloadContext;
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
    public function getDownloadContext(): string
    {
        return $this->downloadContext;
    }
}
