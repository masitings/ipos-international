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

use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadAccess;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * The DownloadAccess model describes the access for a certain data pool, download type and format within the portal engine.
 * With this event the list of provided download types and formats can be modified dynamically.
 */
class DataPoolDownloadAccessEvent extends Event
{
    private $allowed;
    private $downloadAccess;
    private $token;

    public function __construct(bool $allowed, DownloadAccess $downloadAccess, TokenInterface $token)
    {
        $this->allowed = $allowed;
        $this->downloadAccess = $downloadAccess;
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
    public function getDownloadAccess(): DownloadAccess
    {
        return $this->downloadAccess;
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
