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

/**
 * @ORM\Entity()
 * @ORM\Table(name="portal_engine_collection_share")
 */
class CollectionShare
{
    const TABLE = 'portal_engine_collection_share';

    /**
     * @var int|null
     *
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer")
     */
    private $collectionId;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer")
     */
    private $userId;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer")
     */
    private $userGroupId;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=10)
     */
    private $permission;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer")
     */
    private $creationDate;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer")
     */
    private $modificationDate;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     *
     * @return CollectionShare
     */
    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getCollectionId(): ?int
    {
        return $this->collectionId;
    }

    /**
     * @param int|null $collectionId
     *
     * @return CollectionShare
     */
    public function setCollectionId(?int $collectionId): self
    {
        $this->collectionId = $collectionId;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return $this->userId;
    }

    /**
     * @param int|null $userId
     *
     * @return CollectionShare
     */
    public function setUserId(?int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getUserGroupId(): ?int
    {
        return $this->userGroupId;
    }

    /**
     * @param int|null $userGroupId
     *
     * @return CollectionShare
     */
    public function setUserGroupId(?int $userGroupId): self
    {
        $this->userGroupId = $userGroupId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPermission(): ?string
    {
        return $this->permission;
    }

    /**
     * @param string|null $permission
     *
     * @return CollectionShare
     */
    public function setPermission(?string $permission): self
    {
        $this->permission = $permission;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getCreationDate(): ?int
    {
        return $this->creationDate;
    }

    /**
     * @param int|null $creationDate
     *
     * @return CollectionShare
     */
    public function setCreationDate(?int $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getModificationDate(): ?int
    {
        return $this->modificationDate;
    }

    /**
     * @param int|null $modificationDate
     *
     * @return CollectionShare
     */
    public function setModificationDate(?int $modificationDate): self
    {
        $this->modificationDate = $modificationDate;

        return $this;
    }
}
