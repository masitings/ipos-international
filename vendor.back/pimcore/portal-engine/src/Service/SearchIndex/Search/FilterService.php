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
use ONGR\ElasticsearchDSL\Query\TermLevel\TermsQuery;
use Pimcore\Bundle\PortalEngineBundle\Enum\Document\Editables\DataPool\DataPoolConfig\FilterDefinition;
use Pimcore\Bundle\PortalEngineBundle\Enum\ElasticSearchFields;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\FilterDefinitionConfig;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;

/**
 * Class FilterService
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\DataObject\Search
 */
class FilterService
{
    /** @var DataPoolConfigService */
    protected $dataPoolConfigService;

    /**
     * FilterService constructor.
     *
     * @param DataPoolConfigService $dataPoolConfigService
     */
    public function __construct(DataPoolConfigService $dataPoolConfigService)
    {
        $this->dataPoolConfigService = $dataPoolConfigService;
    }

    /**
     * @param array $queryParams
     *
     * @return BoolQuery|null
     *
     * @throws \Exception
     */
    public function getElasticSearchFilterQuery($queryParams = [])
    {
        /** @var BoolQuery|null $filterQuery */
        $filterQuery = null;
        /** @var array $filterParams */
        $filterParams = [];

        //detect all filter params with postfix "Filter" and remove it
        foreach ($queryParams as $paramKey => $paramValue) {
            if (substr($paramKey, -strlen(FilterDefinition::FILTER_PARAM_POSTFIX)) === FilterDefinition::FILTER_PARAM_POSTFIX) {
                $paramKey = str_replace(FilterDefinition::FILTER_PARAM_POSTFIX, '', $paramKey);

                $filterParams[$paramKey] = $paramValue;
            }
        }

        foreach ($filterParams as $filterName => $filterValue) {

            /** @var string|null $filterPath */
            $filterPath = $this->getElasticSearchPathByName($filterName);

            if ($filterPath) {
                if (is_null($filterQuery)) {
                    $filterQuery = new BoolQuery();
                }

                /** @var TermQuery|BoolQuery $selectQuery */
                $selectQuery = null;

                //$filterValue is a array for multiselect filters
                if (is_array($filterValue)) {
                    $selectQuery = new BoolQuery();
                    foreach ($filterValue as $filterValueEntry) {
                        $selectQuery->add(new TermQuery($filterPath, $filterValueEntry), BoolQuery::SHOULD);
                    }
                } else {
                    $selectQuery = new TermQuery($filterPath, $filterValue);
                }

                $filterQuery->add($selectQuery, BoolQuery::FILTER);
            }
        }

        return $filterQuery;
    }

    /**
     * @param array $queryParams
     *
     * @return BoolQuery|null
     *
     * @throws \Exception
     */
    public function getElasticSearchIdFilterQuery($queryParams = [])
    {
        if (!empty($queryParams['ids']) && is_array($queryParams['ids'])) {
            $filterQuery = new BoolQuery();
            $idFilterQuery = new TermsQuery(ElasticSearchFields::SYSTEM_FIELDS . '.' . ElasticSearchFields::SYSTEM_FIELDS_ID, $queryParams['ids']);
            $filterQuery->add($idFilterQuery, BoolQuery::FILTER);

            return $filterQuery;
        }
    }

    /**
     * @param string $name
     *
     * @return string|null
     */
    public function getElasticSearchPathByName($name)
    {
        /** @var FilterDefinitionConfig $filterDefinitionConfig */
        foreach ($this->dataPoolConfigService->getCurrentDataPoolConfig()->getFilterDefinitions() as $filterDefinitionConfig) {
            if ($name == $filterDefinitionConfig->getFilterParamName()) {
                return $filterDefinitionConfig->getFilterAttribute();
            }
        }

        return null;
    }
}
