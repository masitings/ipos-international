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
use Pimcore\Bundle\PortalEngineBundle\Entity\SavedSearch;
use Pimcore\Bundle\PortalEngineBundle\Entity\SavedSearchShare;
use Pimcore\Bundle\PortalEngineBundle\Exception\OutputErrorException;
use Pimcore\Bundle\PortalEngineBundle\Model\DataObject\PortalUserGroupInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\Rest\ApiPayload;
use Pimcore\Bundle\PortalEngineBundle\Model\Rest\BasicListJsonModel;
use Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\User\UserSearchService;
use Pimcore\Bundle\PortalEngineBundle\Service\SavedSearch\SavedSearchService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\Authentication\UserProvider;
use Pimcore\Localization\IntlFormatter;
use Pimcore\Model\DataObject\PortalUserGroup;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/saved-search", condition="request.attributes.get('isPortalEngineSite')")
 */
class SavedSearchController extends AbstractRestApiController
{
    /** @var SavedSearchService */
    protected $savedSearchService;
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
     * SavedSearchController constructor.
     *
     * @param SavedSearchService $savedSearchService
     * @param UserSearchService $userSearchService
     * @param UrlGeneratorInterface $urlGenerator
     * @param IntlFormatter $formatter
     * @param UserProvider $userProvider
     */
    public function __construct(SavedSearchService $savedSearchService, UserSearchService $userSearchService, UrlGeneratorInterface $urlGenerator, IntlFormatter $formatter, UserProvider $userProvider)
    {
        $this->savedSearchService = $savedSearchService;
        $this->userSearchService = $userSearchService;
        $this->urlGenerator = $urlGenerator;
        $this->formatter = $formatter;
        $this->userProvider = $userProvider;
    }

    /**
     * @Route(
     *     "/create",
     *     name="pimcore_portalengine_rest_api_saved_search_create"
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
                throw new OutputErrorException($this->translator->trans('portal-engine.saved-search.name-empty'));
            }

            /** @var SavedSearch|null $savedSearch */
            $savedSearch = $this->savedSearchService->getSavedSearchByName($name);
            if ($savedSearch) {
                throw new OutputErrorException($this->translator->trans('portal-engine.saved-search.name-exists'));
            }

            /** @var string $urlQuery */
            $urlQuery = (string)$requestBodyParams->urlQuery;

            $savedSearch = $this->savedSearchService->create($name, $urlQuery);

            $apiPayload->setData($this->hydrateSavedSearch($savedSearch));
        } catch (\Exception $e) {
            $apiPayload->handleOutputErrorException($e);
        }

        return new JsonResponse(
            $apiPayload
        );
    }

    /**
     * @Route(
     *     "/rename/{savedSearchId}",
     *     name="pimcore_portalengine_rest_api_saved_search_rename",
     *     requirements={"savedSearchId"="\d+"}
     * )
     */
    public function renameAction(Request $request, $savedSearchId): JsonResponse
    {
        /** @var ApiPayload $apiPayload */
        $apiPayload = new ApiPayload([]);
        /** @var \stdClass $requestBodyParams */
        $requestBodyParams = json_decode($request->getContent(), false);

        try {
            /** @var SavedSearch|null $savedSearch */
            $savedSearch = $this->savedSearchService->getSavedSearchById($savedSearchId);
            if (!$savedSearch) {
                throw new OutputErrorException($this->translator->trans('portal-engine.saved-search.not-found'));
            }
            if (!$this->savedSearchService->isCurrentUserSavedSearchOwner($savedSearch)) {
                throw new OutputErrorException($this->translator->trans('portal-engine.saved-search.edit-not-allowed'));
            }

            /** @var string $name */
            $name = trim($requestBodyParams->name);
            if (empty($name)) {
                throw new OutputErrorException($this->translator->trans('portal-engine.saved-search.name-empty'));
            }
            if ($this->savedSearchService->getSavedSearchByName($name)) {
                throw new OutputErrorException($this->translator->trans('portal-engine.saved-search.name-exists'));
            }

            $savedSearch = $this->savedSearchService->rename($savedSearch, $name);

            $apiPayload->setData($this->hydrateSavedSearch($savedSearch));
        } catch (\Exception $e) {
            $apiPayload->handleOutputErrorException($e);
        }

        return new JsonResponse(
            $apiPayload
        );
    }

    /**
     * @Route(
     *     "/delete/{savedSearchId}",
     *     name="pimcore_portalengine_rest_api_saved_search_delete",
     *     requirements={"savedSearchId"="\d+"}
     * )
     */
    public function deleteAction(Request $request, $savedSearchId): JsonResponse
    {
        /** @var ApiPayload $apiPayload */
        $apiPayload = new ApiPayload([]);

        try {
            /** @var SavedSearch|null $savedSearch */
            $savedSearch = $this->savedSearchService->getSavedSearchById($savedSearchId);
            if (!$savedSearch) {
                throw new OutputErrorException($this->translator->trans('portal-engine.saved-search.not-found'));
            }
            if (!$this->savedSearchService->isCurrentUserSavedSearchOwner($savedSearch)) {
                throw new OutputErrorException($this->translator->trans('portal-engine.saved-search.edit-not-allowed'));
            }

            $this->savedSearchService->delete($savedSearch);
        } catch (\Exception $e) {
            $apiPayload->handleOutputErrorException($e);
        }

        return new JsonResponse(
            $apiPayload
        );
    }

    /**
     * Get all visible savedSearchs by current user (also shared)
     *
     * @Route(
     *     "/overview",
     *     name="pimcore_portalengine_rest_api_saved_search_overview"
     * )
     */
    public function overviewAction(Request $request, PaginatorInterface $paginator)
    {
        /** @var \Doctrine\ORM\QueryBuilder $savedSearchQuery */
        $savedSearchQuery = $this->savedSearchService->getSavedSearchQuery();

        $pagination = $paginator->paginate($savedSearchQuery, $request->get('page', 1), $this->itemCountPerPage);

        return new JsonResponse(
            new ApiPayload(BasicListJsonModel::createFromPagination($pagination, function ($savedSearch) {
                return $this->hydrateSavedSearch($savedSearch);
            }))
        );
    }

    /**
     * Get all owned savedSearchs by current user
     *
     * @Route(
     *     "/list",
     *     name="pimcore_portalengine_rest_api_saved_search_list"
     * )
     */
    public function listAction(): JsonResponse
    {
        /** @var array $apiPayloadData */
        $apiPayloadData = [];
        /** @var SavedSearch[] $savedSearches */
        $savedSearches = $this->savedSearchService->getCurrentUserSavedSearches();

        foreach ($savedSearches as $savedSearch) {
            $apiPayloadData[] = $this->hydrateSavedSearch($savedSearch, false);
        }

        return new JsonResponse(
            new ApiPayload($apiPayloadData)
        );
    }

    /**
     * @Route(
     *     "/remove-user-sharing/{savedSearchId}",
     *     name="pimcore_portalengine_rest_api_saved_search_remove_user_sharing",
     *     requirements={"savedSearchId"="\d+"}
     * )
     */
    public function removeUserSharingAction(Request $request, $savedSearchId): JsonResponse
    {
        /** @var ApiPayload $apiPayload */
        $apiPayload = new ApiPayload([]);

        try {
            /** @var SavedSearch|null $savedSearch */
            $savedSearch = $this->savedSearchService->getSavedSearchById($savedSearchId);
            if (!$savedSearch) {
                throw new OutputErrorException($this->translator->trans('portal-engine.saved-search.not-found'));
            }
            if ($this->savedSearchService->isCurrentUserSavedSearchOwner($savedSearch)) {
                throw new OutputErrorException($this->translator->trans('portal-engine.saved-search.edit-not-allowed'));
            }

            /** @var SavedSearchShare|null $savedSearchShare */
            $savedSearchShare = $this->savedSearchService->getSavedSearchShare($savedSearch, $this->getUser()->getId(), 'user');
            if (!$savedSearchShare) {
                throw new OutputErrorException($this->translator->trans('portal-engine.saved-search.not-found'));
            }

            $this
                ->savedSearchService
                ->deleteSavedSearchSharing($savedSearchShare);
        } catch (\Exception $e) {
            $apiPayload->handleOutputErrorException($e);
        }

        return new JsonResponse(
            $apiPayload
        );
    }

    /**
     * @Route(
     *     "/update-sharing/{savedSearchId}",
     *     name="pimcore_portalengine_rest_api_saved_search_update_sharing",
     *     requirements={"savedSearchId"="\d+"}
     * )
     */
    public function updateSharingAction(Request $request, $savedSearchId): JsonResponse
    {
        /** @var ApiPayload $apiPayload */
        $apiPayload = new ApiPayload([]);
        /** @var array $apiPayloadData */
        $apiPayloadData = [];
        /** @var \stdClass $requestBodyParams */
        $requestBodyParams = json_decode($request->getContent(), false);

        try {
            /** @var SavedSearch|null $savedSearch */
            $savedSearch = $this->savedSearchService->getSavedSearchById($savedSearchId);
            if (!$savedSearch) {
                throw new OutputErrorException($this->translator->trans('portal-engine.saved-search.not-found'));
            }
            if (!$this->savedSearchService->isCurrentUserSavedSearchOwner($savedSearch)) {
                throw new OutputErrorException($this->translator->trans('portal-engine.saved-search.edit-not-allowed'));
            }

            if (is_array($requestBodyParams->data)) {

                /** @var int[] $existingSavedSearchShareIds */
                $existingSavedSearchShareIds = [];

                foreach ($requestBodyParams->data as $data) {

                    /** @var string $userOrGroupId */
                    $userOrGroupId = $data->userOrGroup->id;
                    /** @var string $userOrGroupType */
                    $userOrGroupType = $data->userOrGroup->type;

                    $this->validateBodyParams([$userOrGroupId, $userOrGroupType]);

                    /** @var SavedSearchShare|null $savedSearchShare */
                    $savedSearchShare = $this->savedSearchService->getSavedSearchShare($savedSearch, (int)$userOrGroupId, (string)$userOrGroupType);
                    if (!$savedSearchShare) {
                        $savedSearchShare = $this->savedSearchService->addSavedSearchSharing($savedSearch, (int)$userOrGroupId, (string)$userOrGroupType);
                    }
                    $existingSavedSearchShareIds[] = $savedSearchShare->getId();
                }

                // delete all existing savedSearch shareIds which are not in current request body
                foreach ($this->savedSearchService->getSavedSearchShareList($savedSearch) as $savedSearchShare) {
                    if (!in_array($savedSearchShare->getId(), $existingSavedSearchShareIds)) {
                        $this->savedSearchService->deleteSavedSearchSharing($savedSearchShare, false);
                    }
                    $this->savedSearchService->flush();
                }
            }

            foreach ($this->savedSearchService->getSavedSearchShareList($savedSearch) as $savedSearchShare) {

                /** @var array $hydratedSavedSearchShare */
                $hydratedSavedSearchShare = $this->hydrateSavedSearchShare($savedSearchShare);
                if (!empty($hydratedSavedSearchShare)) {
                    $apiPayloadData[] = $hydratedSavedSearchShare;
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
     *     "/share-list/{savedSearchId}",
     *     name="pimcore_portalengine_rest_api_saved_search_share_list",
     *     requirements={"savedSearchId"="\d+"}
     * )
     */
    public function getSavedSearchShareListAction(Request $request, $savedSearchId): JsonResponse
    {
        /** @var ApiPayload $apiPayload */
        $apiPayload = new ApiPayload([]);
        /** @var array $apiPayloadData */
        $apiPayloadData = [];

        try {
            /** @var SavedSearch|null $savedSearch */
            $savedSearch = $this->savedSearchService->getSavedSearchById($savedSearchId);
            if (!$savedSearch) {
                throw new OutputErrorException($this->translator->trans('portal-engine.saved-search.not-found'));
            }

            foreach ($this->savedSearchService->getSavedSearchShareList($savedSearch) as $savedSearchShare) {

                /** @var array $hydratedSavedSearchShare */
                $hydratedSavedSearchShare = $this->hydrateSavedSearchShare($savedSearchShare);
                if (!empty($hydratedSavedSearchShare)) {
                    $apiPayloadData[] = $hydratedSavedSearchShare;
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
     *
     *
     * @Route(
     *     "/share-list/users-and-groups",
     *     name="pimcore_portalengine_rest_api_saved_search_share_list_users_and_groups"
     * )
     */
    public function getUsersAndGroupsSavedSearchShareListAction(Request $request): JsonResponse
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
     * @param SavedSearch $savedSearch
     * @param bool $extendedData
     *
     * @return array
     *
     * @throws \Exception
     */
    protected function hydrateSavedSearch($savedSearch, $extendedData = true)
    {
        /** @var array $hydratedSavedSearch */
        $hydratedSavedSearch = [
            'id' => $savedSearch->getId(),
            'name' => $savedSearch->getName(),
            'urlQuery' => $savedSearch->getUrlQuery(),
            'creationDate' => $this->formatter->formatDateTime(Carbon::createFromTimestamp($savedSearch->getCreationDate()), IntlFormatter::DATETIME_SHORT)
        ];

        if ($extendedData) {
            $hydratedSavedSearch['sharedByUserName'] = null;
            $hydratedSavedSearch['formattedSharedDate'] = null;
            $hydratedSavedSearch['sharedWith'] = null;

            /** @var bool $isCurrentUserSavedSearchOwner */
            $isCurrentUserSavedSearchOwner = $this->savedSearchService->isCurrentUserSavedSearchOwner($savedSearch);
            if (!$isCurrentUserSavedSearchOwner) {
                /** @var SavedSearchShare $savedSearchShare */
                $savedSearchShare = $this->savedSearchService->getSavedSearchShareByCurrentUser($savedSearch);
                /** @var string $sharedByUserName */
                $sharedByUserName = '-';
                $portalUser = $this->userProvider->getById($savedSearch->getUserId());
                if ($portalUser) {
                    $sharedByUserName = ($portalUser->getFirstname() || $portalUser->getLastname())
                        ? trim($portalUser->getFirstname() . ' ' . $portalUser->getLastname())
                        : $portalUser->getKey();
                }

                $hydratedSavedSearch['sharedByUserName'] = $sharedByUserName;
                $hydratedSavedSearch['formattedSharedDate'] = $this->formatter->formatDateTime(Carbon::createFromTimestamp($savedSearch->getCreationDate()), IntlFormatter::DATETIME_SHORT);
                $hydratedSavedSearch['sharedWith'] = $savedSearchShare->getUserId() ? 'user' : 'group';
            }

            $hydratedSavedSearch['owner'] = $isCurrentUserSavedSearchOwner;
            $hydratedSavedSearch['detailUrl'] = $this->urlGenerator->generate('pimcore_portalengine_search') . $savedSearch->getUrlQuery();
        }

        return $hydratedSavedSearch;
    }

    /**
     * @param SavedSearchShare $savedSearchShare
     *
     * @return array
     */
    public function hydrateSavedSearchShare($savedSearchShare)
    {
        /** @var array $hydratedSavedSearchShare */
        $hydratedSavedSearchShare = [];
        /** @var array $userOrGroup */
        $userOrGroup = [];

        try {
            if ($savedSearchShare->getUserId()) {
                $portalUser = $this->userProvider->getById($savedSearchShare->getUserId());
                if (!$portalUser) {
                    throw new \Exception('portalUser not found');
                }

                $userOrGroup = $this->userSearchService->hydratePortalUser($portalUser);
            } elseif ($savedSearchShare->getUserGroupId()) {

                /** @var PortalUserGroupInterface|PortalUserGroup|null $portalUserGroup */
                $portalUserGroup = PortalUserGroup::getById($savedSearchShare->getUserGroupId());
                if (!$portalUserGroup) {
                    throw new \Exception('portalUserGroup not found');
                }

                $userOrGroup = $this->userSearchService->hydratePortalUserGroup($portalUserGroup);
            }

            $hydratedSavedSearchShare = [
                'userOrGroup' => $userOrGroup,
                'creationDate' => $this->formatter->formatDateTime(Carbon::createFromTimestamp($savedSearchShare->getCreationDate()), IntlFormatter::DATETIME_SHORT)
            ];
        } catch (\Exception $e) {
            //portalUser or portalGroup was deleted/unpublished, do not display this entry
        }

        return $hydratedSavedSearchShare;
    }
}
