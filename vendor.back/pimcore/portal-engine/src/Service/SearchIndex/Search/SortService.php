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

use ONGR\ElasticsearchDSL\Search;
use ONGR\ElasticsearchDSL\Sort\FieldSort;
use ONGR\ElasticsearchDSL\Sort\NestedSort;
use Pimcore\Bundle\PortalEngineBundle\Enum\ElasticSearchFields;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\SortOptionConfig;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Localization\LocaleServiceInterface;

/**
 * Class SortService
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\DataObject\Search
 */
class SortService
{
    /** @var DataPoolConfigService */
    protected $dataPoolConfigService;

    /**
     * @var LocaleServiceInterface
     */
    protected $localeService;

    /**
     * SortService constructor.
     *
     * @param DataPoolConfigService $dataPoolConfigService
     * @param LocaleServiceInterface $localeService
     */
    public function __construct(DataPoolConfigService $dataPoolConfigService, LocaleServiceInterface $localeService)
    {
        $this->dataPoolConfigService = $dataPoolConfigService;
        $this->localeService = $localeService;
    }

    /**
     * @param array $queryParams
     * @param bool $reverse
     *
     * @return FieldSort|NestedSort|null
     *
     * @throws \Exception
     */
    public function getElasticSearchFieldSort($queryParams = [], bool $reverse = false)
    {
        /** @var FieldSort|null $elasticSearchSort */
        $elasticSearchSort = null;

        $orderBy = $this->getElasticSearchOrderBy($queryParams);
        $order = $this->getElasticSearchOrder($queryParams);

        if ($orderBy && $this->isOrderValid($order)) {
            if ($reverse) {
                $order = $this->reverseOrder($order);
            }

            $elasticSearchSort = $this->createElasticSearchSort($orderBy, $order);
        }

        return $elasticSearchSort;
    }

    /**
     * @return FieldSort
     */
    protected function createElasticSearchSort(string $orderBy, string $order)
    {
        if ($orderBy === ElasticSearchFields::SYSTEM_FIELDS.'.'.ElasticSearchFields::SYSTEM_FIELDS_NAME) {
            $orderBy = ElasticSearchFields::SYSTEM_FIELDS.'.'.ElasticSearchFields::SYSTEM_FIELDS_NAME.'.en';
        }

        return new FieldSort($orderBy, $order);
    }

    /**
     * @param Search $search
     *
     * @return array
     */
    public function extractSortKeys(Search $search)
    {
        $s = $search->toArray();

        $keys = [];
        $this->extractKeyFromSortArray($keys, $s['sort']);

        return $keys;
    }

    /**
     * @param array $keys
     * @param array $sort
     */
    protected function extractKeyFromSortArray(array &$keys, array $sort)
    {
        if (empty($sort)) {
            return;
        }

        foreach ($sort as $key => $value) {
            if (is_array($value)) {
                if (is_string($key)) {
                    $keys[] = $key;
                }

                $this->extractKeyFromSortArray($keys, $value);
            }
        }
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public function getElasticSearchOrderBy($params = [])
    {
        list($paramOrderBy) = $this->extractOrderFromParams($params);

        return $paramOrderBy ?: ElasticSearchFields::SYSTEM_FIELDS . '.' . ElasticSearchFields::SYSTEM_FIELDS_NAME;
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public function getElasticSearchOrder($params = [])
    {
        list($paramOrderBy, $paramOrder) = $this->extractOrderFromParams($params);

        return $paramOrder ?: ElasticSearchFields::ORDER_BY_ASC;
    }

    /**
     * @param $order
     *
     * @return string|null
     */
    public function reverseOrder($order)
    {
        if (!$this->isOrderValid($order)) {
            return null;
        }

        $reverseMapper = [
            ElasticSearchFields::ORDER_BY_ASC => ElasticSearchFields::ORDER_BY_DESC,
            ElasticSearchFields::ORDER_BY_DESC => ElasticSearchFields::ORDER_BY_ASC
        ];

        return $reverseMapper[$order];
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function extractOrderFromParams(array $params)
    {
        $orderBy = null;
        $order = null;

        if (array_key_exists('currentOrderBy', $params)) {
            /** @var string $paramName */
            $paramName = $params['currentOrderBy'];
            /** @var SortOptionConfig $sortOption */
            $sortOption = $this->getSortOptionByParamName($paramName);

            if ($sortOption) {
                $orderBy = $sortOption->getField();
                $order = $sortOption->getDirection();
            }
        }

        return [$orderBy, $order];
    }

    /**
     * @param bool  $reverse
     *
     * @return FieldSort
     */
    public function getElasticSearchTieBreakerFieldSort(bool $reverse = false)
    {
        $orderBy = implode('.', [ElasticSearchFields::SYSTEM_FIELDS, ElasticSearchFields::SYSTEM_FIELDS_ID]);
        $order = $reverse ? ElasticSearchFields::ORDER_BY_DESC : ElasticSearchFields::ORDER_BY_ASC;

        return new FieldSort($orderBy, $order);
    }

    /**
     * @param $order
     *
     * @return bool
     */
    private function isOrderValid($order)
    {
        return in_array($order, [
            ElasticSearchFields::ORDER_BY_ASC,
            ElasticSearchFields::ORDER_BY_DESC
        ]);
    }

    /**
     *
     * //TODO delete!
     *
     * @param string $name
     *
     * @return string|null
     */
    public function getElasticSearchPathByName($name)
    {
        /** @var SortOptionConfig $sortOption */
        foreach ($this->dataPoolConfigService->getCurrentDataPoolConfig()->getSortOptions() as $sortOption) {
            if ($name == $sortOption->getParamName()) {
                return $sortOption->getField();
            }
        }

        return null;
    }

    /**
     * @param string $paramName
     *
     * @return SortOptionConfig|null
     */
    public function getSortOptionByParamName($paramName)
    {
        /** @var SortOptionConfig $sortOption */
        foreach ($this->dataPoolConfigService->getCurrentDataPoolConfig()->getSortOptions() as $sortOption) {
            if ($paramName == $sortOption->getParamName()) {
                return $sortOption;
            }
        }

        return null;
    }
}
