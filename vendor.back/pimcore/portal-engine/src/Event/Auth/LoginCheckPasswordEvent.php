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
 * Fires before a password recovery email will be sent.
 * Can be used to customize the HTML body of the mail content.
 * Hint: for most use cases it should be enough to just overwrite the twig template.
 */
class LoginCheckPasswordEvent extends Event
{
    /** @var string */
    protected $password;
    /** @var PortalUserInterface */
    protected $portalUser;
    /** @var bool|null */
    protected $loginValid;

    /**
     * @param PortalUserInterface $portalUser
     * @param string $password
     */
    public function __construct(PortalUserInterface $portalUser, string $password)
    {
        $this->portalUser = $portalUser;
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return bool|null
     */
    public function getLoginValid(): ?bool
    {
        return $this->loginValid;
    }

    /**
     * @param bool|null $loginValid
     *
     * @return LoginCheckPasswordEvent
     */
    public function setLoginValid(?bool $loginValid): self
    {
        $this->loginValid = $loginValid;

        return $this;
    }

    /**
     * @return PortalUserInterface
     */
    public function getPortalUser(): PortalUserInterface
    {
        return $this->portalUser;
    }
}
