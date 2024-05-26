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
 * Class SearchItem
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Model\Rest\Api
 */
class SearchItem
{
    /** @var string */
    protected $label;

    /** @var int */
    protected $id;

    /** @var string */
    protected $detailLink;

    /** @var string */
    protected $thumbnail;

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     *
     * @return SearchItem
     */
    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
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
     * @return SearchItem
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getDetailLink(): string
    {
        return $this->detailLink;
    }

    /**
     * @param string $detailLink
     *
     * @return SearchItem
     */
    public function setDetailLink(string $detailLink): self
    {
        $this->detailLink = $detailLink;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    /**
     * @param string|null $thumbnail
     *
     * @return SearchItem
     */
    public function setThumbnail(string $thumbnail = null): self
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }
}
