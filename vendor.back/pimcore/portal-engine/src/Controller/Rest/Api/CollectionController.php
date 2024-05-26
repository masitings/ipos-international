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

namespace Pimcore\Bundle\PortalEngineBundle\Controller\Rest\Api;

use Carbon\Carbon;
use Knp\Component\Pager\PaginatorInterface;
use Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse;
use Pimcore\Bundle\PortalEngineBundle\Entity\Collection;
use Pimcore\Bundle\PortalEngineBundle\Entity\CollectionShare;
use Pimcore\Bundle\PortalEngineBundle\Enum\Collection\Permission;
use Pimcore\Bundle\PortalEngineBundle\Exception\OutputErrorException;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataPoolConfigInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\DataObject\PortalUserGroupInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\Rest\ApiPayload;
use Pimcore\Bundle\PortalEngineBundle\Model\Rest\BasicListJsonModel;
use Pimcore\Bundle\PortalEngineBundle\Service\Collection\CollectionService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolService;
use Pimcore\Bundle\PortalEngineBundle\Service\Download\DownloadProviderService;
use Pimcore\Bundle\PortalEngineBundle\Service\PublicShare\PublicShareService;
use Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\User\UserSearchService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\Authentication\UserProvider;
use Pimcore\Localization\IntlFormatter;
use Pimcore\Model\DataObject\PortalUserGroup;
use Pimcore\Model\Element\ElementInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/collection", condition="request.attributes.get('isPortalEngineSite')")
 */
class CollectionController extends AbstractRestApiController
{
    /** @var CollectionService */
    protected $collectionService;
    /** @var UserSearchService */
    protected $userSearchService;
    /** @var UrlGeneratorInterface */
    protected $urlGenerator;
    /** @var IntlFormatter */
    protected $formatter;
    /** @var UserProvider */
    protected $userProvider;

    /** @var int */
    protected $itemCountPerPage = 10;

    /**
     * CollectionController constructor.
     *
     * @param CollectionService $collectionService
     * @param UserSearchService $userSearchService
     * @param UrlGeneratorInterface $urlGenerator
     * @param IntlFormatter $formatter
     * @param UserProvider $userProvider
     */
    public function __construct(CollectionService $collectionService, UserSearchService $userSearchService, UrlGeneratorInterface $urlGenerator, IntlFormatter $formatter, UserProvider $userProvider)
    {
        $this->collectionService = $collectionService;
        $this->userSearchService = $userSearchService;
        $this->urlGenerator = $urlGenerator;
        $this->formatter = $formatter;
        $this->userProvider = $userProvider;
    }

    /**
     * @Route(
     *     "/create",
     *     name="pimcore_portalengine_rest_api_collection_create"
     * )
     */
    public function createAction(Request $request): JsonResponse
    {
        /** @var ApiPayload $apiPayload */
        $apiPayload = new ApiPayload([]);
        /** @var \stdClass $requestBodyParams */
        $requestBodyParams = json_decode($request->getContent(), false);

        try {
            /** @var string $name */
            $name = trim($requestBodyParams->name);
            if (empty($name)) {
                throw new OutputErrorException($this->translator->trans('portal-engine.collection.name-empty'));
            }

            $collection = $this->collectionService->create($name);

            $apiPayload->setData($this->hydrateCollection($collection));
        } catch (\Exception $e) {
            $apiPayload->handleOutputErrorException($e);
        }

        return new JsonResponse(
            $apiPayload
        );
    }

    /**
     * @Route(
     *     "/rename/{collectionId}",
     *     name="pimcore_portalengine_rest_api_collection_rename",
     *     requirements={"collectionId"="\d+"}
     * )
     */
    public function renameAction(Request $request, $collectionId): JsonResponse
    {
        /** @var ApiPayload $apiPayload */
        $apiPayload = new ApiPayload([]);
        /** @var \stdClass $requestBodyParams */
        $requestBodyParams = json_decode($request->getContent(), false);

        try {
            /** @var Collection|null $collection */
            $collection = $this->collectionService->getCollectionById($collectionId);
            if (!$collection) {
                throw new OutputErrorException($this->translator->trans('portal-engine.collection.not-found'));
            }

            if (!$this->collectionService->isCollectionEditAllowed($collection)) {
                throw new OutputErrorException($this->translator->trans('portal-engine.collection.edit-not-allowed'));
            }

            /** @var string $name */
            $name = trim($requestBodyParams->name);
            if (empty($name)) {
                throw new OutputErrorException($this->translator->trans('portal-engine.collection.name-empty'));
            }

            $collection = $this->collectionService->rename($collection, $name);

            $apiPayload->setData($this->hydrateCollection($collection));
        } catch (\Exception $e) {
            $apiPayload->handleOutputErrorException($e);
        }

        return new JsonResponse(
            $apiPayload
        );
    }

    /**
     * @Route(
     *     "/delete/{collectionId}",
     *     name="pimcore_portalengine_rest_api_collection_delete",
     *     requirements={"collectionId"="\d+"}
     * )
     */
    public function deleteAction(Request $request, PublicShareService $publicShareService, $collectionId): JsonResponse
    {
        /** @var ApiPayload $apiPayload */
        $apiPayload = new ApiPayload([]);

        try {
            /** @var Collection|null $collection */
            $collection = $this->collectionService->getCollectionById($collectionId);
            if (!$collection) {
                throw new OutputErrorException($this->translator->trans('portal-engine.collection.not-found'));
            }

            if (!$this->collectionService->isCurrentUserCollectionOwner($collection)) {
                throw new OutputErrorException($this->translator->trans('portal-engine.collection.edit-not-allowed'));
            }

            $publicShareService->cleanupDeletedCollection($collection);

            $this->collectionService->delete($collection);
        } catch (\Exception $e) {
            $apiPayload->handleOutputErrorException($e);
        }

        return new JsonResponse(
            $apiPayload
        );
    }

    /**
     * @Route(
     *     "/add-to-collection/{collectionId}",
     *     name="pimcore_portalengine_rest_api_collection_add_to_collection",
     *     requirements={"collectionId"="\d+"}
     * )
     */
    public function addToCollectionAction(Request $request, DataPoolConfigService $dataPoolConfigService, $collectionId): JsonResponse
    {
        /** @var ApiPayload $apiPayload */
        $apiPayload = new ApiPayload([]);
        /** @var \stdClass $requestBodyParams */
        $requestBodyParams = json_decode($request->getContent(), false);

        try {
            /** @var Collection|null $collection */
            $collection = $this->collectionService->getCollectionById($collectionId);
            if (!$collection) {
                throw new OutputErrorException($this->translator->trans('portal-engine.collection.not-found'));
            }

            if (!$this->collectionService->isCollectionEditAllowed($collection)) {
                throw new OutputErrorException($this->translator->trans('portal-engine.collection.edit-not-allowed'));
            }

            $this->validateBodyParams([$requestBodyParams->dataPoolId, $requestBodyParams->selectedIds]);

            $dataPoolConfigService->setCurrentDataPoolConfigById($requestBodyParams->dataPoolId);

            /** @var ElementInterface[] $elements */
            $elements = $dataPoolConfigService->getElementsByIds($requestBodyParams->selectedIds);

            if (!empty($elements)) {
                $this->collectionService->addItemsToCollection($collection, $dataPoolConfigService->getCurrentDataPoolConfig(), $elements);
            }

            $apiPayload->setData($this->hydrateCollection($collection, true));
        } catch (\Exception $e) {
            $apiPayload->handleOutputErrorException($e);
        }

        return new JsonResponse(
            $apiPayload
        );
    }

    /**
     * @Route(
     *     "/remove-from-collection/{collectionId}",
     *     name="pimcore_portalengine_rest_api_collection_remove_from_collection",
     *     requirements={"collectionId"="\d+"}
     * )
     */
    public function removeFromCollectionAction(Request $request, DataPoolConfigService $dataPoolConfigService, $collectionId): JsonResponse
    {
        /** @var ApiPayload $apiPayload */
        $apiPayload = new ApiPayload([]);
        /** @var \stdClass $requestBodyParams */
        $requestBodyParams = json_decode($request->getContent(), false);

        try {
            /** @var Collection|null $collection */
            $collection = $this->collectionService->getCollectionById($collectionId);
            if (!$collection) {
                throw new OutputErrorException($this->translator->trans('portal-engine.collection.not-found'));
            }

            if (!$this->collectionService->isCollectionEditAllowed($collection)) {
                throw new OutputErrorException($this->translator->trans('portal-engine.collection.edit-not-allowed'));
            }

            $this->validateBodyParams([$requestBodyParams->dataPoolId, $requestBodyParams->selectedIds]);

            $dataPoolConfigService->setCurrentDataPoolConfigById($requestBodyParams->dataPoolId);

            /** @var ElementInterface[] $elements */
            $elements = $dataPoolConfigService->getElementsByIds($requestBodyParams->selectedIds);

            if (!empty($elements)) {
                $this->collectionService->removeItemsFromCollection($collection, $dataPoolConfigService->getCurrentDataPoolConfig(), $elements);
            }

            $apiPayload->setData($this->hydrateCollection($collection, false));
        } catch (\Exception $e) {
            $apiPayload->handleOutputErrorException($e);
        }

        return new JsonResponse(
            $apiPayload
        );
    }

    /**
     * Get all visible collections by current user (each permission)
     *
     * @Route(
     *     "/overview",
     *     name="pimcore_portalengine_rest_api_collection_overview"
     * )
     *
     * @param Request $request
     * @param PaginatorInterface $paginator
     *
     * @return JsonResponse
     *
     * @throws \Exception
     */
    public function overviewAction(Request $request, PaginatorInterface $paginator)
    {
        /** @var \Doctrine\ORM\QueryBuilder $collectionsQuery */
        $collectionsQuery = $this->collectionService->getCollectionsQuery();

        $pagination = $paginator->paginate($collectionsQuery, $request->get('page', 1), $this->itemCountPerPage);

        return new JsonResponse(
            new ApiPayload(BasicListJsonModel::createFromPagination($pagination, function ($collection) {
                return $this->hydrateCollection($collection);
            }))
        );
    }

    /**
     * Get all editable collections by current user (only edit permission)
     *
     * @Route(
     *     "/list",
     *     name="pimcore_portalengine_rest_api_collection_list"
     * )
     */
    public function listAction(): JsonResponse
    {
        /** @var array $apiPayloadData */
        $apiPayloadData = [];
        /** @var Collection[] $collections */
        $collections = $this->collectionService->getCollections(Permission::EDIT);

        foreach ($collections as $collection) {
            $apiPayloadData[] = $this->hydrateCollection($collection, false);
        }

        return new JsonResponse(
            new ApiPayload($apiPayloadData)
        );
    }

    /**
     * @Route(
     *     "/remove-user-sharing/{collectionId}",
     *     name="pimcore_portalengine_rest_api_collection_remove_user_sharing",
     *     requirements={"collectionId"="\d+"}
     * )
     */
    public function removeUserSharingAction(Request $request, $collectionId): JsonResponse
    {
        /** @var ApiPayload $apiPayload */
        $apiPayload = new ApiPayload([]);

        try {
            /** @var Collection|null $collection */
            $collection = $this->collectionService->getCollectionById($collectionId);
            if (!$collection) {
                throw new OutputErrorException($this->translator->trans('portal-engine.collection.not-found'));
            }

            if ($this->collectionService->isCurrentUserCollectionOwner($collection)) {
                throw new OutputErrorException($this->translator->trans('portal-engine.collection.edit-not-allowed'));
            }

            /** @var CollectionShare|null $collectionShare */
            $collectionShare = $this->collectionService->getCollectionShare($collection, $this->getUser()->getId(), 'user');
            if (!$collectionShare) {
                throw new OutputErrorException($this->translator->trans('portal-engine.collection.not-found'));
            }

            $this
                ->collectionService
                ->deleteCollectionSharing($collectionShare);
        } catch (\Exception $e) {
            $apiPayload->handleOutputErrorException($e);
        }

        return new JsonResponse(
            $apiPayload
        );
    }

    /**
     * @Route(
     *     "/update-sharing/{collectionId}",
     *     name="pimcore_portalengine_rest_api_collection_update_sharing",
     *     requirements={"collectionId"="\d+"}
     * )
     */
    public function updateSharingAction(Request $request, $collectionId): JsonResponse
    {
        /** @var ApiPayload $apiPayload */
        $apiPayload = new ApiPayload([]);
        /** @var array $apiPayloadData */
        $apiPayloadData = [];
        /** @var \stdClass $requestBodyParams */
        $requestBodyParams = json_decode($request->getContent(), false);

        try {
            /** @var Collection|null $collection */
            $collection = $this->collectionService->getCollectionById($collectionId);
            if (!$collection) {
                throw new OutputErrorException($this->translator->trans('portal-engine.collection.not-found'));
            }

            if (!$this->collectionService->isCurrentUserCollectionOwner($collection)) {
                throw new OutputErrorException($this->translator->trans('portal-engine.collection.edit-not-allowed'));
            }

            if (is_array($requestBodyParams->data)) {

                /** @var int[] $existingCollectionShareIds */
                $existingCollectionShareIds = [];

                foreach ($requestBodyParams->data as $data) {

                    /** @var string $userOrGroupId */
                    $userOrGroupId = $data->userOrGroup->id;
                    /** @var string $userOrGroupType */
                    $userOrGroupType = $data->userOrGroup->type;
                    /** @var string $permission */
                    $permission = $data->permission;

                    $this->validateBodyParams([$userOrGroupId, $userOrGroupType, $permission]);

                    /** @var CollectionShare|null $collectionShare */
                    $collectionShare = $this->collectionService->getCollectionShare($collection, (int)$userOrGroupId, (string)$userOrGroupType);

                    // create if not exists
                    if (!$collectionShare) {
                        $collectionShare = $this->collectionService->addCollectionSharing($collection, (int)$userOrGroupId, (string)$userOrGroupType, (string)$permission);

                    // update if permission changed
                    } elseif ($collectionShare->getPermission() !== $permission) {
                        $this->collectionService->updateCollectionSharing($collectionShare, $permission);
                    }

                    // store existing collection shareIds
                    $existingCollectionShareIds[] = $collectionShare->getId();
                }

                // delete all existing collection shareIds which are not in current request body
                foreach ($this->collectionService->getCollectionShareList($collection) as $collectionShare) {
                    if (!in_array($collectionShare->getId(), $existingCollectionShareIds)) {
                        $this->collectionService->deleteCollectionSharing($collectionShare, false);
                    }
                    $this->collectionService->flush();
                }
            }

            foreach ($this->collectionService->getCollectionShareList($collection) as $collectionShare) {

                /** @var array $hydratedCollectionShare */
                $hydratedCollectionShare = $this->hydrateCollectionShare($collectionShare);
                if (!empty($hydratedCollectionShare)) {
                    $apiPayloadData[] = $hydratedCollectionShare;
                }
            }

            $apiPayload->setData($apiPayloadData);
        } catch (\Exception $e) {
            $apiPayload->handleOutputErrorException($e);
        }

        return new JsonResponse(
            $apiPayload
        );
    }

    /**
     * @Route(
     *     "/share-list/{collectionId}",
     *     name="pimcore_portalengine_rest_api_collection_share_list",
     *     requirements={"collectionId"="\d+"}
     * )
     */
    public function getCollectionShareListAction(Request $request, $collectionId): JsonResponse
    {
        /** @var ApiPayload $apiPayload */
        $apiPayload = new ApiPayload([]);
        /** @var array $apiPayloadData */
        $apiPayloadData = [];

        try {
            /** @var Collection|null $collection */
            $collection = $this->collectionService->getCollectionById($collectionId);
            if (!$collection) {
                throw new OutputErrorException($this->translator->trans('portal-engine.collection.not-found'));
            }

            foreach ($this->collectionService->getCollectionShareList($collection) as $collectionShare) {

                /** @var array $hydratedCollectionShare */
                $hydratedCollectionShare = $this->hydrateCollectionShare($collectionShare);
                if (!empty($hydratedCollectionShare)) {
                    $apiPayloadData[] = $hydratedCollectionShare;
                }
            }

            $apiPayload->setData($apiPayloadData);
        } catch (\Exception $e) {
            $apiPayload->handleOutputErrorException($e);
        }

        return new JsonResponse(
            $apiPayload
        );
    }

    /**
     * @Route(
     *     "/share-list/users-and-groups",
     *     name="pimcore_portalengine_rest_api_collection_share_list_users_and_groups"
     * )
     */
    public function getUsersAndGroupsCollectionShareListAction(Request $request): JsonResponse
    {
        /** @var array $apiPayloadData */
        $apiPayloadData = [];
        /** @var string $searchTerm */
        $searchTerm = (string)$request->query->get('q');

        if (strlen($searchTerm) >= 2) {
            foreach ($this->userSearchService->getPortalUsersBySearchTerm($searchTerm) as $portalUser) {
                $apiPayloadData[] = $this->userSearchService->hydratePortalUser($portalUser);
            }

            foreach ($this->userSearchService->getPortalUserGroupsBySearchTerm($searchTerm) as $portalUserGroup) {
                $apiPayloadData[] = $this->userSearchService->hydratePortalUserGroup($portalUserGroup);
            }
        }

        return new JsonResponse(
            new ApiPayload($apiPayloadData)
        );
    }

    /**
     * @Route(
     *     "/detail-actions/{id}",
     *     name="pimcore_portalengine_rest_api_collection_detail_actions"
     * )
     *
     * @throws \Exception
     */
    public function getCollectionDetailActionsAction($id, DownloadProviderService $downloadProviderService, DataPoolService $dataPoolService, DataPoolConfigService $dataPoolConfigService): JsonResponse
    {
        $collection = $this->collectionService->getCollectionById($id);

        if (empty($collection)) {
            throw new NotFoundHttpException('collection not found');
        }

        $actions = [];
        if ($this->collectionService->isCurrentUserCollectionOwner($collection)) {
            $actions['share'] = true;
        }

        $dataPoolConfigs = $this->collectionService->getDataPoolConfigsByCollection($collection, true);
        if (sizeof($dataPoolConfigs)) {
            $downloadPools = [];
            foreach ($dataPoolConfigs as $dataPoolConfig) {
                if (!sizeof($downloadProviderService->getDownloadTypes($dataPoolConfig))) {
                    continue;
                }
                $dataPoolConfigService->setCurrentDataPoolConfig($dataPoolConfig);
                $searchService = $dataPoolService->getCurrentDataPool()->getSearchService();
                $ids = $this->collectionService->getItemIdsByDataPool($collection, $dataPoolConfig);
                if (!$searchService->hasItemsWithPermission(
                    \Pimcore\Bundle\PortalEngineBundle\Enum\Permission::DOWNLOAD,
                    [
                        'ids' => $ids,
                    ]
                )) {
                    continue;
                }

                $downloadPools[] = [
                    'dataPoolId' => $dataPoolConfig->getId(),
                    'name' => $dataPoolConfig->getDataPoolName()
                ];
            }
            if (sizeof($downloadPools)) {
                $actions['download'] = $downloadPools;
            }
        }

        $actions['publicShare'] = array_values(array_map(function (DataPoolConfigInterface $dataPoolConfig) {
            return [
                'id' => $dataPoolConfig->getId(),
                'name' => $dataPoolConfig->getDataPoolName(),
            ];
        }, $this->collectionService->getDataPoolConfigsByCollection($collection)));

        return new JsonResponse([
            'success' => true,
            'data' => [
                'actions' => $actions
            ]
        ]);
    }

    /**
     * @param array $bodyParams
     *
     * @return $this
     *
     * @throws \Exception
     */
    protected function validateBodyParams($bodyParams = [])
    {
        foreach ($bodyParams as $bodyParam) {
            if (is_null($bodyParam)) {
                throw new \Exception('bodyParam is not set');
            }
        }

        return $this;
    }

    /**
     * @param Collection $collection
     * @param bool $extendedData
     *
     * @return array
     *
     * @throws \Exception
     */
    protected function hydrateCollection($collection, $extendedData = true)
    {
        /** @var array $hydratedCollection */
        $hydratedCollection = [
            'id' => $collection->getId(),
            'name' => $collection->getName(),
            'itemCount' => $this->collectionService->getCollectionItemsCount($collection),
            'creationDate' => $this->formatter->formatDateTime(Carbon::createFromTimestamp($collection->getCreationDate()), IntlFormatter::DATETIME_SHORT)
        ];

        if ($extendedData) {
            $hydratedCollection['sharedByUserName'] = null;
            $hydratedCollection['formattedSharedDate'] = null;
            $hydratedCollection['sharedWith'] = null;

            /** @var CollectionShare $collectionShare */
            $collectionShare = $this->collectionService->getCollectionShareByCurrentUser($collection);
            /** @var bool $isCurrentUserCollectionOwner */
            $isCurrentUserCollectionOwner = $this->collectionService->isCurrentUserCollectionOwner($collection);
            if (!$isCurrentUserCollectionOwner) {

                /** @var string $sharedByUserName */
                $sharedByUserName = '-';
                $portalUser = $this->userProvider->getById($collection->getUserId());
                if ($portalUser) {
                    $sharedByUserName = ($portalUser->getFirstname() || $portalUser->getLastname())
                        ? trim($portalUser->getFirstname() . ' ' . $portalUser->getLastname())
                        : $portalUser->getKey();
                }

                $hydratedCollection['sharedByUserName'] = $sharedByUserName;
                $hydratedCollection['formattedSharedDate'] = $this->formatter->formatDateTime(Carbon::createFromTimestamp($collection->getCreationDate()), IntlFormatter::DATETIME_SHORT);
                $hydratedCollection['sharedWith'] = $collectionShare->getUserId() ? 'user' : 'group';
            }

            $hydratedCollection['permission'] = $collectionShare ? $collectionShare->getPermission() : ($isCurrentUserCollectionOwner ? Permission::EDIT : Permission::READ);
            $hydratedCollection['owner'] = $isCurrentUserCollectionOwner;
            $hydratedCollection['detailUrl'] = $this->urlGenerator->generate('pimcore_portalengine_collection_detail', ['collectionId' => $collection->getId()]);

            $hydratedCollection['dataPools'] = array_values(array_map(function (DataPoolConfigInterface $dataPoolConfig) {
                return [
                    'id' => $dataPoolConfig->getId(),
                    'name' => $dataPoolConfig->getDataPoolName(),
                ];
            }, $this->collectionService->getDataPoolConfigsByCollection($collection)));
        }

        return $hydratedCollection;
    }

    /**
     * @param CollectionShare $collectionShare
     *
     * @return array
     */
    protected function hydrateCollectionShare($collectionShare)
    {
        /** @var array $hydratedCollectionShare */
        $hydratedCollectionShare = [];
        /** @var array $userOrGroup */
        $userOrGroup = [];

        try {
            if ($collectionShare->getUserId()) {
                $portalUser = $this->userProvider->getById($collectionShare->getUserId());
                if (!$portalUser) {
                    throw new \Exception('portalUser not found');
                }

                $userOrGroup = $this->userSearchService->hydratePortalUser($portalUser);
            } elseif ($collectionShare->getUserGroupId()) {

                /** @var PortalUserGroupInterface|PortalUserGroup|null $portalUserGroup */
                $portalUserGroup = PortalUserGroup::getById($collectionShare->getUserGroupId());
                if (!$portalUserGroup) {
                    throw new \Exception('portalUserGroup not found');
                }

                $userOrGroup = $this->userSearchService->hydratePortalUserGroup($portalUserGroup);
            }

            $hydratedCollectionShare = [
                'userOrGroup' => $userOrGroup,
                'permission' => $collectionShare->getPermission(),
                'creationDate' => $this->formatter->formatDateTime(Carbon::createFromTimestamp($collectionShare->getCreationDate()), IntlFormatter::DATETIME_SHORT)
            ];
        } catch (\Exception $e) {
            //portalUser or portalGroup was deleted/unpublished, do not display this entry
        }

        return $hydratedCollectionShare;
    }
}
