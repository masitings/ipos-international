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

namespace Pimcore\Bundle\StatisticsExplorerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="bundle_statistics_explorer_configuration")
 */
class Configuration
{
    const TABLE = 'bundle_statistics_explorer_configuration';

    /**
     * @var string|null
     *
     * @ORM\Id()
     * @ORM\Column(name="id", type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=100)
     */
    private $context;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer")
     */
    private $ownerId;

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
    private $modificationDate;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text")
     */
    private $configuration;

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string|null $id
     */
    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getContext(): ?string
    {
        return $this->context;
    }

    /**
     * @param string|null $context
     */
    public function setContext(?string $context): void
    {
        $this->context = $context;
    }

    /**
     * @return int|null
     */
    public function getOwnerId(): ?int
    {
        return $this->ownerId;
    }

    /**
     * @param int|null $ownerId
     */
    public function setOwnerId(?int $ownerId): void
    {
        $this->ownerId = $ownerId;
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
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
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
     */
    public function setModificationDate(?int $modificationDate): void
    {
        $this->modificationDate = $modificationDate;
    }

    /**
     * @return string|null
     */
    public function getConfiguration(): ?string
    {
        return $this->configuration;
    }

    /**
     * @param string|null $configuration
     */
    public function setConfiguration(?string $configuration): void
    {
        $this->configuration = $configuration;
    }

    /**
     * @ORM\PrePersist
     */
    public function updateModificationDate()
    {
        $this->setModificationDate(time());
//        if(empty($this->id)) {
//            $this->id = uniqid();
//        }
    }
}
