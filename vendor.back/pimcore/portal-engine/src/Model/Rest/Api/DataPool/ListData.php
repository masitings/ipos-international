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
 * Class ListData
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Model\Rest\Api\DataObject
 */
class ListData
{
    /** @var int */
    protected $pages;
    /** @var int */
    protected $page;
    /** @var string */
    protected $currentOrderBy;
    /** @var array */
    protected $orderByOptions = [];
    /** @var array */
    protected $listViewAttributes = [];
    /** @var int */
    protected $total;
    /** @var int */
    protected $pageSize;
    /** @var ListDataEntry[] */
    protected $entries = [];
    /** @var array */
    protected $params = [];

    /**
     * @return int
     */
    public function getPages(): int
    {
        return $this->pages;
    }

    /**
     * @param int $pages
     *
     * @return ListData
     */
    public function setPages(int $pages): self
    {
        $this->pages = $pages;

        return $this;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @param int $page
     *
     * @return ListData
     */
    public function setPage(int $page): self
    {
        $this->page = $page;

        return $this;
    }

    /**
     * @return string
     */
    public function getCurrentOrderBy()
    {
        return $this->currentOrderBy;
    }

    /**
     * @param string $currentOrderBy
     *
     * @return ListData
     */
    public function setCurrentOrderBy($currentOrderBy): self
    {
        $this->currentOrderBy = $currentOrderBy;

        return $this;
    }

    /**
     * @return array
     */
    public function getOrderByOptions(): array
    {
        return $this->orderByOptions;
    }

    /**
     * @param array $orderByOptions
     *
     * @return ListData
     */
    public function setOrderByOptions(array $orderByOptions): self
    {
        $this->orderByOptions = $orderByOptions;

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
     * @return ListData
     */
    public function setListViewAttributes(array $listViewAttributes): self
    {
        $this->listViewAttributes = $listViewAttributes;

        return $this;
    }

    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @param int $total
     *
     * @return ListData
     */
    public function setTotal(int $total): self
    {
        $this->total = $total;

        return $this;
    }

    /**
     * @return int
     */
    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    /**
     * @param int $pageSize
     *
     * @return ListData
     */
    public function setPageSize(int $pageSize): self
    {
        $this->pageSize = $pageSize;

        return $this;
    }

    /**
     * @return ListDataEntry[]
     */
    public function getEntries(): array
    {
        return $this->entries;
    }

    /**
     * @param ListDataEntry[] $entries
     *
     * @return ListData
     */
    public function setEntries(array $entries): self
    {
        $this->entries = $entries;

        return $this;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param array $params
     *
     * @return $this
     */
    public function setParams(array $params)
    {
        $this->params = $params;

        return $this;
    }
}
