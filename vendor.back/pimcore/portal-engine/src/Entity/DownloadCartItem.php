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
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadConfig;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadItemInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadItemTrait;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="portal_engine_download_cart_item")
 */
class DownloadCartItem extends AbstractElementItem implements DownloadItemInterface
{
    const TABLE = 'portal_engine_download_cart_item';

    use DownloadItemTrait;

    /**
     * @var DownloadCart|null
     *
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="Pimcore\Bundle\PortalEngineBundle\Entity\DownloadCart")
     * @ORM\JoinColumn(name="cartId", referencedColumnName="id")
     */
    private $cart;

    /**
     * @var DownloadConfig[]
     *
     * @ORM\Column(type="jsonfy")
     */
    private $configs = [];

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
     * @return DownloadCart|null
     */
    public function getCart(): ?DownloadCart
    {
        return $this->cart;
    }

    /**
     * @param DownloadCart $cart
     *
     * @return $this
     */
    public function setCart(DownloadCart $cart)
    {
        $this->cart = $cart;

        return $this;
    }

    /**
     * @param DownloadConfig[] $configs
     *
     * @return $this
     */
    public function setConfigs(array $configs)
    {
        $this->configs = $configs;

        return $this;
    }

    /**
     * @ORM\PostLoad
     */
    public function mapConfigs()
    {
        $this->configs = array_map(function (array $data) {
            $config = new DownloadConfig();
            $config->add($data);

            return $config;
        }, $this->getConfigs() ?: []);
    }

    /**
     * @return DownloadConfig[]
     */
    public function getConfigs(): array
    {
        return $this->configs;
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
     * @return DownloadCartItem
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
     * @return DownloadCartItem
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
