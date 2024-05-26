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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Download;

use Pimcore\Bundle\PortalEngineBundle\Event\Download\DownloadAssetPathInZipEvent;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\BundleStructuredData;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\Downloadable\DownloadableInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\Downloadable\DownloadableStructuredData;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadItemInterface;
use Pimcore\Bundle\PortalEngineBundle\Service\BatchTask\BatchTaskService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DownloadFormatHandler;
use Pimcore\Bundle\PortalEngineBundle\Service\Zip\ContainedInZipService;
use Pimcore\Bundle\PortalEngineBundle\Service\Zip\ZipArchiveService;
use Pimcore\Bundle\PortalEngineBundle\Traits\LoggerAware;
use Pimcore\Helper\TemporaryFileHelperTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DownloadZipGenerationService
{
    use LoggerAware;
    use TemporaryFileHelperTrait;

    /**
     * @var BatchTaskService
     */
    protected $batchTaskService;

    /**
     * @var ZipArchiveService
     */
    protected $zipArchiveService;

    /**
     * @var ContainedInZipService
     */
    protected $containedInZipService;

    /**
     * @var DownloadService
     */
    protected $downloadService;

    /**
     * @var DownloadFormatHandler
     */
    protected $downloadFormatHandler;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param BatchTaskService $batchTaskService
     * @param ZipArchiveService $zipArchiveService
     * @param ContainedInZipService $containedInZipService
     * @param DownloadService $downloadService
     * @param DownloadFormatHandler $downloadFormatHandler
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        BatchTaskService $batchTaskService,
        ZipArchiveService $zipArchiveService,
        ContainedInZipService $containedInZipService,
        DownloadService $downloadService,
        DownloadFormatHandler $downloadFormatHandler,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->batchTaskService = $batchTaskService;
        $this->zipArchiveService = $zipArchiveService;
        $this->containedInZipService = $containedInZipService;
        $this->downloadService = $downloadService;
        $this->downloadFormatHandler = $downloadFormatHandler;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function getDefaultDownloadFilename(): string
    {
        return sprintf('download_%s.zip', date('Y-m-d_H-i-s'));
    }

    /**
     * @param DownloadItemInterface $downloadItem
     * @param string $zipId
     *
     * @return BundleStructuredData[]
     *
     * @throws \Exception
     */
    public function addDownloadItemToZip(DownloadItemInterface $downloadItem, string $zipId, bool $includeDataPoolFolder = false): array
    {
        return $this->addDownloadablesToZip(
            $this->downloadService->getDownloadablesFromDownloadItem($downloadItem, $zipId),
            $zipId,
            $includeDataPoolFolder
        );
    }

    /**
     * @param DownloadableInterface[] $downloadables
     * @param string $zipId
     * @param bool $includeDataPoolFolder
     *
     * @return BundleStructuredData[]
     *
     * @throws \Exception
     */
    public function addDownloadablesToZip(array $downloadables, string $zipId, bool $includeDataPoolFolder = false): array
    {
        $bundleStructuredData = [];
        foreach ($downloadables as $downloadable) {
            if ($downloadable instanceof DownloadableStructuredData) {
                $downloadable->generate();
                $downloadUniqid = $downloadable->getDownloadUniqid();
                $bundleStructuredData[] = new BundleStructuredData(
                    $downloadable->getDownloadUniqid(),
                    $downloadable->getDownloadFormat(),
                    $includeDataPoolFolder ? $downloadable->getDataPoolConfig()->getDataPoolName() : null
                );
            } else {
                $fileSystemPath = $downloadable->generate()->getDownloadFilePath();
                $pathInZip = $this->buildPathInZip($downloadable, $includeDataPoolFolder);
                $this->addToZip($zipId, $fileSystemPath, $pathInZip);
            }
        }

        return $this->uniqueBundleStructuredData($bundleStructuredData);
    }

    public function buildPathInZip(DownloadableInterface $downloadable, bool $includeDataPoolFolder): string
    {
        if ($includeDataPoolFolder) {
            $pathInZip = $downloadable->getDataPoolConfig()->getDataPoolName() . '/' .$downloadable->getDownloadFileName();
        } else {
            $pathInZip = $downloadable->getDownloadFileName();
        }

        $event = new DownloadAssetPathInZipEvent($downloadable, $pathInZip, $includeDataPoolFolder);
        $this->eventDispatcher->dispatch($event);

        return $event->getPathInZip();
    }

    public function addStreamToZip($stream, string $pathInZip, string $zipId)
    {
        $localTmpFile = self::getTemporaryFileFromStream($stream);
        $this->addToZip($zipId, $localTmpFile, $pathInZip);
        unlink($localTmpFile);
    }

    /**
     * @param BundleStructuredData[] $bundleStructuredData
     * @param string $zipId
     */
    public function bundleStructuredDataDownloadFormatsIntoZip(array $bundleStructuredData, string $zipId)
    {
        $uniqueStructuredDownloadFormats = $this->uniqueBundleStructuredData($bundleStructuredData);

        foreach ($uniqueStructuredDownloadFormats as $structuredDataDownloadFormat) {
            if (!$downloadFormatService = $this->downloadFormatHandler->getDownloadFormatService($structuredDataDownloadFormat->getDownloadFormat())) {
                continue;
            }

            $fileSystemPath = $downloadFormatService->bundle($structuredDataDownloadFormat->getDownloadUniquid());
            $targetSubfolder = $structuredDataDownloadFormat->getTargetSubfolder();
            $pathInZip = $targetSubfolder ? $targetSubfolder . '/' : '';
            $pathInZip .= $downloadFormatService->getDownloadFilename($structuredDataDownloadFormat->getDownloadUniquid());
            $this->addToZip($zipId, $fileSystemPath, $pathInZip);
            unlink($fileSystemPath);
        }
    }

    /**
     * @param BundleStructuredData[]
     *
     * @return BundleStructuredData[]
     */
    public function uniqueBundleStructuredData(array $structuredDataDownloadFormats): array
    {
        $uniqueStructuredDownloadFormats = [];
        foreach ($structuredDataDownloadFormats as $structuredDataDownloadFormat) {
            $key = $structuredDataDownloadFormat->getDownloadUniquid() . '__' . $structuredDataDownloadFormat->getDownloadFormat();
            if (!array_key_exists($key, $uniqueStructuredDownloadFormats)) {
                $uniqueStructuredDownloadFormats[$key] = $structuredDataDownloadFormat;
            }
        }

        return array_values($uniqueStructuredDownloadFormats);
    }

    protected function addToZip(string $zipId, string $fileSystemPath, string $pathInZip)
    {
        if (!$this->containedInZipService->isContainedInZip($zipId, $fileSystemPath, $pathInZip)) {
            $zipFile = $this->zipArchiveService->getZipFilesytemPathByZipId($zipId);

            $this->logger->debug(sprintf('Add %s to %s', $pathInZip, $zipFile));
            $this->zipArchiveService->addFileToZip($zipFile, $fileSystemPath, $pathInZip, $zipId);
            $this->containedInZipService->markAsContainedInZip($zipId, $fileSystemPath, $pathInZip);
        }
    }
}
