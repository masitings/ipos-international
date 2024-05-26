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

use Pimcore\Bundle\PortalEngineBundle\Model\DataObject\PortalUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Can be used to deny or allow the version history for a given data pool and user.
 */
class DataPoolVersionHistoryEvent extends Event
{
    /**
     * @var bool
     */
    private $allowed;

    /**
     * @var int
     */
    private $dataPoolId;

    /**
     * @var PortalUserInterface|UserInterface
     */
    private $user;

    /**
     * @param bool $allowed
     * @param int $dataPoolId
     * @param PortalUserInterface|UserInterface $user
     */
    public function __construct(bool $allowed, int $dataPoolId, PortalUserInterface $user)
    {
        $this->allowed = $allowed;
        $this->dataPoolId = $dataPoolId;
        $this->user = $user;
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
    public function getDataPoolId(): int
    {
        return $this->dataPoolId;
    }

    /**
     * @return PortalUserInterface|UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param bool $allowed
     */
    public function setAllowed(bool $allowed): void
    {
        $this->allowed = $allowed;
    }
}
