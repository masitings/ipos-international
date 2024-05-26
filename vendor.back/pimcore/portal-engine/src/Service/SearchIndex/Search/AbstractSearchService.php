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

use Elasticsearch\Client;
use ONGR\ElasticsearchDSL\Query\Compound\BoolQuery;
use ONGR\ElasticsearchDSL\Query\FullText\SimpleQueryStringQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\TermQuery;
use ONGR\ElasticsearchDSL\Search;
use Pimcore\Bundle\PortalEngineBundle\Enum\DataPool\FilterSort;
use Pimcore\Bundle\PortalEngineBundle\Enum\DataPool\TranslatorDomain;
use Pimcore\Bundle\PortalEngineBundle\Enum\Document\Editables\DataPool\DataPoolConfig\FilterDefinition;
use Pimcore\Bundle\PortalEngineBundle\Enum\ElasticSearchFields;
use Pimcore\Bundle\PortalEngineBundle\Enum\Permission;
use Pimcore\Bundle\PortalEngineBundle\Event\Search\SearchQueryEvent;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\AbstractDataPoolConfig;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\AssetConfig;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataPoolConfigInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Field\FilterableField;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Field\ListableField;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Field\SortableField;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Filter\Filter;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Filter\FilterData;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Filter\FilterDataOption;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\FilterDefinitionConfig;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\SortOptionConfig;
use Pimcore\Bundle\PortalEngineBundle\Model\Rest\Api\DataPool\ListData;
use Pimcore\Bundle\PortalEngineBundle\Model\Rest\Api\DataPool\ListDataEntry;
use Pimcore\Bundle\PortalEngineBundle\Model\Rest\Api\SearchGroup;
use Pimcore\Bundle\PortalEngineBundle\Model\Rest\Api\SearchItem;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\ListableFieldFormatter;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\TranslatorService;
use Pimcore\Bundle\PortalEngineBundle\Service\PublicShare\PublicShareService;
use Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\ErrorHandler;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\DataPool\FieldDefinitionAdapter\FieldDefinitionAdapterInterface;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\ElasticSearchConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\PermissionService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\SecurityService;
use Pimcore\Localization\IntlFormatter;
use Pimcore\Localization\LocaleServiceInterface;
use Pimcore\Translation\Translator;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class AbstractSearchService
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Search
 */
abstract class AbstractSearchService implements SearchServiceInterface
{
    /** @var Client */
    protected $esClient;

    /** @var LoggerInterface */
    protected $logger;
    /** @var DataPoolConfigService */
    protected $dataPoolConfigService;
    /** @var EventDispatcherInterface */
    protected $eventDispatcher;
    /** @var ElasticSearchConfigService */
    protected $elasticSearchConfigService;
    /** @var UrlGeneratorInterface */
    protected $urlGenerator;
    /** @var SortService */
    protected $sortService;
    /** @var FilterService */
    protected $filterService;
    /** @var WorkspaceServiceInterface */
    protected $workspaceService;
    /** @var PublishedService */
    protected $publishedService;
    /** @var FolderService */
    protected $folderService;
    /** @var TagService */
    protected $tagService;
    /** @var PreConditionService */
    protected $preConditionService;
    /** @var TranslatorService */
    protected $translatorService;
    /** @var Translator */
    protected $translator;
    /** @var ErrorHandler */
    protected $errorHandler;
    /** @var LocaleServiceInterface */
    protected $localeService;
    /** @var AuthorizationCheckerInterface */
    protected $authorizationChecker;
    /** @var SecurityService */
    protected $securityService;
    /** @var PermissionService */
    protected $permissionService;
    /** @var PublicShareService */
    protected $publicShareService;
    /** @var IntlFormatter */
    protected $intlFormatter;
    /** @var ListableFieldFormatter */
    protected $listableFieldFormatter;

    /** @var ListableField[] */
    protected $listableFieldsByGridConfiguration = [];
    /** @var array */
    protected $tieBreakerCache = [];

    /**
     * AbstractSearchService constructor.
     *
     * @param LoggerInterface $logger
     * @param DataPoolConfigService $dataPoolConfigService
     * @param EventDispatcherInterface $eventDispatcher
     * @param ElasticSearchConfigService $elasticSearchConfigService
     * @param UrlGeneratorInterface $urlGenerator
     * @param SortService $sortService
     * @param FilterService $filterService
     * @param WorkspaceServiceInterface $workspaceService
     * @param PublishedService $publishedService
     * @param FolderService $folderService
     * @param TagService $tagService
     * @param PreConditionService $preConditionService
     * @param TranslatorService $translatorService
     * @param Translator $translator
     * @param ErrorHandler $errorHandler
     * @param LocaleServiceInterface $localeService
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param SecurityService $securityService
     * @param PermissionService $permissionService
     * @param PublicShareService $publicShareService
     * @param IntlFormatter $intlFormatter
     * @param ListableFieldFormatter $listableFieldFormatter
     */
    public function __construct(
        LoggerInterface $logger,
        DataPoolConfigService $dataPoolConfigService,
        EventDispatcherInterface $eventDispatcher,
        ElasticSearchConfigService $elasticSearchConfigService,
        UrlGeneratorInterface $urlGenerator,
        SortService $sortService,
        FilterService $filterService,
        WorkspaceServiceInterface $workspaceService,
        PublishedService $publishedService,
        FolderService $folderService,
        TagService $tagService,
        PreConditionService $preConditionService,
        TranslatorService $translatorService,
        Translator $translator,
        ErrorHandler $errorHandler,
        LocaleServiceInterface $localeService,
        AuthorizationCheckerInterface $authorizationChecker,
        SecurityService $securityService,
        PermissionService $permissionService,
        PublicShareService $publicShareService,
        ListableFieldFormatter $listableFieldFormatter
    ) {
        $this->logger = $logger;
        $this->dataPoolConfigService = $dataPoolConfigService;
        $this->eventDispatcher = $eventDispatcher;
        $this->elasticSearchConfigService = $elasticSearchConfigService;
        $this->urlGenerator = $urlGenerator;
        $this->sortService = $sortService;
        $this->filterService = $filterService;
        $this->workspaceService = $workspaceService;
        $this->publishedService = $publishedService;
        $this->folderService = $folderService;
        $this->tagService = $tagService;
        $this->preConditionService = $preConditionService;
        $this->translatorService = $translatorService;
        $this->translator = $translator;
        $this->errorHandler = $errorHandler;
        $this->localeService = $localeService;
        $this->authorizationChecker = $authorizationChecker;
        $this->securityService = $securityService;
        $this->permissionService = $permissionService;
        $this->publicShareService = $publicShareService;
        $this->listableFieldFormatter = $listableFieldFormatter;
    }

    /**
     * @param Client $esClient
     * @required
     */
    public function setEsClient(Client $esClient)
    {
        $this->esClient = $esClient;
    }

    /**
     * @return Client
     */
    public function getEsClient(): Client
    {
        return $this->esClient;
    }

    /**
     * @param array $params
     *
     * @return Search
     *
     * @throws \Exception
     */
    public function getSearchByParams(array $params = [])
    {
        /** @var Search $esSearch */
        $esSearch = new Search();

        if ($filterQuery = $this->filterService->getElasticSearchFilterQuery($params)) {
            $esSearch->addQuery($filterQuery);
        }

        if ($idQuery = $this->filterService->getElasticSearchIdFilterQuery($params)) {
            $esSearch->addQuery($idQuery);
        }

        if ($tagsQuery = $this->tagService->getElasticSearchTagsQuery($params)) {
            $esSearch->addQuery($tagsQuery);
        }

        if ($publicShareQuery = $this->getElasticSearchPublicShareQuery($params)) {
            $esSearch->addQuery($publicShareQuery);
        }

        if ($collectionQuery = $this->getElasticSearchCollectionQuery($params)) {
            $esSearch->addQuery($collectionQuery);
        }

        if ($uploadFolderQuery = $this->getElasticSearchUploadFolderQuery($params)) {
            $esSearch->addQuery($uploadFolderQuery);
        }

        if (is_null($uploadFolderQuery) && $viewOwnedAssetOnlyQuery = $this->getElasticSearchViewOwnedAssetOnlyQuery($params)) {
            $esSearch->addQuery($viewOwnedAssetOnlyQuery);
        }

        if (is_null($uploadFolderQuery) && $workspaceQuery = $this->workspaceService->getElasticSearchWorkspaceQuery()) {
            $esSearch->addQuery($workspaceQuery);
        }

        if (is_null($uploadFolderQuery) && $fullPathFilter = $this->folderService->getElasticSearchFullPathQuery($params)) {
            $esSearch->addQuery($fullPathFilter);
        }

        if ($fullTextSearchQuery = $this->getElasticSearchFullTextSearchQuery($params)) {
            $esSearch->addQuery($fullTextSearchQuery);
        }

        $this->preConditionService->applyElasticSearchPreConditions($esSearch);

        return $esSearch;
    }

    /**
     * @param string[] $searchTerms
     * @param int $size
     *
     * @return SearchGroup
     *
     * @throws \Exception
     */
    public function getSearchGroupForTerms(array $searchTerms, int $size = 5)
    {
        $search = $this->getSearchByParams(['searchTerms' => $searchTerms]);
        $search->setSize($size);

        /** @var array $searchResult */
        $searchResult = $this->search([
            'index' => $this->getESIndexName(),
            'body' => $search->toArray()
        ]);

        /** @var DataPoolConfigInterface|AbstractDataPoolConfig $currentDataPoolConfig */
        $currentDataPoolConfig = $this->dataPoolConfigService->getCurrentDataPoolConfig();

        return (new SearchGroup())
            ->setDataPoolConfigId($currentDataPoolConfig->getLanguageVariantDataPoolId())
            ->setName($currentDataPoolConfig->getDataPoolName())
            ->setIcon($currentDataPoolConfig->getIcon())
            ->setUrl(
                $this->urlGenerator->generate(
                    'pimcore_portalengine_search',
                    ['q' => $searchTerms, 'activeDataPoolId' => $currentDataPoolConfig->getLanguageVariantDataPoolId()]
                )
            )
            ->setTotalItemCount($searchResult['hits']['total'] ?? 0)
            ->setItems($this->hydrateSearchItems($searchResult));
    }

    /**
     * @return string
     */
    abstract public function getESIndexName();

    /**
     * @return SortableField[]
     *
     * @throws \Exception
     */
    abstract public function getSortableFields();

    /**
     * @param \Pimcore\Model\Element\ElementInterface|mixed $item
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function isItemInDataPool($item): bool
    {
        $esSearch = $this->getSearchByParams();
        $idFilter = new TermQuery('system_fields.id', $item->getId());
        $boolQuery = new BoolQuery();
        $boolQuery->add($idFilter, BoolQuery::FILTER);
        $esSearch->addQuery($boolQuery);
        $esSearch->setSize(0);
        $searchResult = $this->search([
            'index' => $this->getESIndexName(),
            'body' => $esSearch->toArray()
        ]);

        return ($searchResult['hits']['total'] ?? 0) > 0;
    }

    /**
     * @param string $permission
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function hasItemsWithPermission(string $permission, array $params = []): bool
    {
        $esSearch = $this->getSearchByParams($params);
        $esSearch->addQuery($this->workspaceService->getElasticSearchWorkspaceQuery($permission));
        $esSearch->setSize(0);
        $searchResult = $this->search([
            'index' => $this->getESIndexName(),
            'body' => $esSearch->toArray()
        ]);

        return ($searchResult['hits']['total'] ?? 0) > 0;
    }

    /**
     * @param array $params
     *
     * @return ListData
     *
     * @throws \Exception
     */
    public function getListDataByParams(array $params = [])
    {
        $esSearch = $this->getSearchByParams($params);
        $this->applySortingParamsToSearch($esSearch, $params);
        list($pageSize, $page) = $this->applyPagingParamsToSearch($esSearch, $params);

        /** @var ListData $listData */
        $listData = (new ListData())
            ->setParams($params)
            ->setCurrentOrderBy($params['currentOrderBy'] ?? null)
            ->setPage($page)
            ->setPageSize($pageSize)
            ->setOrderByOptions($this->getOrderByOptions())
            ->setPages(0)
            ->setTotal(0);

        if (!$this->errorHandler->hasError()) {

            /** @var array $searchResult */
            $searchResult = $this->search([
                'index' => $this->getESIndexName(),
                'body' => $esSearch->toArray()
            ]);

            if (is_array($searchResult) && array_key_exists('hits', $searchResult)) {
                $listDataEntries = $this->hydrateListDataEntries($searchResult);

                $total = $searchResult['hits']['total'] ?? 0;
                $listData
                    ->setPages(ceil($total / $pageSize))
                    ->setListViewAttributes($this->getListViewAttributes())
                    ->setTotal($total)
                    ->setEntries($listDataEntries);
            }
        }

        return $listData;
    }

    /**
     * @param $searchResult
     *
     * @return ListDataEntry[]
     *
     * @throws \Exception
     */
    protected function hydrateListDataEntries($searchResult)
    {
        /** @var ListDataEntry[] $listDataEntries */
        $listDataEntries = [];

        /** @var array $searchResultHit */
        foreach ($searchResult['hits']['hits'] ?? [] as $searchResultHit) {
            $listDataEntries[] = $this->hydrateListDataEntry($searchResultHit);
        }

        return $listDataEntries;
    }

    /**
     * @param $searchResultHit
     *
     * @return ListDataEntry
     *
     * @throws \Exception
     */
    protected function hydrateListDataEntry($searchResultHit)
    {
        return (new ListDataEntry())
            ->setId($searchResultHit['_source'][ElasticSearchFields::SYSTEM_FIELDS][ElasticSearchFields::SYSTEM_FIELDS_ID])
            ->setName($this->getNameBySearchResultHit($searchResultHit))
            ->setThumbnail($searchResultHit['_source'][ElasticSearchFields::SYSTEM_FIELDS][ElasticSearchFields::SYSTEM_FIELDS_THUMBNAIL])
            ->setFullPath($searchResultHit['_source'][ElasticSearchFields::SYSTEM_FIELDS][ElasticSearchFields::SYSTEM_FIELDS_FULL_PATH])
            ->setDetailLink($this->generateDetailLink($searchResultHit))
            ->setListViewAttributes($this->getListViewAttributesBySearchResultHit($searchResultHit))
            ->setHasWorkflowWithPermissions($searchResultHit['_source'][ElasticSearchFields::SYSTEM_FIELDS][ElasticSearchFields::SYSTEM_FIELDS_HAS_WORKFLOW_WITH_PERMISSIONS] ?? false);
    }

    abstract protected function generateDetailLink(array $searchResultHit): string;

    /**
     * @param Search $search
     * @param array $params
     * @param bool $reverse
     *
     * @throws \Exception
     */
    public function applySortingParamsToSearch(Search $search, array $params = [], bool $reverse = false)
    {
        // disable sorting in searchResult, only when no currentOrderBy is active
        if (!array_key_exists('searchResult', $params) || array_key_exists('currentOrderBy', $params)) {
            $fieldSort = $this->sortService->getElasticSearchFieldSort($params, $reverse);

            if ($fieldSort) {
                $search->addSort($fieldSort);
            }

            $search->addSort($this->sortService->getElasticSearchTieBreakerFieldSort($reverse));
        }
    }

    /**
     * @param array $searchResultHit
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getListViewAttributesBySearchResultHit($searchResultHit)
    {
        /** @var array $listViewAttributes */
        $listViewAttributes = [];

        if (array_key_exists('_source', $searchResultHit)) {
            /** @var ListableField[] $listableFields */
            $listableFields = $this->getListableFieldsByGridConfiguration();

            foreach ($listableFields as $path => $listableField) {
                $listViewAttributes[] = $this->extractListViewAttribute($searchResultHit, $path, $listableField);
            }
        }

        return $listViewAttributes;
    }

    protected function extractListViewAttribute(array $searchResultHit, string $path, ListableField $listableField): array
    {
        $listViewAttribute = $searchResultHit['_source'];

        foreach (explode('.', $path) as $pathKey) {

            // ignore raw in path. e.g. standard_fields.categories.name.raw is as standard_fields.categories.name in listableFields
            if ($pathKey === 'raw') {
                break;
            }
            //if field data is not set/empty
            if (!is_array($listViewAttribute)) {
                break;
            }
            // if there is a missing array key, ignore whole $listableField entry
            if (!array_key_exists($pathKey, $listViewAttribute)) {
                break;
            }
            $listViewAttribute = $listViewAttribute[$pathKey];
        }

        return [
            'key' => $listableField->getName(),
            'value' => $listableField->getType()
                ? $this->listableFieldFormatter->format($listViewAttribute, $listableField->getType())
                : $listViewAttribute,
        ];
    }

    /**
     * @return array
     *
     * @throws \Exception
     */
    public function getOrderByOptions()
    {
        /** @var array $orderByOptions */
        $orderByOptions = [];
        /** @var SortableField[] $sortableFieldsMapping */
        $sortableFieldsMapping = $this->getSortableFieldsMapping();

        /** @var SortOptionConfig $sortOption */
        foreach ($this->dataPoolConfigService->getCurrentDataPoolConfig()->getSortOptions() as $sortOption) {

            /** @var string|null $label */
            $label = array_key_exists($sortOption->getField(), $sortableFieldsMapping) ? $sortableFieldsMapping[$sortOption->getField()]->getName() : null;

            if ($label) {
                $orderByOptions[] = [
                    'value' => $sortOption->getParamName(),
                    'label' => $this->translatorService->translate($label . ' ' . $sortOption->getDirection(), TranslatorDomain::DOMAIN_SORT_LABEL)
                ];
            }
        }

        $orderByOptions = $this->sortFieldsByLabel($orderByOptions);

        return $orderByOptions;
    }

    /**
     * @param Search $search
     * @param array $params
     *
     * @return int[]
     */
    public function applyPagingParamsToSearch(Search $search, array $params = [])
    {
        /** @var int $pageSize */
        $pageSize = 0;
        if (isset($params['pageSize'])) {
            $pageSize = intval($params['pageSize']);
        }

        $pageSize = $pageSize < 1 ? $this->elasticSearchConfigService->getSearchSettings()['list_page_size'] : $pageSize;
        $pageSize = $pageSize > 1000 ? 1000 : $pageSize;

        /** @var int $page */
        $page = intval(($params['page'] ?? 1) ?: 1);

        //check if max possible page is exceeded, ES item limit is 10000
        if ($page > floor(10000 / $pageSize)) {
            $this->errorHandler->setErrorMessage($this->translator->trans('portal-engine.data-pool.error.max-page-exceeded'));
        }

        /** @var int $pageFrom */
        $pageFrom = ($page - 1) * $pageSize;

        $search
            ->setSize($pageSize)
            ->setFrom($pageFrom);

        return [$pageSize, $page, $pageFrom];
    }

    /**
     * @return array
     *
     * @throws \Exception
     */
    public function getSortableFieldsSelectStore()
    {
        /** @var array $result */
        $result = [];
        foreach ($this->getSortableFields() as $sortableField) {
            $result[] = [$sortableField->getPath(), $sortableField->getTitle()];
        }

        return $result;
    }

    /**
     * @return SortableField[]
     *
     * @throws \Exception
     */
    public function getSortableFieldsMapping(): array
    {
        $result = [];
        foreach ($this->getSortableFields() as $sortableField) {
            $result[$sortableField->getPath()] = $sortableField;
        }

        return $result;
    }

    /**
     * @param array $params
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getFoldersDataByParams(array $params): array
    {
        $search = $this->getSearchByParams($params);
        $search->setSize(0);

        $search->addAggregation(
            $this->folderService->getLevelAggregation($params)
        );
        $searchResult = $this->search(
            [
                'index' => $this->getESIndexName(),
                'body' => $search->toArray()
            ]
        );

        list($subFolders, $hasNextPage, $totalResults) = $this->folderService->extractSubFoldersFromSearchResult(
            $params,
            $searchResult
        );

        $items = [];

        foreach ($subFolders as $subFolder) {
            $search = $this->getSearchByParams($params);
            $search->setSize(0);

            $hasChildrenFilter = $this->folderService->getHasChildrenFilter($params, $subFolder);
            $search->addQuery($hasChildrenFilter);

            $searchResult = $this->search(
                [
                    'index' => $this->getESIndexName(),
                    'body' => $search->toArray()
                ]
            );
            $total = $searchResult['hits']['total'] ?? 0;
            $items[] = [
                'name' => $subFolder,
                'hasItems' => $total > 0,
                'permissions' => [
                    Permission::CREATE => $this->permissionService->isPermissionAllowed(
                        Permission::CREATE,
                        $this->securityService->getPortalUser(),
                        $this->dataPoolConfigService->getCurrentDataPoolConfig()->getId(),
                        str_replace('//', '/', $params['folder'] . '/' . $subFolder)
                    ),
                ]
            ];
        }

        return [
            'items' => $items,
            'hasNextPage' => $hasNextPage,
            'totalResults' => $totalResults,
            'pageSize' => FolderService::PAGE_SIZE
        ];
    }

    /**
     * @param array $params
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getTagsDataByParams(array $params): array
    {
        $tagIds = $this->getTagIds();

        return $this->tagService->getTagTree($params, $tagIds);
    }

    /**
     * @return bool
     *
     * @throws \Exception
     */
    public function hasTags(): bool
    {
        $tagIds = $this->getTagIds(1);

        return sizeof($tagIds) > 0;
    }

    /**
     * @param int $limit
     *
     * @return array
     *
     * @throws \Exception
     */
    protected function getTagIds(int $limit = 100000): array
    {
        $search = $this->getSearchByParams();
        $search->addAggregation($this->tagService->getTagIdsAggregation($limit));
        $search->setSize(0);
        $searchResult = $this->search(
            [
                'index' => $this->getESIndexName(),
                'body' => $search->toArray()
            ]
        );

        return $this->tagService->extractTagIdsFromAggregation($searchResult);
    }

    public function getWorkspaceService(): WorkspaceServiceInterface
    {
        return $this->workspaceService;
    }

    /**
     * @return array
     *
     * @throws \Exception
     */
    public function getListableFieldsSelectStore()
    {
        /** @var array $result */
        $result = [];
        foreach ($this->getListableFields() as $listableField) {
            $result[] = [$listableField->getPath(), $listableField->getTitle(), $listableField->getType()];
        }

        return $result;
    }

    /**
     * @return ListableField[]
     *
     * @throws \Exception
     */
    public function getListableFieldsMapping(): array
    {
        $result = [];
        foreach ($this->getListableFields() as $listableField) {
            $result[$listableField->getPath()] = $listableField;
        }

        return $result;
    }

    /**
     * @return FilterableField[]
     *
     * @throws \Exception
     */
    public function getFilterableFieldsMapping(): array
    {
        $result = [];
        foreach ($this->getFilterableFields() as $filterableField) {
            $result[$filterableField->getPath()] = $filterableField;
        }

        return $result;
    }

    /**
     * @return array
     *
     * @throws \Exception
     */
    public function getFilterableFieldsSelectStore()
    {
        /** @var array $result */
        $result = [];
        foreach ($this->getFilterableFields() as $filterableField) {
            $result[] = [$filterableField->getPath(), $filterableField->getTitle()];
        }

        return $result;
    }

    /**
     * @param $tieBreakerId
     * @param array $params
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getItemsBeforeAndAfterByParams($tieBreakerId, array $params = [])
    {
        return array_merge(
            array_reverse(
                $this->getItemsForTiebreaker($tieBreakerId, $params, ElasticSearchFields::TIEBREAKER_POSITION_BEFORE)
            ),
            [$this->hydrateListDataEntry($this->getTieBreakerById($tieBreakerId))],
            $this->getItemsForTiebreaker($tieBreakerId, $params, ElasticSearchFields::TIEBREAKER_POSITION_AFTER)
        );
    }

    /**
     * @param array $params
     *
     * @return array|Filter[]
     *
     * @throws \Exception
     */
    public function getFilterData(array $params = [])
    {
        /** @var Filter[] $filters */
        $filters = [];
        /** @var array $esAggregationParams */
        $esAggregationParams = [];
        /** @var FilterableField[] $filterableFieldsMapping */
        $filterableFieldsMapping = $this->getFilterableFieldsMapping();

        /** @var int $listMaxFilterOptions */
        $listMaxFilterOptions = $this->elasticSearchConfigService->getSearchSettings()['list_max_filter_options'];

        /** @var FilterDefinitionConfig $filterDefinitionConfig */
        foreach ($this->dataPoolConfigService->getCurrentDataPoolConfig()->getFilterDefinitions() as $filterDefinitionConfig) {

            /** @var string $filterPath e.g. standard_fields.manufacturer.name.raw */
            $filterPath = $filterDefinitionConfig->getFilterAttribute();
            /** @var string $filterName */
            $filterName = $filterDefinitionConfig->getFilterParamName();

            /** @var string|null $label */
            $filterLabel = array_key_exists(
                $filterPath,
                $filterableFieldsMapping
            ) ? $filterableFieldsMapping[$filterPath]->getName() : null;

            if ($filterLabel) {

                //get all available filters for frontend
                $filters[$filterPath] = (new Filter())
                    ->setType($filterDefinitionConfig->getFilterType())
                    ->setName($filterName . FilterDefinition::FILTER_PARAM_POSTFIX)
                    ->setLabel(
                        $this->translatorService->translate($filterLabel, TranslatorDomain::DOMAIN_FILTER_LABEL)
                    );

                //build a elastic-search aggregation to get all available filter-options
                $esAggregationParams['body']['aggs'][$filterPath]['terms'] = [
                    'field' => $filterPath,
                    'size' => $listMaxFilterOptions
                ];
            }
        }

        if (!empty($esAggregationParams)) {
            $search = $this->getSearchByParams($this->getFilterDataSearchParams($params));
            $search->setSize(0);
            $esSearchParams = ['index' => $this->getESIndexName(), 'body' => $search->toArray()];
            $esSearchParams = array_merge_recursive($esSearchParams, $esAggregationParams);

            /** @var array $searchResult */
            $searchResult = $this->search($esSearchParams);

            foreach ($searchResult['aggregations'] as $filterPath => $aggregation) {

                /** @var bool $filterDataOptionSort */
                $filterDataOptionSort = FilterSort::SORT_BY_LABEL;
                /** @var FilterDataOption[] $filterDataOptions */
                $filterDataOptions = [];
                /** @var string|null $label */
                $filterLabel = array_key_exists(
                    $filterPath,
                    $filterableFieldsMapping
                ) ? $filterableFieldsMapping[$filterPath]->getName() : null;

                if ($filterLabel) {
                    /** @var FieldDefinitionAdapterInterface $fieldDefinitionAdapter */
                    $fieldDefinitionAdapter = $filterableFieldsMapping[$filterPath]->getFieldDefinitionAdapter();

                    $filterDataOptionSort = $fieldDefinitionAdapter ?
                        $fieldDefinitionAdapter->getFilterDataOptionSort()
                        : FilterSort::SORT_BY_LABEL;

                    foreach ($aggregation['buckets'] as $bucket) {

                        /** @var string $filterDataOptionValue */
                        $filterDataOptionValue = (string)$bucket['key'];
                        $filterDataOptionLabel = $fieldDefinitionAdapter
                            ? $fieldDefinitionAdapter->formatFilterDataOptionLabel($filterLabel, $filterDataOptionValue)
                            : $filterDataOptionValue;

                        $filterDataOptions[] = (new FilterDataOption())
                            ->setValue($filterDataOptionValue)
                            ->setLabel($filterDataOptionLabel)
                            ->setCount($bucket['doc_count']);
                    }
                }

                switch ($filterDataOptionSort) {
                    case FilterSort::SORT_BY_LABEL:
                        $filterDataOptions = $this->sortFieldsByLabel($filterDataOptions);
                        break;
                    case FilterSort::SORT_BY_VALUE:
                        $filterDataOptions = $this->sortFieldsByValue($filterDataOptions);
                        break;
                }

                /** @var Filter $filter */
                $filter = $filters[$filterPath];
                if ($filter) {

                    /** @var null|string|array $currentFilter string for select, array for multiselect */
                    $currentFilter = null;
                    if (array_key_exists($filter->getName(), $params)) {
                        $currentFilter = $params[$filter->getName()];
                    }

                    /** @var FilterData $filterData */
                    $filterData = (new FilterData())
                        ->setCurrentValue($currentFilter)
                        ->setVisible(!empty($filterDataOptions))
                        ->setOptions($filterDataOptions);

                    $filter->setData($filterData);
                }
            }
        }

        return $filters;
    }

    protected function getFilterDataSearchParams(array $params): array
    {
        $searchParams = [];
        if (isset($params['q'])) {
            $searchParams['searchTerms'] = $params['q'];
        }
        if (isset($params['collectionId'])) {
            $searchParams['collectionId'] = $params['collectionId'];
        }
        if (isset($params['uploadFolder'])) {
            $searchParams['uploadFolder'] = $params['uploadFolder'];
        }

        return $searchParams;
    }

    /**
     * Sort fields ASC by title
     *
     * @param FilterableField[] $fields
     *
     * @return FilterableField[]
     */
    protected function sortFieldsByTitle($fields)
    {
        usort($fields, function ($a, $b) {
            /**
             * @var ListableField $a
             * @var ListableField $b
             */
            if ($this->isSystemFieldPath($a->getPath()) && !$this->isSystemFieldPath($b->getPath())) {
                return -1;
            }
            if ($this->isSystemFieldPath($b->getPath()) && !$this->isSystemFieldPath($a->getPath())) {
                return 1;
            }

            return strcmp(strtolower($a->getTitle()), strtolower($b->getTitle()));
        });

        return $fields;
    }

    protected function isSystemFieldPath(string $path)
    {
        $path = explode('.', $path);

        return $path[0] === ElasticSearchFields::SYSTEM_FIELDS;
    }

    /**
     * Sort fields ASC by label
     *
     * @param []|FilterDataOption[]|Filter[] $fields
     *
     * @return []|FilterDataOption[]|Filter[]
     */
    protected function sortFieldsByLabel($fields)
    {
        usort($fields, function ($a, $b) {
            if (is_array($a) && array_key_exists('label', $a)) {
                return strcmp(strtolower($a['label']), strtolower($b['label']));
            } elseif (method_exists($a, 'getLabel')) {
                return strcmp(strtolower($a->getLabel()), strtolower($b->getLabel()));
            } else {
                return 0;
            }
        });

        return $fields;
    }

    /**
     * Sort fields ASC by value
     *
     * @param []|FilterDataOption[]|Filter[] $fields
     *
     * @return []|FilterDataOption[]|Filter[]
     */
    protected function sortFieldsByValue($fields)
    {
        usort($fields, function ($a, $b) {
            if (is_array($a) && array_key_exists('value', $a)) {
                return strcmp(strtolower($a['value']), strtolower($b['value']));
            } elseif (method_exists($a, 'getValue')) {
                return strcmp(strtolower($a->getValue()), strtolower($b->getValue()));
            } else {
                return 0;
            }
        });

        return $fields;
    }

    /**
     * @return ListableField[]
     *
     * @throws \Exception
     */
    abstract public function getListableFields();

    /**
     * @return FilterableField[]|array
     *
     * @throws \Exception
     */
    abstract public function getFilterableFields();

    /**
     * @return ListableField[]
     *
     * @throws \Exception
     */
    protected function getListableFieldsByGridConfiguration()
    {
        if (empty($this->listableFieldsByGridConfiguration)) {

            /** @var string[] $gridConfigurationAttributes */
            $gridConfigurationAttributes = $this->dataPoolConfigService->getCurrentDataPoolConfig()->getGridConfigurationAttributes();

            if (!empty($gridConfigurationAttributes)) {

                /** @var ListableField[] $listableFieldsMapping */
                $listableFieldsMapping = $this->getListableFieldsMapping();

                foreach ($gridConfigurationAttributes as $gridConfigurationAttribute) {
                    if (array_key_exists($gridConfigurationAttribute, $listableFieldsMapping)) {
                        /** @var ListableField $listableField */
                        $listableField = $listableFieldsMapping[$gridConfigurationAttribute];

                        $this->listableFieldsByGridConfiguration[$listableField->getPath()] = $listableField;
                    }
                }
            }
        }

        return $this->listableFieldsByGridConfiguration;
    }

    /**
     * @return array
     *
     * @throws \Exception
     */
    protected function getListViewAttributes()
    {
        /** @var array $listViewAttributes */
        $listViewAttributes = [];

        foreach ($this->getListableFieldsByGridConfiguration() as $listableField) {
            $listViewAttributes[] = [
                'key' => $listableField->getName(),
                'label' => $this->translatorService->translate(
                    $listableField->getName(),
                    TranslatorDomain::DOMAIN_LIST_LABEL
                )
            ];
        }

        return $listViewAttributes;
    }

    /**
     * @param array $params
     *
     * @return BoolQuery|null
     */
    protected function getElasticSearchCollectionQuery(array $params = [])
    {
        /** @var BoolQuery|null $collectionQuery */
        $collectionQuery = null;
        if (array_key_exists('collectionId', $params)) {

            /** @var int $collectionId */
            $collectionId = $params['collectionId'];
            if (is_numeric($collectionId)) {
                $collectionQuery = new TermQuery(
                    ElasticSearchFields::SYSTEM_FIELDS . '.' . ElasticSearchFields::SYSTEM_FIELDS_COLLECTIONS, $collectionId
                );
            }
        }

        return $collectionQuery;
    }

    /**
     * @param array $params
     *
     * @return BoolQuery|null
     */
    protected function getElasticSearchPublicShareQuery(array $params = [])
    {
        /** @var BoolQuery|null $publicShareQuery */
        $publicShareQuery = null;
        if (array_key_exists('publicShareHash', $params) && $publicShare = $this->publicShareService->getByHash($params['publicShareHash'])) {
            $this->publicShareService->setCurrentPublicShare($publicShare);

            if ($publicShare->getCollectionId()) {
                $publicShareQuery = new TermQuery(
                    ElasticSearchFields::SYSTEM_FIELDS . '.' . ElasticSearchFields::SYSTEM_FIELDS_COLLECTIONS, $publicShare->getCollectionId()
                );
            } else {
                $publicShareQuery = new TermQuery(
                    ElasticSearchFields::SYSTEM_FIELDS . '.' . ElasticSearchFields::SYSTEM_FIELDS_PUBLIC_SHARES, $publicShare->getId()
                );
            }
        }

        return $publicShareQuery;
    }

    /**
     * Function is disabled when getElasticSearchUploadFolderQuery returns a query
     *
     * @param array $params
     *
     * @return BoolQuery|TermQuery|null
     */
    protected function getElasticSearchViewOwnedAssetOnlyQuery(array $params = [])
    {
        /** @var BoolQuery|TermQuery|null $uploadFolderQuery */
        $viewOwnedAssetOnlyQuery = null;
        /** @var string $folder */
        $folder = $this->folderService->getFolder($params);

        if ($this->authorizationChecker->isGranted(Permission::VIEW_OWNED_ASSET_ONLY, $folder)) {
            $viewOwnedAssetOnlyQuery = new TermQuery(ElasticSearchFields::SYSTEM_FIELDS . '.' . ElasticSearchFields::SYSTEM_FIELDS_USER_OWNER, $this->securityService->getPortalUser()->getPimcoreUser());
        }

        return $viewOwnedAssetOnlyQuery;
    }

    /**
     * @param array $params
     *
     * @return BoolQuery|null
     */
    protected function getElasticSearchUploadFolderQuery(array $params = [])
    {
        /** @var BoolQuery|null $uploadFolderQuery */
        $uploadFolderQuery = null;
        /** @var DataPoolConfigInterface $dataPoolConfig */
        $dataPoolConfig = $this->dataPoolConfigService->getCurrentDataPoolConfig();

        if (
            $dataPoolConfig instanceof AssetConfig
            && $dataPoolConfig->getUploadFolder()
            && array_key_exists('uploadFolder', $params)
            && 'true' === $params['uploadFolder']
        ) {
            $uploadFolderQuery = new BoolQuery();
            $uploadFolderQuery->add(new TermQuery(ElasticSearchFields::SYSTEM_FIELDS . '.' . ElasticSearchFields::SYSTEM_FIELDS_FULL_PATH, $dataPoolConfig->getUploadFolder()->getRealFullPath()), BoolQuery::FILTER);

            if (!$this->authorizationChecker->isGranted(Permission::DATA_POOL_ASSET_UPLOAD_FOLDER_REVIEWING)) {
                $uploadFolderQuery->add(new TermQuery(ElasticSearchFields::SYSTEM_FIELDS . '.' . ElasticSearchFields::SYSTEM_FIELDS_USER_OWNER, $this->securityService->getPortalUser()->getPimcoreUser()));
            }
        }

        return $uploadFolderQuery;
    }

    /**
     * @param $tieBreakerId
     * @param array $params
     * @param string $position
     * @param int $size
     *
     * @return ListDataEntry[]
     *
     * @throws \Exception
     */
    protected function getItemsForTiebreaker(
        $tieBreakerId,
        array $params = [],
        string $position = ElasticSearchFields::TIEBREAKER_POSITION_AFTER,
        int $size = 50
    ) {
        $search = $this->getSearchByParams($params);
        $search->setSize($size);
        $this->applySortingParamsToSearch(
            $search,
            $params,
            $position === ElasticSearchFields::TIEBREAKER_POSITION_BEFORE
        );

        $sortKeys = $this->sortService->extractSortKeys($search);
        $tieBreaker = $this->flattenElasticSearchResult($this->getTieBreakerById($tieBreakerId), $sortKeys);

        if (!$tieBreaker) {
            return [];
        }

        $searchArray = $search->toArray();

        $searchArray['search_after'] = array_map(
            function ($sortKey) use ($tieBreaker) {
                return $tieBreaker[$sortKey];
            },
            $sortKeys
        );

        return $this->hydrateListDataEntries(
            $this->search(
                [
                    'index' => $this->getESIndexName(),
                    'body' => $searchArray
                ]
            )
        );
    }

    /**
     * @param $id
     * @param array $includeFields
     *
     * @return array|null
     *
     * @throws \Exception
     */
    protected function getTieBreakerById($id)
    {
        if (array_key_exists($id, $this->tieBreakerCache)) {
            return $this->tieBreakerCache[$id];
        }

        $idField = ElasticSearchFields::SYSTEM_FIELDS . '.' . ElasticSearchFields::SYSTEM_FIELDS_ID;

        $searchResult = $this->search(
            [
                'index' => $this->getESIndexName(),
                'body' => [
                    'query' => [
                        'term' => [
                            $idField => $id
                        ]
                    ]
                ]
            ]
        );

        if (empty($searchResult['hits']['hits'])) {
            return null;
        }

        $tieBreaker = $searchResult['hits']['hits'][0];
        $this->tieBreakerCache[$id] = $tieBreaker;

        return $tieBreaker;
    }

    /**
     * @param array $hit
     * @param array $fields
     *
     * @return array
     */
    protected function flattenElasticSearchResult(array $hit, array $fields = [])
    {
        $result = [];

        foreach ($fields as $field) {
            $current = $hit['_source'];
            $parts = explode('.', $field);

            if (empty($parts)) {
                continue;
            }

            $valid = true;

            foreach ($parts as $part) {
                if (!is_array($current) || !array_key_exists($part, $current)) {
                    $valid = false;
                    break;
                }

                $current = $current[$part];
            }

            if ($valid) {
                $result[$field] = $current;
            }
        }

        return $result;
    }

    /**
     * @param $searchResult
     *
     * @return SearchItem[]
     *
     * @throws \Exception
     */
    protected function hydrateSearchItems($searchResult)
    {
        /** @var SearchItem[] $searchItems */
        $searchItems = [];

        /** @var array $searchResultHit */
        foreach ($searchResult['hits']['hits'] ?? [] as $searchResultHit) {
            $searchItems[] = (new SearchItem())
                ->setLabel(
                    $this->getNameBySearchResultHit($searchResultHit)
                )
                ->setId(
                    $searchResultHit['_source'][ElasticSearchFields::SYSTEM_FIELDS][ElasticSearchFields::SYSTEM_FIELDS_ID]
                )
                ->setThumbnail(
                    $searchResultHit['_source'][ElasticSearchFields::SYSTEM_FIELDS][ElasticSearchFields::SYSTEM_FIELDS_THUMBNAIL]
                )
                ->setDetailLink($this->generateDetailLink($searchResultHit));
        }

        return $searchItems;
    }

    /**
     * @param array $params
     *
     * @return BoolQuery|null
     */
    protected function getElasticSearchFullTextSearchQuery(array $params = [])
    {
        /** @var BoolQuery|null $fullTextSearchQuery */
        $fullTextSearchQuery = null;
        if (array_key_exists('searchTerms', $params)) {

            /** @var array $searchTerms */
            $searchTerms = $params['searchTerms'];
            if (is_array($searchTerms) && !empty($searchTerms)) {
                $fullTextSearchQuery = new BoolQuery();
                foreach ($searchTerms as $searchTerm) {
                    $fullTextSearchQuery->add((new SimpleQueryStringQuery($searchTerm)));
                }

                /** @var int $currentDataPoolConfigId */
                $currentDataPoolConfigId = $this->dataPoolConfigService->getCurrentDataPoolConfig()->getId();

                $searchQueryEvent = new SearchQueryEvent($fullTextSearchQuery, $currentDataPoolConfigId);
                $this->eventDispatcher->dispatch($searchQueryEvent);

                if ($currentDataPoolConfigId === $searchQueryEvent->getDataPoolConfigId()) {
                    $fullTextSearchQuery = $searchQueryEvent->getSearchQuery();
                }
            }
        }

        return $fullTextSearchQuery;
    }

    /**
     * @param array $params
     *
     * @return array
     */
    protected function search($params = [])
    {
        /** @var array $defaultParams */
        $defaultParams = [
            '_source_excludes' => [ElasticSearchFields::SYSTEM_FIELDS . '.' . ElasticSearchFields::SYSTEM_FIELDS_COLLECTIONS],
            'track_total_hits' => true,
            'rest_total_hits_as_int' => true
        ];

        return $this->esClient->search(array_merge(
            $defaultParams,
            $params
        ));
    }

    /**
     * @param array $searchResultHit
     *
     * @return string
     *
     * @throws \Exception
     */
    protected function getNameBySearchResultHit(array $searchResultHit): string
    {
        $listableFieldsMapping = $this->getListableFieldsMapping();
        $nameAttributePath = isset(
            $listableFieldsMapping[
                $this->dataPoolConfigService->getCurrentDataPoolConfig()->getGridConfigurationNameAttribute()
            ]
        )
            ? $this->dataPoolConfigService->getCurrentDataPoolConfig()->getGridConfigurationNameAttribute()
            : ElasticSearchFields::SYSTEM_FIELDS . '.' . ElasticSearchFields::SYSTEM_FIELDS_NAME;

        $listableField = $listableFieldsMapping[$nameAttributePath];

        $listViewAttribute = $this->extractListViewAttribute($searchResultHit, $listableField->getPath(), $listableField);

        return $listViewAttribute['value'];
    }
}
