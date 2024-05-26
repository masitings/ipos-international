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
 * @ORM\Table(name="portal_engine_collection")
 */
class Collection
{
    const TABLE = 'portal_engine_collection';

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
    private $userId;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer")
     */
    private $currentSiteId;

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
     * @return $this
     */
    public function setId(?int $id)
    {
        $this->id = $id;

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
     * @return $this
     */
    public function setUserId(?int $userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     *
     * @return Collection
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getCurrentSiteId(): ?int
    {
        return $this->currentSiteId;
    }

    /**
     * @param int|null $currentSiteId
     *
     * @return Collection
     */
    public function setCurrentSiteId(?int $currentSiteId): self
    {
        $this->currentSiteId = $currentSiteId;

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
     * @return Collection
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
     * @return Collection
     */
    public function setModificationDate(?int $modificationDate): self
    {
        $this->modificationDate = $modificationDate;

        return $this;
    }
}
