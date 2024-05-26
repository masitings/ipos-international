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
 * Fires while the login form gets created.
 * Can be used switch the login field from an email field to a regular input field.
 * You will find a description and example on how it works in the portal engine docs.
 */
class LoginFieldTypeEvent extends Event
{
    /** @var bool */
    protected $useEmailField = true;

    /**
     * @return bool
     */
    public function getUseEmailField(): bool
    {
        return $this->useEmailField;
    }

    /**
     * @param bool $useEmailField
     *
     * @return LoginFieldTypeEvent
     */
    public function setUseEmailField(bool $useEmailField): self
    {
        $this->useEmailField = $useEmailField;

        return $this;
    }
}
