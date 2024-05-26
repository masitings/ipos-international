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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Collection;

use Pimcore\Bundle\AdminBundle\Controller\Traits\AdminStyleTrait;
use Pimcore\Bundle\AdminBundle\Security\User\TokenStorageUserResolver;
use Pimcore\Bundle\PortalEngineBundle\Entity\Collection;
use Pimcore\Bundle\PortalEngineBundle\Enum\ElementType;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataObjectConfig;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\Authentication\UserProvider;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\PermissionService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\SecurityService;
use Pimcore\Event\Admin\ElementAdminStyleEvent;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\Element\Service;
use Pimcore\Model\Site;
use Pimcore\Model\User;

class AdminCollectionService
{
    use AdminStyleTrait;

    /**
     * @var TokenStorageUserResolver
     */
    protected $tokenStorageUserResolver;

    /**
     * @var UserProvider
     */
    protected $userProvider;

    /**
     * @var SecurityService
     */
    protected $securityService;

    /**
     * @var DataPoolConfigService
     */
    protected $dataPoolConfigService;

    /**
     * @var CollectionService
     */
    protected $collectionService;

    /**
     * @var PermissionService
     */
    protected $permissionService;

    /**
     * AdminCollectionService constructor.
     *
     * @param TokenStorageUserResolver $tokenStorageUserResolver
     * @param UserProvider $userProvider
     * @param SecurityService $securityService
     * @param DataPoolConfigService $dataPoolConfigService
     * @param CollectionService $collectionService
     * @param PermissionService $permissionService
     */
    public function __construct(TokenStorageUserResolver $tokenStorageUserResolver, UserProvider $userProvider, SecurityService $securityService, DataPoolConfigService $dataPoolConfigService, CollectionService $collectionService, PermissionService $permissionService)
    {
        $this->tokenStorageUserResolver = $tokenStorageUserResolver;
        $this->userProvider = $userProvider;
        $this->securityService = $securityService;
        $this->dataPoolConfigService = $dataPoolConfigService;
        $this->collectionService = $collectionService;
        $this->permissionService = $permissionService;
    }

    public function applyPortalUserToSecurityService(User $user)
    {
        if ($portalUser = $this->userProvider->getPortalUserForPimcoreUser($user)) {
            $this->securityService->setPortalUser($portalUser);
        } else {
            throw new \Exception('No Portal User found for current Pimcore user!');
        }
    }

    public function getTreeRootNodes(User $user, Collection $collection): array
    {
        $site = Site::getById($collection->getCurrentSiteId());
        $dataPoolConfigs = $this->dataPoolConfigService->getDataPoolConfigsFromSite($site);

        $isEditAllowed = $this->collectionService->isCollectionEditAllowed($collection);

        $nodes = [];
        foreach ($dataPoolConfigs as $dataPoolConfig) {
            if ($user->isAdmin() || $this->permissionService->isDataPoolAccessAllowed($this->securityService->getPortalUser(), $dataPoolConfig->getId())) {
                $icon = 'pimcore_icon_folder';
                $className = '';
                if ($dataPoolConfig instanceof DataObjectConfig) {
                    $class = ClassDefinition::getById($dataPoolConfig->getDataObjectClass());
                    $className = $class->getName();
                }

                $nodes[] = [
                    'id' => $dataPoolConfig->getId(),
                    'elementId' => $dataPoolConfig->getId(),
                    'key' => $dataPoolConfig->getDataPoolName(),
                    'text' => $dataPoolConfig->getDataPoolName(),
                    'basePath' => '/',
                    'allowDrop' => $isEditAllowed,
                    'allowDrag' => false,
                    'allowChildren' => true,
                    'elementType' => $dataPoolConfig->getElementType(),
                    'className' => $className,
                    'leaf' => false,
                    'expanded' => false,
                    'iconCls' => $icon,
                    'permissions' => [
                        'view' => false,
                        'remove' => false,
                        'settings' => false,
                        'publish' => false,
                        'rename' => false
                    ]
                ];
            }
        }

        return [
            'total' => count($nodes),
            'nodes' => $nodes
        ];
    }

    public function getCollectionNodesForDataPool(User $user, Collection $collection, int $dataPoolId, int $limit, int $offset, string $filterString)
    {
        $isEditAllowed = $this->collectionService->isCollectionEditAllowed($collection);
        $items = $this->collectionService->getCollectionItems($collection);

        $itemsDataStructure = [];
        $elementType = null;
        foreach ($items as $item) {
            $itemsDataStructure[$item->getDataPoolId()][] = $item->getElementId();

            if ($item->getDataPoolId() == $dataPoolId) {
                $elementType = $item->getElementType();
            }
        }

        if (empty($itemsDataStructure[$dataPoolId])) {
            return [];
        }

        $itemsIds = $itemsDataStructure[$dataPoolId];

        if ($elementType === ElementType::ASSET) {
            $listing = new Asset\Listing();
            $listing->addConditionParam('filename LIKE ?', [$filterString . '%']);
            $listing->addConditionParam('id IN (?)', [$itemsIds]);
            $listing->setOrderKey('filename');

            //permissions
            if (!$user->isAdmin()) {
                $userIds = $user->getRoles();
                $userIds[] = $user->getId();
                $listing->addConditionParam('
                    (
                        (SELECT list FROM users_workspaces_asset WHERE userId IN (?) AND LOCATE(CONCAT(path,filename),cpath)=1 ORDER BY LENGTH(cpath) DESC, FIELD(userId, ?) DESC, list DESC LIMIT 1)=1
                        OR
                        (SELECT list FROM users_workspaces_asset WHERE userId IN (?) AND LOCATE(cpath,CONCAT(path,filename))=1 ORDER BY LENGTH(cpath) DESC, FIELD(userId, ?) DESC, list DESC LIMIT 1)=1
                    )',
                    [$userIds, $user->getId(), $userIds, $user->getId()]
                );
            }
        } elseif ($elementType === ElementType::DATA_OBJECT) {
            $listing = new DataObject\Listing();
            $listing->setCondition('o_key LIKE ?', [$filterString . '%']);
            $listing->addConditionParam('o_id IN (?)', [$itemsIds]);
            $listing->setOrderKey('o_key');

            //permissions
            if (!$user->isAdmin()) {
                $userIds = $user->getRoles();
                $userIds[] = $user->getId();
                $listing->addConditionParam('
                    (
                        (SELECT list FROM users_workspaces_object WHERE userId IN (?) AND LOCATE(CONCAT(objects.o_path,objects.o_key),cpath)=1 ORDER BY LENGTH(cpath) DESC, FIELD(userId, ?) DESC, list DESC LIMIT 1)=1
                        OR
                        (SELECT list FROM users_workspaces_object WHERE userId IN (?) AND LOCATE(cpath,CONCAT(objects.o_path,objects.o_key))=1 ORDER BY LENGTH(cpath) DESC, FIELD(userId, ?) DESC, list DESC LIMIT 1)=1
                    )',
                    [$userIds, $user->getId(), $userIds, $user->getId()]
                );
            }
        } else {
            throw new \Exception("Invalid element type '$elementType'.");
        }

        $listing->setLimit($limit);
        $listing->setOffset($offset);

        $dataPoolConfig = $this->dataPoolConfigService->getDataPoolConfigById($dataPoolId);

        $nodes = [];
        foreach ($listing as $element) {
            $node = [
                'elementId' => $elementType . '-' . $element->getId(),
                'id' => $element->getId(),
                'key' => $element->getKey(),
                'type' => $element->getType(),
                'className' => $element instanceof DataObject\Concrete ? $element->getClassname() : '',
                'elementType' => $elementType,
                'text' => $element->getKey() . ' (' . $element->getPath() . ')',
                'path' => $element->getFullPath(),
                'basePath' => '/' . $dataPoolConfig->getDataPoolName(),
                'allowDrop' => $isEditAllowed,
                'allowChildren' => false,
                'leaf' => true,
                'expanded' => false,
                'permissions' => [
                    'view' => true,
                    'remove' => $isEditAllowed,
                    'settings' => false,
                    'publish' => false,
                    'rename' => false
                ]
            ];
            $this->addAdminStyle($element, ElementAdminStyleEvent::CONTEXT_TREE, $node);

            $nodes[] = $node;
        }

        return [
            'offset' => $offset,
            'limit' => $limit,
            'total' => $listing->getTotalCount(),
            'nodes' => $nodes,
            'fromPaging' => 0,
            'inSearch' => empty($filterString) ? 0 : 1,
            'filter' => $filterString
        ];
    }

    public function addElementsToCollection(Collection $collection, int $dataPoolId, array $elementIds)
    {
        $isEditAllowed = $this->collectionService->isCollectionEditAllowed($collection);
        if (!$isEditAllowed) {
            throw new \Exception('No edit permission for collection.');
        }

        $dataPoolConfig = $this->dataPoolConfigService->getDataPoolConfigById($dataPoolId);
        $classId = null;
        if ($dataPoolConfig instanceof DataObjectConfig) {
            $classId = $dataPoolConfig->getDataObjectClass();
        }

        $elements = [];

        foreach ($elementIds as $elementId) {
            $element = Service::getElementById($dataPoolConfig->getElementType(), $elementId);
            $elementType = Service::getElementType($element);
            if (empty($element)) {
                throw new \Exception("Element $elementId not found! Not adding anything.");
            }
            if ($elementType !== $dataPoolConfig->getElementType() || (!empty($classId) && $element instanceof DataObject\Concrete && $element->getClassId() != $classId)) {
                throw new \Exception("Invalid element $elementId! {$dataPoolConfig->getElementType()} {$element->getType()} Not adding anything.");
            }

            $elements[] = $element;
        }
        $this->collectionService->addItemsToCollection($collection, $dataPoolConfig, $elements);
    }

    public function removeElementsFromCollection(Collection $collection, array $elementIds): array
    {
        $isEditAllowed = $this->collectionService->isCollectionEditAllowed($collection);
        if (!$isEditAllowed) {
            throw new \Exception('No edit permission for collection.');
        }

        $parentIds = [];
        foreach ($elementIds as $elementIdTuple) {
            list($parentId, $elementId) = explode('_', $elementIdTuple);
            $parentIds[$parentId] = $parentId;

            $dataPoolConfig = $this->dataPoolConfigService->getDataPoolConfigById($parentId);
            $element = Service::getElementById($dataPoolConfig->getElementType(), $elementId);
            if (empty($element)) {
                throw new \Exception("Element $elementId not found!");
            }

            $this->collectionService->removeItemsFromCollection($collection, $dataPoolConfig, [$element]);
        }

        return $parentIds;
    }
}
