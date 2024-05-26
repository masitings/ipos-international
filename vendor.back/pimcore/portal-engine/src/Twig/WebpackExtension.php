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

use Pimcore\Bundle\PortalEngineBundle\Service\PortalConfig\FrontendBuildService;
use Pimcore\Bundle\PortalEngineBundle\Service\PortalConfig\PortalConfigService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class WebpackExtension extends AbstractExtension
{
    const BUILD_BUNDLE = 'portalEngineBundle';
    const BUILD_PORTAL_CONFIG = 'portalEngineAppPortalConfig';
    const BUILD_CUSTOMIZED = 'portalEngineApp';

    /**
     * @var PortalConfigService
     */
    protected $portalConfigService;

    private $build;

    /**
     * WebpackExtension constructor.
     *
     * @param PortalConfigService $portalConfigService
     */
    public function __construct(PortalConfigService $portalConfigService)
    {
        $this->portalConfigService = $portalConfigService;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('webpack_build', [$this, 'webpackBuild'])
        ];
    }

    public function webpackBuild(): string
    {
        if (is_null($this->build)) {
            $portalConfig = $this->portalConfigService->getCurrentPortalConfig();
            $customizedFrontendBuild = $portalConfig->getCustomizedFrontendBuild();
            $portalId = $portalConfig->getPortalId();

            if ($customizedFrontendBuild && file_exists(FrontendBuildService::APP_FRONTEND_CUSTOMIZED_FRONTEND_BUILDS . '/build/' . $customizedFrontendBuild . '/entrypoints.json')) {
                $this->build = self::BUILD_CUSTOMIZED . '_' . $customizedFrontendBuild;
            } elseif (!$customizedFrontendBuild && file_exists(FrontendBuildService::APP_FRONTEND_ROOT . '/build/portal_' . $portalId . '/entrypoints.json')) {
                $this->build = self::BUILD_PORTAL_CONFIG . '_' . $portalId;
            } else {
                $this->build = self::BUILD_BUNDLE;
            }
        }

        return $this->build;
    }
}
