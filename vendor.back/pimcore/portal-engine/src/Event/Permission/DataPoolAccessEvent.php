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
 * Can be used to block or allow access to a data pool for the current user.
 */
class DataPoolAccessEvent extends Event
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
     * @var UserInterface|PortalUserInterface
     */
    private $user;

    /**
     * DataPoolAccessEvent constructor.
     *
     * @param bool $allowed
     * @param int $dataPoolId
     * @param UserInterface|PortalUserInterface $user
     */
    public function __construct(bool $allowed, int $dataPoolId, UserInterface $user)
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
