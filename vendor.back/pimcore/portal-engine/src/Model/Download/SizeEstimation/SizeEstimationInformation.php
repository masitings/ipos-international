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

namespace Pimcore\Bundle\PortalEngineBundle\Model\Download\SizeEstimation;

use Pimcore\Bundle\PortalEngineBundle\Model\Download\Downloadable\DownloadableInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadSize;

class SizeEstimationInformation
{
    /**
     * @var DownloadableInterface[]
     */
    private $downloadables;

    /**
     * @var DownloadSize
     */
    private $downloadSize;

    public function __construct(array $downloadables, DownloadSize $downloadSize)
    {
        $this->downloadables = $downloadables;
        $this->downloadSize = $downloadSize;
    }

    /**
     * @return DownloadableInterface[]
     */
    public function getDownloadables(): array
    {
        return $this->downloadables;
    }

    /**
     * @param DownloadableInterface[] $downloadables
     */
    public function setDownloadables(array $downloadables): void
    {
        $this->downloadables = $downloadables;
    }

    /**
     * @return DownloadSize
     */
    public function getDownloadSize(): DownloadSize
    {
        return $this->downloadSize;
    }

    /**
     * @param DownloadSize $downloadSize
     */
    public function setDownloadSize(DownloadSize $downloadSize): void
    {
        $this->downloadSize = $downloadSize;
    }
}
