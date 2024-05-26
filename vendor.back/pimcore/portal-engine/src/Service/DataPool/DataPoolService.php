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

namespace Pimcore\Bundle\PortalEngineBundle\Service\DataPool;

use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataPoolConfigInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;

class DataPoolService
{
    /**
     * @var ServiceLocator
     */
    protected $serviceLocator;

    /**
     * @var DataPoolConfigService
     */
    protected $dataPoolConfigService;

    /**
     * @param ServiceLocator $serviceLocator
     */
    public function __construct(ServiceLocator $serviceLocator, DataPoolConfigService $dataPoolConfigService)
    {
        $this->serviceLocator = $serviceLocator;
        $this->dataPoolConfigService = $dataPoolConfigService;
    }

    public function getDataPoolByConfig(DataPoolConfigInterface $dataPoolConfig): DataPoolInterface
    {
        $className = get_class($dataPoolConfig);

        return $this->serviceLocator->get($className);
    }

    public function getCurrentDataPool(): ?DataPoolInterface
    {
        if ($dataPoolConfig = $this->dataPoolConfigService->getCurrentDataPoolConfig()) {
            return $this->getDataPoolByConfig($dataPoolConfig);
        }

        return null;
    }
}
