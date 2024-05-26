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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Zip;

use Pimcore\Bundle\PortalEngineBundle\Service\Download\DownloadTmpStoreService;

class ContainedInZipService
{
    /**
     * @var DownloadTmpStoreService
     */
    protected $tmpStoreService;

    /**
     * @param DownloadTmpStoreService $tmpStoreService
     */
    public function __construct(DownloadTmpStoreService $tmpStoreService)
    {
        $this->tmpStoreService = $tmpStoreService;
    }

    /**
     * @param string $zipId
     * @param string $fileSystemPath
     * @param string $pathWithinZip
     *
     * @return bool
     */
    public function isContainedInZip(string $zipId, string $fileSystemPath, string $pathWithinZip): bool
    {
        $json = $this->getContainedInZipJson($zipId);

        return isset($json[$fileSystemPath][$pathWithinZip]);
    }

    /**
     * @param string $zipId
     * @param string $fileSystemPath
     * @param string $pathWithinZip
     */
    public function markAsContainedInZip(string $zipId, string $fileSystemPath, string $pathWithinZip)
    {
        $json = $this->getContainedInZipJson($zipId);
        $json[$fileSystemPath][$pathWithinZip] = 1;
        $this->tmpStoreService->setTmpStoreData('ZIP_' . $zipId, $json);
    }

    /**
     * @param string $zipId
     *
     * @return array
     */
    private function getContainedInZipJson(string $zipId): array
    {
        return $this->tmpStoreService->getTmpStoreData('ZIP_' . $zipId);
    }

    public function clearStore(string $zipId)
    {
//        $this->tmpStoreService->clearTmpSToreData('ZIP_' . $zipId);
    }
}
