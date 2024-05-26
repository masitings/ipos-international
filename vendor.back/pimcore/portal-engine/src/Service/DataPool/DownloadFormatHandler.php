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

use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DownloadFormat\DownloadFormatInterface;

class DownloadFormatHandler
{
    /**
     * @var DataPoolConfigService
     */
    protected $dataPoolConfigService;

    /**
     * @var DownloadFormatInterface[]
     */
    protected $downloadFormatServices = [];

    /**
     * @param DataPoolConfigService $dataPoolConfigService
     */
    public function __construct(DataPoolConfigService $dataPoolConfigService)
    {
        $this->dataPoolConfigService = $dataPoolConfigService;
    }

    public function addDownloadFormatService(string $serviceId, DownloadFormatInterface $downloadFormatService)
    {
        $this->downloadFormatServices[$serviceId] = $downloadFormatService;
    }

    public function getDownloadFormatServicesSelectStore(bool $checkSupports = true): array
    {
        $result = [];
        foreach ($this->downloadFormatServices as $serviceId => $downloadService) {
            if (!$checkSupports || $downloadService->supports($this->dataPoolConfigService->getCurrentDataPoolConfig())) {
                $result[] = [$serviceId, $downloadService->getDisplayName()];
            }
        }

        return $result;
    }

    public function getDownloadFormatService(string $serviceId): ?DownloadFormatInterface
    {
        return $this->downloadFormatServices[$serviceId] ?? null;
    }
}
