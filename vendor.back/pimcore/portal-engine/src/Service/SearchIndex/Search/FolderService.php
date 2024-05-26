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

use ONGR\ElasticsearchDSL\Aggregation\Bucketing\FilterAggregation;
use ONGR\ElasticsearchDSL\Aggregation\Bucketing\NestedAggregation;
use ONGR\ElasticsearchDSL\Aggregation\Bucketing\TermsAggregation;
use ONGR\ElasticsearchDSL\Query\Compound\BoolQuery;
use ONGR\ElasticsearchDSL\Query\Joining\NestedQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\TermQuery;
use Pimcore\Bundle\PortalEngineBundle\Enum\ElasticSearchFields;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolService;

/**
 * Class WorkspaceService
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Search
 */
class FolderService
{
    const PARAM_FOLDER = 'folder';
    const PARAM_PAGE = 'page';
    const PAGE_SIZE = 50;

    /**
     * @var DataPoolService
     */
    protected $dataPoolService;

    /**
     * @var DataPoolConfigService
     */
    protected $dataPoolConfigService;

    /**
     * FolderService constructor.
     *
     * @param DataPoolService $dataPoolService
     */
    public function __construct(DataPoolService $dataPoolService, DataPoolConfigService $dataPoolConfigService)
    {
        $this->dataPoolService = $dataPoolService;
        $this->dataPoolConfigService = $dataPoolConfigService;
    }

    /**
     * @return BoolQuery|null
     *
     * @throws \Exception
     */
    public function getElasticsearchFullPathQuery(array $params)
    {
        $folder = $this->getFolder($params);
        if ($folder !== '/') {
            $fullPathFilter = new BoolQuery();
            $fullPathFilter->add(new TermQuery(ElasticSearchFields::SYSTEM_FIELDS . '.' . ElasticSearchFields::SYSTEM_FIELDS_FULL_PATH, $folder), BoolQuery::FILTER);

            return $fullPathFilter;
        }

        return null;
    }

    /**
     * @param array $params
     *
     * @return NestedAggregation
     */
    public function getLevelAggregation(array $params)
    {
        $level = $this->getLevel($params);

        $levelAggregation = new NestedAggregation('children', 'system_fields.pathLevels');
        $levelFilter = new FilterAggregation('level_filter', new TermQuery('system_fields.pathLevels.level', $level));
        $nameAggregation = new TermsAggregation('children_names', 'system_fields.pathLevels.name');
        $nameAggregation->addParameter('size', 100000);
        $levelFilter->addAggregation($nameAggregation);
        $levelAggregation->addAggregation($levelFilter);

        return $levelAggregation;
    }

    public function extractSubFoldersFromSearchResult(array $params, array $searchResult): array
    {
        $subFolders = [];
        foreach ($searchResult['aggregations']['children']['level_filter']['children_names']['buckets'] as $bucket) {
            if (empty($bucket['key'])) {
                continue;
            }
            $subFolders[] = $bucket['key'];
        }
        uasort($subFolders, function ($a, $b) {
            return strcasecmp($a, $b);
        });

        $page = intval($params[self::PARAM_PAGE] ?? 1);
        if ($page < 1) {
            $page = 1;
        }
        $limit = self::PAGE_SIZE;
        $offset = $limit * ($page - 1);

        $resultFolders = array_slice($subFolders, $offset, $limit);

        return [$resultFolders, sizeof($subFolders) > $offset + $limit, sizeof($subFolders)];
    }

    /**
     * @param array $params
     * @param string $subFolder
     *
     * @return BoolQuery
     */
    public function getHasChildrenFilter(array $params, string $subFolder)
    {
        $level = $this->getLevel($params);
        $baseFolder = $this->getFolder($params);

        $boolQuery = new BoolQuery();
        $boolQuery->add(new TermQuery('system_fields.pathLevels.level', $level + 1), BoolQuery::FILTER);
        $levelFilter = new NestedQuery('system_fields.pathLevels', $boolQuery);

        $subFullPath = $baseFolder === '/' ? '/' . $subFolder : $baseFolder . '/' . $subFolder;

        $pathFilter = new TermQuery(ElasticSearchFields::SYSTEM_FIELDS . '.' . ElasticSearchFields::SYSTEM_FIELDS_PATH, $subFullPath);

        $boolQuery = new BoolQuery();
        $boolQuery->add($levelFilter, BoolQuery::FILTER);
        $boolQuery->add($pathFilter, BoolQuery::FILTER);

        return $boolQuery;
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public function getFolder(array $params)
    {
        if (empty($params[self::PARAM_FOLDER])) {
            $dataPool = $this->dataPoolService->getCurrentDataPool();
            $workspaceService = $dataPool->getSearchService()->getWorkspaceService();
            $dataPoolConfig = $this->dataPoolConfigService->getCurrentDataPoolConfig();

            return $workspaceService->getRootPathFromWorkspaces($dataPoolConfig->getWorkspaces());
        }

        return str_replace('//', '/', $params[self::PARAM_FOLDER]);
    }

    /**
     * @param array $params
     *
     * @return int
     */
    protected function getLevel(array $params)
    {
        $folder = $this->getFolder($params);

        return $folder === '/' ? 1 : count(explode('/', $folder));
    }
}
