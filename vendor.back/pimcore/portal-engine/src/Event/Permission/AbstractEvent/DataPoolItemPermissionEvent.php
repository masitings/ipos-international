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

namespace Pimcore\Bundle\PortalEngineBundle\Event\Permission\AbstractEvent;

use Pimcore\Bundle\PortalEngineBundle\Model\DataObject\PortalUserInterface;
use Pimcore\Model\Element\ElementInterface;
use Pimcore\Model\Element\Service;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

abstract class DataPoolItemPermissionEvent extends Event
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
     * @var string
     */
    private $subjectFullPath;

    /**
     * @var string
     */
    private $subjectType;

    /**
     * @var PortalUserInterface|UserInterface
     */
    private $user;

    /**
     * @param bool $allowed
     * @param int $dataPoolId
     * @param string $subjectFullPath
     * @param string $subjectType
     * @param PortalUserInterface|UserInterface $user
     */
    public function __construct(bool $allowed, int $dataPoolId, string $subjectFullPath, string $subjectType, PortalUserInterface $user)
    {
        $this->allowed = $allowed;
        $this->dataPoolId = $dataPoolId;
        $this->subjectFullPath = $subjectFullPath;
        $this->subjectType = $subjectType;
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
     * @return int
     */
    public function getSubjectFullPath()
    {
        return $this->subjectFullPath;
    }

    /**
     * @return string
     */
    public function getSubjectType(): string
    {
        return $this->subjectType;
    }

    /**
     * @return null|ElementInterface
     */
    public function getSubject()
    {
        return Service::getElementByPath($this->subjectType, $this->subjectFullPath);
    }

    /**
     * @param bool $allowed
     */
    public function setAllowed(bool $allowed): void
    {
        $this->allowed = $allowed;
    }
}
