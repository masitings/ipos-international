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

namespace Pimcore\Bundle\PortalEngineBundle\Model\Download\Downloadable;

use Pimcore\Model\Asset;

class DownloadableAsset extends AbstractDownloadable
{
    private $asset;
    private $thumbnail;
    private $setup;

    /**
     * @param Asset $asset
     *
     * @return $this
     */
    public function setAsset(Asset $asset)
    {
        $this->asset = $asset;

        return $this;
    }

    /**
     * @return Asset
     */
    public function getAsset(): Asset
    {
        return $this->asset;
    }

    /**
     * @return string
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * @param string $thumbnail
     *
     * @return $this
     */
    public function setThumbnail(string $thumbnail)
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    /**
     * @param array|null $setup
     *
     * @return $this
     */
    public function setSetup(?array $setup)
    {
        $this->setup = $setup;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getSetup()
    {
        return $this->setup;
    }
}
