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
 * @ORM\Table(name="portal_engine_public_share")
 */
class PublicShare
{
    const TABLE = 'portal_engine_public_share';

    /**
     * @var int
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
     * @var string
     *
     * @ORM\Column(type="string", length=32)
     */
    private $hash;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $userId;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $currentSiteId;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer")
     */
    private $expiryDate;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $showTermsText;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text")
     */
    private $termsText;

    /**
     * @var []
     *
     * @ORM\Column(type="jsonfy")
     */
    private $configs = [];

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $creationDate;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $modificationDate;

    /**
     * @return bool
     */
    public function isExpired()
    {
        return $this->expiryDate && $this->expiryDate < time();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return PublicShare
     */
    public function setId(int $id): self
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
     * @return PublicShare
     */
    public function setCollectionId(?int $collectionId): self
    {
        $this->collectionId = $collectionId;

        return $this;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     *
     * @return PublicShare
     */
    public function setHash(string $hash): self
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return PublicShare
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     *
     * @return PublicShare
     */
    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @return int
     */
    public function getCurrentSiteId(): int
    {
        return $this->currentSiteId;
    }

    /**
     * @param int $currentSiteId
     *
     * @return PublicShare
     */
    public function setCurrentSiteId(int $currentSiteId): self
    {
        $this->currentSiteId = $currentSiteId;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getExpiryDate(): ?int
    {
        return $this->expiryDate;
    }

    /**
     * @param int|null $expiryDate
     *
     * @return PublicShare
     */
    public function setExpiryDate(?int $expiryDate): self
    {
        $this->expiryDate = $expiryDate;

        return $this;
    }

    /**
     * @return bool
     */
    public function isShowTermsText(): bool
    {
        return $this->showTermsText;
    }

    /**
     * @param bool $showTermsText
     *
     * @return PublicShare
     */
    public function setShowTermsText(bool $showTermsText): self
    {
        $this->showTermsText = $showTermsText;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTermsText(): ?string
    {
        return $this->termsText;
    }

    /**
     * @param string|null $termsText
     *
     * @return PublicShare
     */
    public function setTermsText(?string $termsText): self
    {
        $this->termsText = $termsText;

        return $this;
    }

    /**
     * @return []
     */
    public function getConfigs(): array
    {
        return $this->configs;
    }

    /**
     * @param [] $configs
     *
     * @return PublicShare
     */
    public function setConfigs(array $configs): self
    {
        $this->configs = $configs;

        return $this;
    }

    /**
     * @return int
     */
    public function getCreationDate(): int
    {
        return $this->creationDate;
    }

    /**
     * @param int $creationDate
     *
     * @return PublicShare
     */
    public function setCreationDate(int $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * @return int
     */
    public function getModificationDate(): int
    {
        return $this->modificationDate;
    }

    /**
     * @param int $modificationDate
     *
     * @return PublicShare
     */
    public function setModificationDate(int $modificationDate): self
    {
        $this->modificationDate = $modificationDate;

        return $this;
    }
}
