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

namespace Pimcore\Bundle\PortalEngineBundle\MessageHandler\BatchTask\Traits;

use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolService;

trait RefreshIndexTrait
{
    /**
     * @var DataPoolConfigService
     */
    protected $dataPoolConfigService;

    /**
     * @var DataPoolService
     */
    protected $dataPoolService;

    public function refreshIndexByDataPoolId(int $dataPoolId): ?array
    {
        $this->dataPoolConfigService->setCurrentDataPoolConfigById($dataPoolId);

        if (!$dataPoolConfig = $this->dataPoolConfigService->getCurrentDataPoolConfig()) {
            return null;
        }
        $dataPool = $this->dataPoolService->getDataPoolByConfig($dataPoolConfig);

        $result = $dataPool
            ->getIndexService()
            ->refreshIndex($dataPool->getSearchService()->getESIndexName());

        return $result;
    }

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
}
