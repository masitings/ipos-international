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

namespace Pimcore\Bundle\PortalEngineBundle\Event\Auth;

use Pimcore\Bundle\PortalEngineBundle\Model\DataObject\PortalUserInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Fires in the getUser() method of the authenticator.
 * Can be used to fetch the user from custom authentication systems.
 * You will find a description and example on how it works in the portal engine docs.
 */
class LoginGetUserEvent extends Event
{
    /** @var string */
    protected $userName;
    /** @var string */
    protected $password;
    /** @var PortalUserInterface|null */
    protected $portalUser;
    /** @var bool */
    protected $portalUserResolved = false;

    /**
     * @param string $userName
     * @param string $password
     */
    public function __construct(string $userName, string $password)
    {
        $this->userName = $userName;
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getUserName(): string
    {
        return $this->userName;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return PortalUserInterface|null
     */
    public function getPortalUser(): ?PortalUserInterface
    {
        return $this->portalUser;
    }

    /**
     * @param PortalUserInterface|null $portalUser
     *
     * @return LoginGetUserEvent
     */
    public function setPortalUser(?PortalUserInterface $portalUser): self
    {
        $this->portalUser = $portalUser;
        $this->portalUserResolved = true;

        return $this;
    }

    /**
     * @return bool
     */
    public function getPortalUserResolved(): bool
    {
        return $this->portalUserResolved;
    }
}
