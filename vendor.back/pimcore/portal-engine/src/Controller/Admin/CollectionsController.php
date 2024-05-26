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

namespace Pimcore\Bundle\PortalEngineBundle\Controller\Admin;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Pimcore\Bundle\AdminBundle\Controller\AdminController;
use Pimcore\Bundle\PortalEngineBundle\Entity\Collection;
use Pimcore\Bundle\PortalEngineBundle\Service\Collection\AdminCollectionService;
use Pimcore\Bundle\PortalEngineBundle\Service\Collection\CollectionService;
use Pimcore\Bundle\PortalEngineBundle\Service\PortalConfig\PortalConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\PublicShare\PublicShareService;
use Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\User\UserSearchService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\Authentication\UserProvider;
use Pimcore\Logger;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\PortalUser;
use Pimcore\Model\Site;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/portal-engine")
 */
class CollectionsController extends AdminController
{
    /**
     * @Route("/check-user-assignment", name="pimcore_portalengine_admin_collection_check_user_assignment")
     *
     * @param PortalConfigService $portalConfigService
     *
     * @return \Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse
     */
    public function checkAdminUserAssignmentAction(AdminCollectionService $adminCollectionService)
    {
        try {
            $adminCollectionService->applyPortalUserToSecurityService($this->getAdminUser());

            return $this->adminJson(['success' => true]);
        } catch (\Exception $e) {
            return $this->adminJson(['success' => false]);
        }
    }

    /**
     * @Route("/portals", name="pimcore_portalengine_admin_collection_portallist")
     *
     * @param PortalConfigService $portalConfigService
     *
     * @return \Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse
     */
    public function portalsAction(PortalConfigService $portalConfigService)
    {
        $portalConfigs = $portalConfigService->getAllPortalConfigs();

        $data = [];
        foreach ($portalConfigs as $portalConfig) {
            $data[] = [
                'id' => $portalConfig->getPortalId(),
                'name' => $portalConfig->getPortalName()
            ];
        }

        return $this->adminJson(['data' => $data, 'success' => true]);
    }

    /**
     * @Route("/user-search", name="pimcore_portalengine_admin_collection_usersearch")
     *
     * @param Request $request
     * @param CollectionService $collectionService
     * @param AdminCollectionService $adminCollectionService
     * @param UserSearchService $userSearchService
     *
     * @return \Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse
     *
     * @throws \Exception
     */
    public function userPortalsAction(Request $request, CollectionService $collectionService, AdminCollectionService $adminCollectionService, UserSearchService $userSearchService)
    {
        $adminCollectionService->applyPortalUserToSecurityService($this->getAdminUser());

        $data = [];
        /** @var string $searchTerm */
        $searchTerm = (string)$request->get('query');

        if (strlen($searchTerm) >= 2) {
            $collection = $collectionService->getCollectionById($request->get('collectionId'), false, false);
            $excludedUserId = $collection ? $collection->getUserId() : null;

            foreach ($userSearchService->getPortalUsersBySearchTerm($searchTerm, $excludedUserId) as $portalUser) {
                $data[] = [
                    'id' => $portalUser->getId(),
                    'name' => $portalUser->getEmail(),
                    'type' => 'user'
                ];
            }

            foreach ($userSearchService->getPortalUserGroupsBySearchTerm($searchTerm) as $portalUserGroup) {
                $data[] = [
                    'id' => $portalUserGroup->getId(),
                    'name' => $portalUserGroup->getKey(),
                    'type' => 'group'
                ];
            }

            $addedIds = $request->get('addedIds', []);
            $data = array_filter($data, function ($item) use ($addedIds) {
                return !in_array($item['id'], $addedIds);
            });
        }

        return $this->adminJson(['data' => $data, 'success' => true]);
    }

    /**
     * @Route("/grid", name="pimcore_portalengine_admin_collection_grid")
     *
     * @param Request $request
     * @param AdminCollectionService $adminCollectionService
     * @param CollectionService $collectionService
     * @param UserProvider $userProvider
     * @param PortalConfigService $portalConfigService
     *
     * @return \Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function gridAction(Request $request, AdminCollectionService $adminCollectionService, CollectionService $collectionService, UserProvider $userProvider, PortalConfigService $portalConfigService, PublicShareService $publicShareService)
    {
        $adminCollectionService->applyPortalUserToSecurityService($this->getAdminUser());

        if ($request->get('data')) {
            if ($request->get('xaction') == 'destroy') {
                $data = $this->decodeJson($request->get('data'));

                $collection = $collectionService->getCollectionById($data['id'], false, false);
                $publicShareService->cleanupDeletedCollection($collection);
                $collectionService->delete($collection);

                return $this->adminJson(['success' => true, 'data' => []]);
            } elseif ($request->get('xaction') == 'update') {
                $data = $this->decodeJson($request->get('data'));

                $collection = $collectionService->getCollectionById($data['id'], false, false);
                $collectionService->rename($collection, $data['name']);

                return $this->adminJson(['data' => $this->hydrateCollection($collection, $collectionService, $userProvider, $portalConfigService), 'success' => true]);
            } elseif ($request->get('xaction') == 'create') {
                $data = $this->decodeJson($request->get('data'));

                $site = Site::getByRootId($data['portal']);
                $collection = $collectionService->create($data['name'], $site->getId());

                return $this->adminJson(['data' => $this->hydrateCollection($collection, $collectionService, $userProvider, $portalConfigService), 'success' => true]);
            }
        } else {
            $filterForCurrentUserPermission = true;
            if ($this->getAdminUser()->isAdmin() && $request->get('showAll') == 'true') {
                $filterForCurrentUserPermission = false;
            }

            $queryBuilder = $collectionService->getCollectionsQuery(false, $filterForCurrentUserPermission);

            $queryBuilder
                ->setFirstResult($request->get('start', 0))
                ->setMaxResults($request->get('limit', 50))
            ;

            if ($request->get('sort')) {
                $sort = json_decode($request->get('sort'), true);
                foreach ($sort as $sortEntry) {
                    $queryBuilder->orderBy('c.' . $sortEntry['property'], $sortEntry['direction']);
                }
            }

            if ($filter = $request->get('filter')) {
                $queryBuilder
                    ->andWhere('c.name LIKE :name')
                    ->setParameter('name', '%' . $filter . '%');
            }

            $paginator = new Paginator($queryBuilder);

            $data = [];
            foreach ($paginator as $collection) {
                /**
                 * @var $collection Collection
                 */
                $data[] = $this->hydrateCollection($collection, $collectionService, $userProvider, $portalConfigService);
            }

            return $this->adminJson(['data' => $data, 'success' => true, 'total' => $paginator->count()]);
        }

        return $this->adminJson(['success' => false]);
    }

    protected function hydrateCollection(Collection $collection, CollectionService $collectionService, UserProvider $userProvider, PortalConfigService $portalConfigService): array
    {
        $user = $collection->getUserId() ? $userProvider->getById($collection->getUserId()) : null;

        $site = Site::getById($collection->getCurrentSiteId());
        $portalConfig = $portalConfigService->getPortalConfigById($site->getRootId());

        return [
            'id' => $collection->getId(),
            'name' => $collection->getName(),
            'userId' => $user ? $user->getEmail() : '',
            'itemCount' => $collectionService->getCollectionItemsCount($collection),
            'creationDate' => $collection->getCreationDate(),
            'currentSiteId' => $portalConfig->getPortalName()
        ];
    }

    /**
     * @Route("/collection-sharelist", name="pimcore_portalengine_admin_collection_sharelist")
     *
     * @param Request $request
     * @param AdminCollectionService $adminCollectionService
     * @param CollectionService $collectionService
     *
     * @throws \Exception
     */
    public function collectionSharingAction(Request $request, AdminCollectionService $adminCollectionService, CollectionService $collectionService)
    {
        $adminCollectionService->applyPortalUserToSecurityService($this->getAdminUser());

        $collection = $collectionService->getCollectionById($request->get('collectionId', -1), false, !$this->getAdminUser()->isAdmin());
        if (empty($collection)) {
            throw new \Exception('Collection not found.');
        }

        $collectionShareList = $collectionService->getCollectionShareList($collection);

        $data = [];
        foreach ($collectionShareList as $collectionShare) {
            $shareRecipientId = $collectionShare->getUserId() ?: $collectionShare->getUserGroupId();
            $shareRecipient = Concrete::getById($shareRecipientId);
            if (!empty($shareRecipient)) {
                $shareRecipientName = $shareRecipient->getKey();
                if ($shareRecipient instanceof PortalUser) {
                    $shareRecipientName = $shareRecipient->getEmail();
                }

                $data[] = [
                    'shareRecipientId' => $shareRecipientId,
                    'shareRecipient' => $shareRecipientName,
                    'permission' => $collectionShare->getPermission()
                ];
            }
        }

        return $this->adminJson(['data' => $data, 'success' => true]);
    }

    /**
     * @Route("/collection-update-share", name="pimcore_portalengine_admin_collection_update_share")
     *
     * @param Request $request
     * @param AdminCollectionService $adminCollectionService
     * @param CollectionService $collectionService
     *
     * @throws \Exception
     */
    public function updateCollectionSharingAction(Request $request, AdminCollectionService $adminCollectionService, CollectionService $collectionService)
    {
        $adminCollectionService->applyPortalUserToSecurityService($this->getAdminUser());

        try {
            $shares = $request->get('shares', []);
            $collection = $collectionService->getCollectionById($request->get('collectionId', -1), false, !$this->getAdminUser()->isAdmin());
            if (empty($collection)) {
                throw new \Exception('Collection not found.');
            }

            foreach ($shares as $shareRecipientId => $permission) {
                $shareRecipient = Concrete::getById($shareRecipientId);
                $shareType = $shareRecipient instanceof PortalUser ? 'user' : 'group';

                $collectionShare = $collectionService->getCollectionShare($collection, (int)$shareRecipientId, (string)$shareType);
                // create if not exists

                if ($permission === 'none') {
                    if ($collectionShare) {
                        $collectionService->deleteCollectionSharing($collectionShare);
                    }
                } else {
                    if (!$collectionShare) {
                        $collectionShare = $collectionService->addCollectionSharing($collection, (int)$shareRecipientId, (string)$shareType, (string)$permission);
                    // update if permission changed
                    } elseif ($collectionShare->getPermission() !== $permission) {
                        $collectionService->updateCollectionSharing($collectionShare, $permission);
                    }
                }
            }

            return $this->adminJson(['success' => true]);
        } catch (\Exception $e) {
            Logger::error($e);

            return $this->adminJson(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * @Route("/tree", name="pimcore_portalengine_admin_collection_tree")
     *
     * @param Request $request
     * @param AdminCollectionService $adminCollectionService
     * @param CollectionService $collectionService
     *
     * @return JsonResponse
     */
    public function getCollectionDataAction(Request $request, AdminCollectionService $adminCollectionService, CollectionService $collectionService)
    {
        $adminCollectionService->applyPortalUserToSecurityService($this->getAdminUser());
        $collection = $collectionService->getCollectionById($request->get('collectionId'), false, !$this->getAdminUser()->isAdmin());

        if ($collection) {
            return $this->adminJson([
                'success' => true,
                'collectionId' => $collection->getId(),
                'name' => $collection->getName()
            ]);
        } else {
            return $this->adminJson(['success' => false]);
        }
    }

    /**
     * @Route("/tree-data", name="pimcore_portalengine_admin_collection_tree_data")
     *
     * @param Request $request
     * @param AdminCollectionService $adminCollectionService
     * @param CollectionService $collectionService
     *
     * @return JsonResponse
     *
     * @throws \Exception
     */
    public function getTreeDataAction(Request $request, AdminCollectionService $adminCollectionService, CollectionService $collectionService)
    {
        $adminCollectionService->applyPortalUserToSecurityService($this->getAdminUser());
        $collection = $collectionService->getCollectionById($request->get('collectionId'), false, !$this->getAdminUser()->isAdmin());
        if (empty($collection)) {
            return $this->adminJson([]);
        }

        $parent = $request->get('node');
        if ($parent == 'root') {
            return $this->adminJson($adminCollectionService->getTreeRootNodes($this->getAdminUser(), $collection));
        } else {
            $data = $adminCollectionService->getCollectionNodesForDataPool(
                $this->getAdminUser(),
                $collection,
                intval($parent),
                intval($request->get('limit')),
                intval($request->get('start')),
                strip_tags($request->get('filter'))
            );

            return $this->adminJson($data);
        }
    }

    /**
     * @Route("/tree-add", name="pimcore_portalengine_admin_collection_add")
     *
     * @param Request $request
     * @param AdminCollectionService $adminCollectionService
     * @param CollectionService $collectionService
     *
     * @return JsonResponse
     */
    public function addElementAction(Request $request, AdminCollectionService $adminCollectionService, CollectionService $collectionService)
    {
        try {
            $adminCollectionService->applyPortalUserToSecurityService($this->getAdminUser());

            $collection = $collectionService->getCollectionById($request->get('collectionId'), false, !$this->getAdminUser()->isAdmin());

            if (empty($collection)) {
                throw new \Exception('Collection not found');
            }

            $elementIds = explode(',', $request->get('elementIds'));
            $adminCollectionService->addElementsToCollection($collection, intval($request->get('targetId')), $elementIds);

            return $this->adminJson(['success' => true]);
        } catch (\Exception $e) {
            Logger::error($e);

            return $this->adminJson(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * @Route("/tree-remove", name="pimcore_portalengine_admin_collection_remove")
     *
     * @param Request $request
     * @param AdminCollectionService $adminCollectionService
     * @param CollectionService $collectionService
     *
     * @return JsonResponse
     */
    public function removeElementAction(Request $request, AdminCollectionService $adminCollectionService, CollectionService $collectionService)
    {
        try {
            $adminCollectionService->applyPortalUserToSecurityService($this->getAdminUser());

            $collection = $collectionService->getCollectionById($request->get('collectionId'), false, !$this->getAdminUser()->isAdmin());

            if (empty($collection)) {
                throw new \Exception('Collection not found');
            }

            $elementIds = explode(',', $request->get('elementIds'));
            $parentIds = $adminCollectionService->removeElementsFromCollection($collection, $elementIds);

            return $this->adminJson(['success' => true, 'parentIds' => array_values($parentIds)]);
        } catch (\Exception $e) {
            Logger::error($e);

            return $this->adminJson(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
