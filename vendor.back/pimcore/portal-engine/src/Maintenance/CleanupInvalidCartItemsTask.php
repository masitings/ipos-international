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

use Pimcore\Bundle\PortalEngineBundle\Service\Download\DownloadCartService;
use Pimcore\Maintenance\TaskInterface;

class CleanupInvalidCartItemsTask implements TaskInterface
{
    /**
     * @var DownloadCartService
     */
    protected $downloadCartService;

    /**
     * @param DownloadCartService $downloadCartService
     */
    public function __construct(DownloadCartService $downloadCartService)
    {
        $this->downloadCartService = $downloadCartService;
    }

    /**
     * @throws \Exception
     */
    public function execute()
    {
        $this->downloadCartService->cleanupInvalidDownloadCartItems();
    }
}
