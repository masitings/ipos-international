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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Security\Firewall;

use Pimcore\Bundle\PortalEngineBundle\EventSubscriber\RequestSubscriber;
use Pimcore\Bundle\PortalEngineBundle\Service\PortalConfig\PortalConfigService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;

class RequestMatcher implements RequestMatcherInterface
{
    /**
     * @var PortalConfigService
     */
    protected $portalConfigService;

    /**
     * RequestMatcher constructor.
     *
     * @param PortalConfigService $portalConfigService
     */
    public function __construct(PortalConfigService $portalConfigService)
    {
        $this->portalConfigService = $portalConfigService;
    }

    public function matches(Request $request)
    {
        return $request->attributes->get(RequestSubscriber::IS_PORTAL_ENGINE_SITE);
    }
}
