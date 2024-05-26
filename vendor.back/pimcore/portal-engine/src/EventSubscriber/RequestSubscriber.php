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

namespace Pimcore\Bundle\PortalEngineBundle\EventSubscriber;

use Pimcore\Bundle\PortalEngineBundle\Service\PortalConfig\PortalConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\SecurityService;
use Pimcore\Tool;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class RequestSubscriber implements EventSubscriberInterface
{
    const IS_PORTAL_ENGINE_SITE = 'isPortalEngineSite';

    protected $portalConfigService;
    protected $securityService;

    public function __construct(PortalConfigService $portalConfigService, SecurityService $securityService)
    {
        $this->portalConfigService = $portalConfigService;
        $this->securityService = $securityService;
    }

    public static function getSubscribedEvents()
    {
        return [
            RequestEvent::class => ['onKernelRequest', 33],
        ];
    }

    public function onKernelRequest(RequestEvent $requestEvent)
    {
        if ($this->portalConfigService->isPortalEngineSite() || Tool::isFrontendRequestByAdmin()) {
            $requestEvent->getRequest()->attributes->set(self::IS_PORTAL_ENGINE_SITE, true);
        } elseif ($this->securityService->isAdminRestApiCall()) {
            $requestEvent->getRequest()->attributes->set(self::IS_PORTAL_ENGINE_SITE, true);
        }
    }
}
