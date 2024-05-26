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

namespace Pimcore\Bundle\PortalEngineBundle\Event\Search;

use ONGR\ElasticsearchDSL\Query\Compound\BoolQuery;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Can be used to modify the elasticsearch full text search query depending on your use case.
 */
class SearchQueryEvent extends Event
{
    /** @var BoolQuery */
    protected $searchQuery;
    /** @var int */
    protected $dataPoolConfigId;

    /**
     * SearchQueryEvent constructor.
     *
     * @param BoolQuery $searchQuery
     * @param int $dataPoolConfigId
     */
    public function __construct(BoolQuery $searchQuery, int $dataPoolConfigId)
    {
        $this->searchQuery = $searchQuery;
        $this->dataPoolConfigId = $dataPoolConfigId;
    }

    /**
     * @return BoolQuery
     */
    public function getSearchQuery(): BoolQuery
    {
        return $this->searchQuery;
    }

    /**
     * @return int
     */
    public function getDataPoolConfigId(): int
    {
        return $this->dataPoolConfigId;
    }

    /**
     * @param BoolQuery $searchQuery
     *
     * @return SearchQueryEvent
     */
    public function setSearchQuery(BoolQuery $searchQuery): self
    {
        $this->searchQuery = $searchQuery;

        return $this;
    }
}
