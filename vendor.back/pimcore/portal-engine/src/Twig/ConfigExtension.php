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

namespace Pimcore\Bundle\PortalEngineBundle\Twig;

use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataPoolConfigInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\PortalConfig;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\Frontend\FrontendConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\PortalConfig\PortalConfigService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ConfigExtension extends AbstractExtension
{
    protected $portalConfigService;
    protected $dataPoolConfigService;
    protected $frontendConfigService;

    public function __construct(PortalConfigService $portalConfigService, DataPoolConfigService $dataPoolConfigService, FrontendConfigService $frontendConfigService)
    {
        $this->portalConfigService = $portalConfigService;
        $this->dataPoolConfigService = $dataPoolConfigService;
        $this->frontendConfigService = $frontendConfigService;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('portalEngine_portalConfig', [$this, 'getPortalConfig']),
            new TwigFunction('portalEngine_dataPoolConfig', [$this, 'getDataPoolConfig']),
            new TwigFunction('portalEngine_frontendConfig', [$this, 'getFrontendConfig']),
        ];
    }

    public function getPortalConfig(): ?PortalConfig
    {
        return $this->portalConfigService->getCurrentPortalConfig();
    }

    public function getDataPoolConfig(): ?DataPoolConfigInterface
    {
        return $this->dataPoolConfigService->getCurrentDataPoolConfig();
    }

    public function getFrontendConfig(): FrontendConfigService
    {
        return $this->frontendConfigService;
    }
}
