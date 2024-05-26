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

namespace Pimcore\Bundle\PortalEngineBundle\Event\Permission;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Can be used to block or allow access to a portal for the current user.
 */
class PortalAccessEvent extends Event
{
    /**
     * @var bool
     */
    private $allowed;

    /**
     * @var int
     */
    private $portalId;

    /**
     * @var TokenInterface
     */
    private $token;

    /**
     * @param bool $allowed
     * @param int $portalId
     * @param TokenInterface $token
     */
    public function __construct(bool $allowed, int $portalId, TokenInterface $token)
    {
        $this->allowed = $allowed;
        $this->portalId = $portalId;
        $this->token = $token;
    }

    /**
     * @return bool
     */
    public function isAllowed(): bool
    {
        return $this->allowed;
    }

    /**
     * @return int
     */
    public function getPortalId(): int
    {
        return $this->portalId;
    }

    /**
     * @return TokenInterface
     */
    public function getToken(): TokenInterface
    {
        return $this->token;
    }

    /**
     * @param bool $allowed
     */
    public function setAllowed(bool $allowed): void
    {
        $this->allowed = $allowed;
    }
}
