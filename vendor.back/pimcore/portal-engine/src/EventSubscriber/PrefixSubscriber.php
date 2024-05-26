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

use Pimcore\Bundle\PortalEngineBundle\Service\Document\PrefixService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

/**
 * @package Pimcore\Bundle\PortalEngineBundle\EventListener
 */
class PrefixSubscriber implements EventSubscriberInterface
{
    /**
     * @var PrefixService
     */
    protected $prefixService;

    /**
     * PrefixSubscriber constructor.
     *
     * @param PrefixService $prefixService
     */
    public function __construct(
        PrefixService $prefixService
    ) {
        $this->prefixService = $prefixService;
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.controller' => ['onKernelController'],
        ];
    }

    public function onKernelController(ControllerEvent $requestEvent)
    {
        $this->prefixService->setupRoutingPrefix();
    }
}
