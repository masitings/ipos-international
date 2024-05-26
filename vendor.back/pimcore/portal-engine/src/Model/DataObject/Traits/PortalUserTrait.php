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

namespace Pimcore\Bundle\PortalEngineBundle\Model\DataObject\Traits;

trait PortalUserTrait
{
    protected $portalShareUser = false;

    /**
     * @var string|null
     */
    protected $publicShareUserId;

    public function getRoles()
    {
        return [];
    }

    public function getPassword()
    {
        return $this->getPortalPassword();
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function getUsername()
    {
        return $this->getExternalUserId() ?? $this->getEmail();
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function isPortalShareUser(): bool
    {
        return $this->portalShareUser;
    }

    /**
     * @param bool $portalShareUser
     *
     * @return static
     */
    public function setPortalShareUser(bool $portalShareUser)
    {
        $this->portalShareUser = $portalShareUser;

        return $this;
    }

    /**
     * @param string|null $publicShareId
     */
    public function setPublicShareUserId(string $publicShareUserId = null)
    {
        $this->publicShareUserId = $publicShareUserId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPublicShareUserId(): ?string
    {
        return $this->publicShareUserId;
    }

    /**
     * @return string|int|null
     */
    public function getPortalUserId()
    {
        return $this->getPublicShareUserId() ?? $this->getId();
    }
}
