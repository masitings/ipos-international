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

namespace Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Search;

use ONGR\ElasticsearchDSL\Query\Compound\BoolQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\TermQuery;
use Pimcore\Bundle\PortalEngineBundle\Enum\ElasticSearchFields;

/**
 * Class PublishedService
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Search
 */
class PublishedService
{
    /**
     * @return BoolQuery
     */
    public function getElasticSearchPublishedQuery()
    {
        /** @var BoolQuery $publishedQuery */
        $publishedQuery = new BoolQuery();
        $publishedQuery->add(new TermQuery(ElasticSearchFields::SYSTEM_FIELDS . '.' . ElasticSearchFields::SYSTEM_FIELDS_PUBLISHED, true), BoolQuery::FILTER);

        return $publishedQuery;
    }
}
