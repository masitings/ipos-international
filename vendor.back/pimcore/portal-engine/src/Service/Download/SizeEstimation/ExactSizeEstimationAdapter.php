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

use Pimcore\Bundle\PortalEngineBundle\Model\Download\Downloadable\DownloadableStructuredData;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadItemInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadSize;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\SizeEstimation\SizeEstimationInformation;
use Pimcore\Bundle\PortalEngineBundle\Service\Download\DownloadService;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

class ExactSizeEstimationAdapter implements SizeEstimationAdapterInterface
{
    use LoggerAwareTrait;

    protected $downloadService;
    protected $lastEstimationInformation;

    public function __construct(DownloadService $downloadService, LoggerInterface $logger)
    {
        $this->downloadService = $downloadService;
        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     */
    public function estimate(DownloadItemInterface $downloadItem): DownloadSize
    {
        $downloadables = $this->downloadService->getDownloadablesFromDownloadItem($downloadItem);

        if (empty($downloadables)) {
            return DownloadSize::zero();
        }

        $kb = 0;

        foreach ($downloadables as $downloadable) {
            try {
                // Structured data files normally shouldn't get that big. Therefore we ignore it in the estimation.
                if ($downloadable instanceof DownloadableStructuredData) {
                    continue;
                }

                $file = $downloadable->generate()->getDownloadFilePath();

                if (!file_exists($file)) {
                    $this->logger->info('File was not generated, does not exist.');
                    continue;
                }

                $kb += filesize($file) / 1000;
            } catch (\Exception $e) {
                $this->logger->error('Could not generate downloadable.' . $e);
            }
        }

        $downloadSize = new DownloadSize($kb);
        $this->lastEstimationInformation = new SizeEstimationInformation($downloadables, $downloadSize);

        return $downloadSize;
    }

    /**
     * {@inheritDoc}
     */
    public function getLastEstimationInfo(): ?SizeEstimationInformation
    {
        return $this->lastEstimationInformation;
    }
}
