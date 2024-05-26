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

namespace Pimcore\Bundle\PortalEngineBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;

/**
 * @ORM\Entity()
 * @ORM\Table(name="portal_engine_batch_task", indexes={@Index(columns={"userId"})})
 * @ORM\HasLifecycleCallbacks()
 */
class BatchTask
{
    const TABLE = 'portal_engine_batch_task';

    /**
     * @var int|null
     *
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255)
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=30)
     */
    private $type;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $totalItems;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=20)
     */
    private $state = '';

    /**
     * @var array
     *
     * @ORM\Column(type="json")
     */
    private $payload = [];

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $disableNotificationAction = false;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $disableDeleteConfirmation = false;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer")
     */
    private $createdAt;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer")
     */
    private $modifiedAt;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getUserId(): ?string
    {
        return $this->userId;
    }

    /**
     * @param string|null $userId
     *
     * @return $this
     */
    public function setUserId(?string $userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return BatchTask
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return int
     */
    public function getTotalItems(): int
    {
        return $this->totalItems;
    }

    /**
     * @param int $totalItems
     *
     * @return BatchTask
     */
    public function setTotalItems(int $totalItems): self
    {
        $this->totalItems = $totalItems;

        return $this;
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @param string $state
     *
     * @return BatchTask
     */
    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return array
     */
    public function getPayload(): array
    {
        return $this->payload;
    }

    /**
     * @param array $payload
     *
     * @return BatchTask
     */
    public function setPayload(array $payload): self
    {
        $this->payload = $payload;

        return $this;
    }

    /**
     * @return bool
     */
    public function getDisableNotificationAction(): bool
    {
        return $this->disableNotificationAction;
    }

    /**
     * @param bool $disableNotificationAction
     */
    public function setDisableNotificationAction(bool $disableNotificationAction): self
    {
        $this->disableNotificationAction = $disableNotificationAction;

        return $this;
    }

    /**
     * @return bool
     */
    public function getDisableDeleteConfirmation(): bool
    {
        return $this->disableDeleteConfirmation;
    }

    /**
     * @param bool $disableDeleteConfirmation
     *
     * @return BatchTask
     */
    public function setDisableDeleteConfirmation(bool $disableDeleteConfirmation): self
    {
        $this->disableDeleteConfirmation = $disableDeleteConfirmation;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getCreatedAt(): ?int
    {
        return $this->createdAt;
    }

    /**
     * @param int $createdAt
     *
     * @return BatchTask
     */
    public function setCreatedAt(int $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getModifiedAt(): ?int
    {
        return $this->modifiedAt;
    }

    /**
     * @param int $modifiedAt
     *
     * @return BatchTask
     */
    public function setModifiedAt(int $modifiedAt): self
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {
        $this->setModifiedAt(time());

        if ($this->getCreatedAt() == null) {
            $this->setCreatedAt(time());
        }
    }
}
