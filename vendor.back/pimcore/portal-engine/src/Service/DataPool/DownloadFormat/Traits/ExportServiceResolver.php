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

namespace Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DownloadFormat\Traits;

use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolService;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Export\ExportServiceInterface;

trait ExportServiceResolver
{
    /**
     * @var DataPoolConfigService
     */
    protected $dataPoolConfigService;

    /**
     * @var DataPoolService
     */
    protected $dataPoolService;

    /**
     * @param DataPoolConfigService $dataPoolConfigService
     * @required
     */
    public function setDataPoolConfigService(DataPoolConfigService $dataPoolConfigService)
    {
        $this->dataPoolConfigService = $dataPoolConfigService;
    }

    /**
     * @param DataPoolService $dataPoolService
     * @required
     */
    public function setDataPoolService(DataPoolService $dataPoolService)
    {
        $this->dataPoolService = $dataPoolService;
    }

    public function getExportService(): ExportServiceInterface
    {
        $dataPoolConfig = $this->dataPoolConfigService->getCurrentDataPoolConfig();
        $dataPool = $this->dataPoolService->getDataPoolByConfig($dataPoolConfig);

        return  $dataPool->getExportService();
    }
}
