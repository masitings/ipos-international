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

namespace Pimcore\Bundle\PortalEngineBundle\Controller;

use Pimcore\Controller\FrontendController;
use Pimcore\Controller\KernelControllerEventInterface;
use Pimcore\Model\Site;
use Pimcore\Tool;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

class AbstractSiteController extends FrontendController implements KernelControllerEventInterface
{
    public function onKernelControllerEvent(ControllerEvent $event)
    {
        if (!Site::isSiteRequest() && !Tool::isFrontendRequestByAdmin() && !Tool\Authentication::authenticateSession($event->getRequest())) {
            throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException(sprintf('No routes found for "%s".', $event->getRequest()->getRequestUri()));
        }
    }
}
