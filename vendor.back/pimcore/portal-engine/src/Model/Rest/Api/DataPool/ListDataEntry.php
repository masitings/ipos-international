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

namespace Pimcore\Bundle\PortalEngineBundle\Model\Rest\Api\DataPool;

/**
 * Class ListDataEntry
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Model\Rest\Api\DataObject
 */
class ListDataEntry
{
    /** @var int */
    protected $id;
    /** @var string */
    protected $name;
    /** @var string|null */
    protected $thumbnail;
    /** @var string */
    protected $detailLink;
    /** @var string */
    protected $fullPath;
    /** @var array */
    protected $listViewAttributes = [];
    /** @var bool */
    protected $hasWorkflowWithPermissions = false;

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
     * @return ListDataEntry
     */
    public function setId(int $id): self
    {
        $this->id = $id;

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
     * @return ListDataEntry
     */
    public function setName(string $name): self
    {
        $this->name = $name;

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
     * @return ListDataEntry
     */
    public function setThumbnail(?string $thumbnail): self
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public function getDetailLink(array $params = []): string
    {
        $url = parse_url($this->detailLink);
        $queryParams = [];

        if (!empty($url['query'])) {
            parse_str($url['query'], $queryParams);
        }

        $queryParams = array_replace($queryParams, $params);
        $query = null;

        if (array_key_exists('searchResult', $queryParams)) {
            unset($queryParams['searchResult']);
        }

        if (!empty($queryParams)) {
            $query = '?' . http_build_query($queryParams);
        }

        return $this->detailLink . $query;
    }

    /**
     * @param string $detailLink
     *
     * @return ListDataEntry
     */
    public function setDetailLink(string $detailLink): self
    {
        $this->detailLink = $detailLink;

        return $this;
    }

    /**
     * @return string
     */
    public function getFullPath(): string
    {
        return $this->fullPath;
    }

    /**
     * @param string $fullPath
     *
     * @return ListDataEntry
     */
    public function setFullPath(string $fullPath): self
    {
        $this->fullPath = $fullPath;

        return $this;
    }

    /**
     * @return array
     */
    public function getListViewAttributes(): array
    {
        return $this->listViewAttributes;
    }

    /**
     * @param array $listViewAttributes
     *
     * @return ListDataEntry
     */
    public function setListViewAttributes(array $listViewAttributes): self
    {
        $this->listViewAttributes = $listViewAttributes;

        return $this;
    }

    /**
     * @return bool
     */
    public function getHasWorkflowWithPermissions(): bool
    {
        return $this->hasWorkflowWithPermissions;
    }

    /**
     * @param bool $hasWorkflowWithPermissions
     */
    public function setHasWorkflowWithPermissions(bool $hasWorkflowWithPermissions): self
    {
        $this->hasWorkflowWithPermissions = $hasWorkflowWithPermissions;

        return $this;
    }
}
