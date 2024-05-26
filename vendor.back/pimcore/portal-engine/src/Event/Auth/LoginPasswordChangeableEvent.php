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

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Fires in the checkCredentials() method of the authenticator.
 * Can be used to validate the user password via external authentication systems.
 * You will find a description and example on how it works in the portal engine docs.
 */
class LoginPasswordChangeableEvent extends Event
{
    /** @var bool */
    protected $passwordChangeable = true;

    /**
     * @return bool
     */
    public function getPasswordChangeable(): bool
    {
        return $this->passwordChangeable;
    }

    /**
     * @param bool $passwordChangeable
     *
     * @return LoginPasswordChangeableEvent
     */
    public function setPasswordChangeable(bool $passwordChangeable): self
    {
        $this->passwordChangeable = $passwordChangeable;

        return $this;
    }
}
