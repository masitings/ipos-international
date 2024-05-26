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

namespace Pimcore\Bundle\PortalEngineBundle\Model\Rest\Api;

/**
 * Class SearchGroup
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Model\Rest\Api
 */
class SearchGroup
{
    /** @var int */
    protected $dataPoolConfigId;
    /** @var string */
    protected $name;
    /** @var string|null */
    protected $icon;
    /** @var string */
    protected $url;
    /** @var string */
    protected $type;
    /** @var int */
    protected $totalItemCount;
    /** @var SearchItem[] */
    protected $items = [];

    /**
     * @return int
     */
    public function getDataPoolConfigId(): int
    {
        return $this->dataPoolConfigId;
    }

    /**
     * @param int $dataPoolConfigId
     *
     * @return SearchGroup
     */
    public function setDataPoolConfigId(int $dataPoolConfigId): self
    {
        $this->dataPoolConfigId = $dataPoolConfigId;

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
     * @return SearchGroup
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * @param string|null $icon
     *
     * @return SearchGroup
     */
    public function setIcon(?string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return SearchGroup
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;

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
     * @return SearchGroup
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return int
     */
    public function getTotalItemCount(): int
    {
        return $this->totalItemCount;
    }

    /**
     * @param int $totalItemCount
     *
     * @return SearchGroup
     */
    public function setTotalItemCount(int $totalItemCount): self
    {
        $this->totalItemCount = $totalItemCount;

        return $this;
    }

    /**
     * @return SearchItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param SearchItem[] $items
     *
     * @return SearchGroup
     */
    public function setItems(array $items): self
    {
        $this->items = $items;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasItems()
    {
        return !empty($this->items);
    }
}
