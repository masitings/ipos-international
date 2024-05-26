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

namespace Pimcore\Bundle\PortalEngineBundle\Model\Rest;

use Knp\Component\Pager\Pagination\PaginationInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\BasicJsonModel;

class BasicListJsonModel extends BasicJsonModel
{
    public function __construct(array $params = [])
    {
        parent::__construct(array_merge([
            'pages' => null,
            'page' => null,
            'total' => null,
            'pageSize' => null,
            'entries' => [],
            'url' => null
        ], $params));
    }

    /**
     * @param int|null $pages
     *
     * @return $this
     */
    public function setPages(?int $pages)
    {
        $this->set('pages', $pages);

        return $this;
    }

    /**
     * @return int|null
     */
    public function getPages(): ?int
    {
        return $this->get('pages');
    }

    /**
     * @param int|null $page
     *
     * @return $this
     */
    public function setPage(?int $page)
    {
        $this->set('page', $page);

        return $this;
    }

    /**
     * @return int|null
     */
    public function getPage(): ?int
    {
        return $this->get('page');
    }

    /**
     * @param int|null $total
     *
     * @return $this
     */
    public function setTotal(?int $total)
    {
        $this->set('total', $total);

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTotal(): ?int
    {
        return $this->get('total');
    }

    /**
     * @param int|null $pageSize
     *
     * @return $this
     */
    public function setPageSize(?int $pageSize)
    {
        $this->set('pageSize', $pageSize);

        return $this;
    }

    /**
     * @return int|null
     */
    public function getPageSize(): ?int
    {
        return $this->get('pageSize');
    }

    /**
     * @param string|null $url
     *
     * @return $this
     */
    public function setUrl(?string $url)
    {
        $this->set('url', $url);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUrl()
    {
        return $this->get('url');
    }

    /**
     * @param BasicJsonModel[] $entries
     *
     * @return $this
     */
    public function setEntries(array $entries)
    {
        $this->set('entries', array_filter($entries));

        return $this;
    }

    /**
     * @return BasicJsonModel[]
     */
    public function getEntries(): array
    {
        return $this->get('entries', []);
    }

    /**
     * @param BasicJsonModel $entry
     *
     * @return $this
     */
    public function addEntry(BasicJsonModel $entry)
    {
        $this->setEntries(array_merge($this->getEntries(), [$entry]));

        return $this;
    }

    /**
     * @param PaginationInterface $pagination
     * @param callable|null $mapItem
     * @param string|null $url
     *
     * @return BasicListJsonModel
     */
    public static function createFromPagination(PaginationInterface $pagination, callable $mapItem = null, ?string $url = null)
    {
        $entries = (array)$pagination->getItems();

        if ($mapItem) {
            $entries = array_map($mapItem, $entries);
        }

        // remove potential null entries
        $entries = array_values(array_filter($entries));

        return (new static())
            ->setPage($pagination->getCurrentPageNumber())
            ->setPageSize($pagination->getItemNumberPerPage())
            ->setTotal($pagination->getTotalItemCount())
            ->setPages(ceil($pagination->getTotalItemCount() / $pagination->getItemNumberPerPage()))
            ->setEntries($entries)
            ->setUrl($url);
    }
}
