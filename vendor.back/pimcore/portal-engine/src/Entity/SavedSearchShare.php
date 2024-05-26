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
 * @ORM\Table(name="portal_engine_saved_search_share")
 */
class SavedSearchShare
{
    const TABLE = 'portal_engine_saved_search_share';

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
    private $searchId;

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
     * @return SavedSearchShare
     */
    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getSearchId(): ?int
    {
        return $this->searchId;
    }

    /**
     * @param int|null $searchId
     *
     * @return SavedSearchShare
     */
    public function setSearchId(?int $searchId): self
    {
        $this->searchId = $searchId;

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
     * @return SavedSearchShare
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
     * @return SavedSearchShare
     */
    public function setUserGroupId(?int $userGroupId): self
    {
        $this->userGroupId = $userGroupId;

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
     * @return SavedSearchShare
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
     * @return SavedSearchShare
     */
    public function setModificationDate(?int $modificationDate): self
    {
        $this->modificationDate = $modificationDate;

        return $this;
    }
}
