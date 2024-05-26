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

use ONGR\ElasticsearchDSL\Aggregation\Bucketing\TermsAggregation;
use ONGR\ElasticsearchDSL\Query\Compound\BoolQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\TermQuery;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Db;
use Pimcore\Model\Element\Tag;

/**
 * Class WorkspaceService
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Search
 */
class TagService
{
    const PARAM_CHECKED_IDS = 'checkedIds';
    const PARAM_TAGS = 'tags';

    /**
     * @var Db\Connection
     */
    protected $connection;

    /**
     * @var DataPoolConfigService
     */
    protected $dataPoolConfigService;

    private $tagsLookup = [];

    /**
     * TagService constructor.
     *
     * @param Db\Connection $connection
     * @param DataPoolConfigService $dataPoolConfigService
     */
    public function __construct(Db\Connection $connection, DataPoolConfigService $dataPoolConfigService)
    {
        $this->connection = $connection;
        $this->dataPoolConfigService = $dataPoolConfigService;
    }

    /**
     * @return BoolQuery|null
     *
     * @throws \Exception
     */
    public function getElasticSearchTagsQuery(array $params)
    {
        $tags = isset($params[self::PARAM_TAGS]) ? (array)$params[self::PARAM_TAGS] : [];
        if (sizeof($tags)) {
            $boolQuery = new BoolQuery();
            foreach ($tags as $tag) {
                $termFilter = new TermQuery('system_fields.tags', $tag);
                $boolQuery->add($termFilter, BoolQuery::FILTER);
            }

            return $boolQuery;
        }
    }

    public function getTagIdsAggregation(int $size = 100000): TermsAggregation
    {
        $aggregation = new TermsAggregation('tag_ids', 'system_fields.tags');
        $aggregation->addParameter('size', $size);

        return $aggregation;
    }

    public function extractTagIdsFromAggregation(array $searchResults): array
    {
        $result = [];
        foreach ($searchResults['aggregations']['tag_ids']['buckets'] as $bucket) {
            $result[] = $bucket['key'];
        }

        return $result;
    }

    public function getTagSelectStore()
    {
        $rows = $this->connection->fetchAllAssociative('select idPath, id, name from tags order by concat(idPath,id)');
        $tagMap = [];
        $result = [];
        $result[] = [null, '-'];
        foreach ($rows as $row) {
            $tagMap[$row['id']] = $row['name'];

            $label = [];
            $idPath = $this->idPathToArray($row['idPath']);
            foreach ($idPath as $id) {
                $label[] = $tagMap[$id];
            }
            $label[] = $row['name'];
            $result[] = [$row['id'], implode(' > ', $label)];
        }

        usort($result, function ($a, $b) {
            return strcmp($a[1], $b[1]);
        });

        return $result;
    }

    /**
     * @param int $parentId
     *
     * @return Tag[]
     */
    public function getAllTagsByParent(?int $parentId = 0)
    {
        $parentId = $parentId ?: 0;

        if ($parentId === 0) {
            $path = '/';
        } else {
            $tag = $this->getTagById($parentId);
            $path = $tag->getFullIdPath();
        }

        $tags = new Tag\Listing();
        $tags->addConditionParam('idPath like :path', ['path' => "{$path}%"]);

        return $tags->load();
    }

    public function getTagsSelectOptions(?int $parentId = 0)
    {
        $options = array_map(
            function (Tag $tag) {
                return ['value' => $tag->getId(), 'label' => $this->getTagFullPath($tag)];
            },
            $this->getAllTagsByParent($parentId)
        );
        usort($options, function ($a, $b) {
            return strcmp($a['label'], $b['label']);
        });

        return $options;
    }

    public function getTagTree(array $params = [], array $tagIds = [], bool $includeAll = false)
    {
        if (empty($tagIds) && !$includeAll) {
            return [];
        }

        $checkedIds = isset($params[self::PARAM_CHECKED_IDS]) ? (array)$params[self::PARAM_CHECKED_IDS] : [];
        $rootTag = $this->getRootTag();
        $rootIds = [];
        if ($rootTag) {
            $rootIds = $this->idPathToArray($rootTag->getIdPath());
            $rootIds[] = $rootTag->getId();
        }

        $in = '';
        if (sizeof($tagIds)) {
            $in = 'id in (' . implode(',', $tagIds) . ') ';
        }

        $and = '';
        if ($rootTag) {
            $and = ' and idPath like "' . $rootTag->getIdPath() . $rootTag->getId() . '%"';
        }

        $condition = '';
        if ($in || $and) {
            $condition = ' where ' . $in . $and;
        }

        $idPaths = $this->connection->fetchCol('select concat(idPath,id) from tags ' . $condition);

        $flatTree = [];
        foreach ($idPaths as $idPath) {
            $ids = $this->idPathToArray($idPath);

            while ($id = array_shift($ids)) {
                if (sizeof($rootIds) and in_array($id, $rootIds)) {
                    continue;
                }
                if (!$tag = $this->getTagById($id)) {
                    continue;
                }
                $id = intval($id);
                $parentId = $tag->getParentId();
                if ($rootTag && $rootTag->getId() == $parentId) {
                    $parentId = 0;
                }
                $treeItem = [
                    'id' => $id,
                    'name' => $tag->getName(),
                    'parentId' => $parentId,
                    'checked' => in_array($id, $checkedIds),
                ];
                $flatTree[$id . '_' . $parentId] = $treeItem;
            }
        }
        $flatTree = array_values($flatTree);
        $tree = $this->buildTree($flatTree);

        $result = ['items' => $tree];
        $this->cleanUpTagTree($result);

        return $result;
    }

    protected function getRootTag(): ?Tag
    {
        if ($rootTag = $this->dataPoolConfigService->getCurrentDataPoolConfig()->getRootTag()) {
            return $this->getTagById($rootTag);
        }

        return null;
    }

    protected function cleanUpTagTree(array &$tree)
    {
        if (isset($tree['items'])) {
            $tree['items'] = array_values($tree['items']);
            foreach ($tree['items'] as &$item) {
                unset($item['parentId']);
                $this->cleanUpTagTree($item);

                if (isset($item['items'])) {
                    $item['expanded'] = $this->hasCheckedNodes($item['items']);
                }
            }
        }
    }

    protected function hasCheckedNodes($nodesArray)
    {
        $it = new \RecursiveIteratorIterator(
            new \RecursiveArrayIterator($nodesArray),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($it as $key => $value) {
            if ($key === 'checked' && $value === true) {
                return true;
            }
        }

        return false;
    }

    protected function buildTree(array &$elements, $parentId = 0)
    {
        $branch = [];

        foreach ($elements as &$element) {
            if ($element['parentId'] == $parentId) {
                $children = $this->buildTree($elements, $element['id']);
                if ($children) {
                    $element['items'] = $children;
                }
                $branch[$element['id']] = $element;
                unset($element);
            }
        }

        return $branch;
    }

    public function getTagFullPath(Tag $tag, int $rootTagId = null): string
    {
        $idPath = explode('/', $tag->getFullIdPath());
        $idPath = array_values(array_filter($idPath));

        $pathParts = array_map(function ($id) {
            $tag = $this->getTagById($id);

            return $tag->getName() ?? '-';
        }, $idPath);

        return implode(' > ', $pathParts);
    }

    /**
     * @param int $id
     *
     * @return Tag|null
     */
    protected function getTagById(int $id)
    {
        if (!isset($this->tagsLookup[$id])) {
            $this->tagsLookup[$id] = Tag::getById($id);
        }

        return $this->tagsLookup[$id];
    }

    protected function idPathToArray(string $idPath): array
    {
        return array_filter(explode('/', $idPath));
    }
}
