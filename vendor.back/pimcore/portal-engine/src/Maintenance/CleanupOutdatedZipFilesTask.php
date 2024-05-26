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

namespace Pimcore\Bundle\PortalEngineBundle\Maintenance;

use Pimcore\Bundle\PortalEngineBundle\Service\Download\StorageService;
use Pimcore\Bundle\PortalEngineBundle\Service\Zip\ZipArchiveService;
use Pimcore\Maintenance\TaskInterface;

/**
 * Class CleanupOutdatedZipFilesTask
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Maintenance
 */
class CleanupOutdatedZipFilesTask implements TaskInterface
{
    /** @var ZipArchiveService */
    protected $zipArchiveService;

    /** @var StorageService */
    protected $storageService;

    /**
     * CleanupOutdatedZipFilesTask constructor.
     *
     * @param ZipArchiveService $zipArchiveService
     * @param StorageService $storageService
     */
    public function __construct(ZipArchiveService $zipArchiveService, StorageService $storageService)
    {
        $this->zipArchiveService = $zipArchiveService;
        $this->storageService = $storageService;
    }

    /**
     * Execute the Task
     */
    public function execute()
    {
        $this->zipArchiveService->cleanupOutdatedZipFiles();
        $this->storageService->cleanupOutdatedZipFiles();
    }
}
