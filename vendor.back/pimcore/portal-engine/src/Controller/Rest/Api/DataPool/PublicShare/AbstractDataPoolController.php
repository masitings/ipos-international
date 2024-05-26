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

namespace Pimcore\Bundle\PortalEngineBundle\Controller\Rest\Api\DataPool\PublicShare;

use Pimcore\Bundle\PortalEngineBundle\Traits\PublicShareAware;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

/**
 * Class AbstractRestApiController
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Controller\Rest\Api
 */
abstract class AbstractDataPoolController extends \Pimcore\Bundle\PortalEngineBundle\Controller\Rest\Api\DataPool\AbstractDataPoolController
{
    use PublicShareAware;

    /**
     * @param ControllerEvent $event
     */
    public function onKernelControllerEvent(ControllerEvent $event)
    {
        $this->setupPublicShareByRequest($event->getRequest());
        $this->setupDataPoolConfigByRequest($event->getRequest());

        parent::onKernelControllerEvent($event);
    }
}
