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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Download\SizeEstimation;

use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadItemInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadSize;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\SizeEstimation\SizeEstimationInformation;

interface SizeEstimationAdapterInterface
{
    /**
     * Estimates the download size for a given download item.
     *
     * @param DownloadItemInterface $downloadItem
     *
     * @return DownloadSize
     */
    public function estimate(DownloadItemInterface $downloadItem): DownloadSize;

    /**
     * @return SizeEstimationInformation
     */
    public function getLastEstimationInfo(): ?SizeEstimationInformation;
}
