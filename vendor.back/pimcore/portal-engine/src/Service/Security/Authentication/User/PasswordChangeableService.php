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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Security\Authentication\User;

use Pimcore\Bundle\PortalEngineBundle\Event\Auth\LoginPasswordChangeableEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class PasswordChangeableService
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\Security\Authentication\User
 */
class PasswordChangeableService
{
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function isPasswordChangeable(): bool
    {
        $event = new LoginPasswordChangeableEvent();
        $this->eventDispatcher->dispatch($event);

        return $event->getPasswordChangeable();
    }
}
