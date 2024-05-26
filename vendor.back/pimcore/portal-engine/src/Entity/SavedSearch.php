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
 * @ORM\Table(name="portal_engine_saved_search")
 */
class SavedSearch
{
    const TABLE = 'portal_engine_saved_search';

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
     * @var string|null
     *
     * @ORM\Column(type="string", length=255)
     */
    private $urlQuery;

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
     * @return SavedSearch
     */
    public function setId(?int $id): self
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
     * @return SavedSearch
     */
    public function setUserId(?int $userId): self
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
     * @return SavedSearch
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
     * @return SavedSearch
     */
    public function setCurrentSiteId(?int $currentSiteId): self
    {
        $this->currentSiteId = $currentSiteId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUrlQuery(): ?string
    {
        return $this->urlQuery;
    }

    /**
     * @param string|null $urlQuery
     *
     * @return SavedSearch
     */
    public function setUrlQuery(?string $urlQuery): self
    {
        $this->urlQuery = $urlQuery;

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
     * @return SavedSearch
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
     * @return SavedSearch
     */
    public function setModificationDate(?int $modificationDate): self
    {
        $this->modificationDate = $modificationDate;

        return $this;
    }
}
