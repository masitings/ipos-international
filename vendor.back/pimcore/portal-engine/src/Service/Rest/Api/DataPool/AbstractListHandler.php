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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\DataPool;

use Pimcore\Bundle\PortalEngineBundle\Enum\ElasticSearchFields;
use Pimcore\Bundle\PortalEngineBundle\Enum\Permission;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Filter\Filter;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Filter\FilterData;
use Pimcore\Bundle\PortalEngineBundle\Model\Rest\Api\DataPool\ListData;
use Pimcore\Bundle\PortalEngineBundle\Model\Rest\Api\DataPool\ListDataEntry;
use Pimcore\Bundle\PortalEngineBundle\Service\Collection\CollectionService;
use Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\ErrorHandler;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Search\SearchServiceInterface;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\PermissionService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\SecurityService;
use Pimcore\Translation\Translator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class AbstractListHandler
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\DataPool
 */
abstract class AbstractListHandler
{
    /** @var ErrorHandler */
    protected $errorHandler;
    /** @var Translator */
    protected $translator;
    /** @var SearchServiceInterface */
    protected $searchService;
    /** @var AuthorizationCheckerInterface */
    protected $authorizationChecker;
    /** @var PermissionService */
    protected $permissionService;
    /** @var SecurityService */
    protected $securityService;
    /** @var CollectionService */
    protected $collectionService;
    /** @var int */
    protected $selectAllMaxSize;

    /**
     * AbstractListHandler constructor.
     *
     * @param ErrorHandler $errorHandler
     * @param Translator $translator
     * @param SearchServiceInterface $searchService
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param PermissionService $permissionService
     * @param SecurityService $securityService
     * @param CollectionService $collectionService
     * @param int $selectAllMaxSize
     */
    public function __construct(ErrorHandler $errorHandler, Translator $translator, SearchServiceInterface $searchService, AuthorizationCheckerInterface $authorizationChecker, PermissionService $permissionService, SecurityService $securityService, CollectionService $collectionService, int $selectAllMaxSize = 500)
    {
        $this->errorHandler = $errorHandler;
        $this->translator = $translator;
        $this->searchService = $searchService;
        $this->authorizationChecker = $authorizationChecker;
        $this->permissionService = $permissionService;
        $this->securityService = $securityService;
        $this->collectionService = $collectionService;
        $this->selectAllMaxSize = $selectAllMaxSize;
    }

    /**
     * @param Request $request
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getData(Request $request): array
    {
        /** @var array $params */
        $params = $request->query->all();
        /** @var ListData $listData */
        $listData = $this
            ->searchService
            ->getListDataByParams($params);

        if ($listData->getPages() > 0 && $listData->getPage() > $listData->getPages()) {
            $this->errorHandler->setErrorMessage(
                $this->translator->trans('portal-engine.data-pool.error.max-page-exceeded')
            );
        }

        /** @var bool $permissionCollectionRemove */
        $permissionCollectionRemove = false;
        if (array_key_exists('collectionId', $params) && $collection = $this->collectionService->getCollectionById($params['collectionId'])) {
            $permissionCollectionRemove = $this->collectionService->isCollectionEditAllowed($collection);
        }

        /** @var array $items */
        $items = [];
        foreach ($listData->getEntries() as $listDataEntry) {
            $item = $this->getListItemData($listData, $listDataEntry);
            $item['permissions']['collectionRemove'] = $permissionCollectionRemove;

            $items[] = $item;
        }

        return [
            'pages' => $listData->getPages(),
            'page' => $listData->getPage(),
            'pageSize' => $listData->getPageSize(),
            'totalResults' => $listData->getTotal(),
            'currentOrderBy' => $listData->getCurrentOrderBy(),
            'orderByOptions' => $listData->getOrderByOptions(),
            'listViewAttributes' => $listData->getListViewAttributes(),
            'items' => $items
        ];
    }

    /**
     * @param Request $request
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getSelectAllIds(Request $request): array
    {
        $params = $request->query->all();

        $esSearch = $this
            ->searchService
            ->getSearchByParams($params)
            ->setSize($this->selectAllMaxSize)
            ->setFrom(0);

        $esClient = $this
            ->searchService
            ->getEsClient();

        $body = $esSearch->toArray();
        $body['_source'] = ElasticSearchFields::SYSTEM_FIELDS . '.' . ElasticSearchFields::SYSTEM_FIELDS_ID;

        $searchResult = $esClient->search([
            'index' => $this->searchService->getESIndexName(),
            'body' => $body
        ]);

        $hits = $searchResult['hits']['hits'] ?? [];

        return array_map(function ($hit) {
            return $hit['_source'][ElasticSearchFields::SYSTEM_FIELDS][ElasticSearchFields::SYSTEM_FIELDS_ID];
        }, $hits);
    }

    protected function getListItemData(ListData $listData, ListDataEntry $listDataEntry)
    {
        return [
            'id' => $listDataEntry->getId(),
            'name' => $listDataEntry->getName(),
            'thumbnail' => $listDataEntry->getThumbnail(),
            'detailLink' => $listDataEntry->getDetailLink($listData->getParams()),
            'listViewAttributes' => $listDataEntry->getListViewAttributes(),
            'permissions' => [
                'download' => $this->authorizationChecker->isGranted(
                    Permission::DOWNLOAD,
                    $listDataEntry->getFullPath()
                )
            ]
        ];
    }

    /**
     * @param Request $request
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getTagsData(Request $request)
    {
        return $this->searchService->getTagsDataByParams($request->query->all());
    }

    /**
     * @param Request $request
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getFoldersData(Request $request): array
    {
        return $this->searchService->getFoldersDataByParams($request->query->all());
    }

    /**
     * @param Request $request
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getFiltersData(Request $request): array
    {
        /** @var array $filtersData */
        $filtersData = [];
        /** @var Filter[] $filters */
        $filters = $this
            ->searchService
            ->getFilterData($request->query->all());

        foreach ($filters as $filter) {
            $filtersData['filters'][] = [
                'type' => $filter->getType(),
                'name' => $filter->getName(),
                'label' => $filter->getLabel(),
            ];

            /** @var FilterData $filterData */
            $filterData = $filter->getData();

            /** @var array $filterDataOptions */
            $filterDataOptions = [];
            foreach ($filterData->getOptions() as $filterDataOption) {
                $filterDataOptions[] = [
                    'value' => $filterDataOption->getValue(),
                    'label' => $filterDataOption->getLabel()
                    //'count' => $filterDataOption->getCount()
                ];
            }

            $filtersData['filtersData'][$filter->getName()] = [
                'currentValue' => $filterData->getCurrentValue(),
                'visible' => $filterData->isVisible(),
                'options' => $filterDataOptions
            ];
        }

        return $filtersData;
    }

    /**
     * @return int
     */
    public function getSelectAllMaxSize(): int
    {
        return $this->selectAllMaxSize;
    }
}
