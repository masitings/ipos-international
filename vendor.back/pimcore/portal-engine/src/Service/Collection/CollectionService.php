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

use Pimcore\Bundle\PortalEngineBundle\Entity\Collection;
use Pimcore\Bundle\PortalEngineBundle\Entity\CollectionItem;
use Pimcore\Bundle\PortalEngineBundle\Entity\CollectionShare;
use Pimcore\Bundle\PortalEngineBundle\Enum\Collection\Permission;
use Pimcore\Bundle\PortalEngineBundle\Enum\Index\DatabaseConfig;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataPoolConfigInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\DataObject\PortalUserInterface;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\Entity\EntityManagerService;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\IndexQueueService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\SecurityService;
use Pimcore\Db;
use Pimcore\Model\DataObject\PortalUser;
use Pimcore\Model\Element\ElementInterface;
use Pimcore\Model\Element\Service;
use Pimcore\Model\Site;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class CollectionService
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\Collection
 */
class CollectionService
{
    /** @var EntityManagerService */
    protected $entityManagerService;
    /** @var SecurityService */
    protected $securityService;
    /** @var IndexQueueService */
    protected $indexQueueService;
    /** @var DataPoolConfigService */
    protected $dataPoolConfigService;
    /** @var AuthorizationCheckerInterface */
    protected $authorizationChecker;

    /**
     * CollectionService constructor.
     *
     * @param EntityManagerService $entityManagerService
     * @param SecurityService $securityService
     * @param IndexQueueService $indexQueueService
     * @param DataPoolConfigService $dataPoolConfigService
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(EntityManagerService $entityManagerService, SecurityService $securityService, IndexQueueService $indexQueueService, DataPoolConfigService $dataPoolConfigService, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->entityManagerService = $entityManagerService;
        $this->securityService = $securityService;
        $this->indexQueueService = $indexQueueService;
        $this->dataPoolConfigService = $dataPoolConfigService;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param int $id
     * @param bool $checkCurrentSite
     * @param bool $filterForCurrentUserPermission
     *
     * @return Collection|null
     *
     * @throws \Exception
     */
    public function getCollectionById(int $id, bool $checkCurrentSite = true, bool $filterForCurrentUserPermission = true)
    {
        /** @var Collection|null $collection */
        $collection = null;

        $params = [
            'id' => $id
        ];

        if ($filterForCurrentUserPermission) {
            /** @var int[] $collectionIds */
            $collectionIds = $this->getAllowedCollectionIds();

            if (!in_array($id, $collectionIds)) {
                return null;
            }
        }

        if ($checkCurrentSite) {
            $params['currentSiteId'] = Site::getCurrentSite()->getId();
        }

        $collection = $this->entityManagerService->getManager()->getRepository(Collection::class)->findOneBy($params);

        return $collection;
    }

    /**
     * @param null $permission get collections with a specific permission
     * @param bool $checkCurrentSite
     * @param bool $filterForCurrentUserPermission
     *
     * @return Collection[]
     *
     * @throws \Exception
     */
    public function getCollections($permission = null, bool $checkCurrentSite = true, bool $filterForCurrentUserPermission = true)
    {
        $params = [];

        if ($filterForCurrentUserPermission) {
            $params['id'] = $this->getAllowedCollectionIds($permission);
        }
        if ($checkCurrentSite) {
            $params['currentSiteId'] = Site::getCurrentSite()->getId();
        }

        return $this->entityManagerService->getManager()->getRepository(Collection::class)->findBy($params);
    }

    /**
     * @param bool $checkCurrentSite
     *
     * @return \Doctrine\ORM\QueryBuilder
     *
     * @throws \Exception
     */
    public function getCollectionsQuery(bool $checkCurrentSite = true, bool $filterForCurrentUserPermission = true)
    {
        $builder = $this->entityManagerService->getManager()->createQueryBuilder()
            ->select('c')
            ->from(Collection::class, 'c')
            ->orderBy('c.creationDate', 'DESC')
        ;

        if ($filterForCurrentUserPermission) {
            $builder
                ->andWhere('c.id IN (:ids)')
                ->setParameter('ids', $this->getAllowedCollectionIds())
            ;
        }

        if ($checkCurrentSite) {
            $builder
                ->andWhere('c.currentSiteId = :currentSiteId')
                ->setParameter('currentSiteId', Site::getCurrentSite()->getId())
            ;
        }

        return $builder;
    }

    /**
     * Get all access-able collectionIds from collectionShareTable for current user and userGroups
     *
     * @param string $permission get collections with a specific permission
     *
     * @return int[]
     *
     * @throws \Exception
     */
    public function getAllowedCollectionIds($permission = null)
    {
        /** @var PortalUserInterface $portalUser */
        $portalUser = $this->securityService->getPortalUser();
        /** @var int $userId */
        $userId = $portalUser->getId();
        /** @var int[] $userGroupIds */
        $userGroupIds = array_map(function ($group) {
            return $group->getId();
        }, $portalUser->getGroups());

        /** @var string $query */
        $query = sprintf('userId = %s', $userId);
        if (!empty($userGroupIds)) {
            $query = sprintf('%s OR userGroupId IN (%s)', $query, implode(',', $userGroupIds));
        }
        $query = sprintf('SELECT collectionId FROM %s WHERE (%s)', CollectionShare::TABLE, $query);

        if ($permission && Permission::isValid($permission)) {
            $query .= sprintf(' AND permission = %s', Db::get()->quote($permission));
        }

        /** @var int[] $collectionIds */
        $collectionIds = Db::get()->fetchCol($query);

        $ownCollectionsQuery = sprintf('SELECT DISTINCT id FROM %s WHERE userId = ?', Collection::TABLE);
        $collectionIds = array_merge($collectionIds, Db::get()->fetchCol($ownCollectionsQuery, $portalUser->getId()));

        return array_unique($collectionIds);
    }

    /**
     * Check if current user or one of his groups has edit permisson for given collection
     *
     * @param Collection $collection
     *
     * @return bool
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function isCollectionEditAllowed(Collection $collection)
    {
        if ($this->isCurrentUserCollectionOwner($collection)) {
            return true;
        }

        /** @var PortalUserInterface $portalUser */
        $portalUser = $this->securityService->getPortalUser();
        /** @var int $userId */
        $userId = $portalUser->getId();
        /** @var int[] $userGroupIds */
        $userGroupIds = array_map(function ($group) {
            return $group->getId();
        }, $portalUser->getGroups());

        /** @var string $query */
        $query = sprintf('userId = %s', $userId);
        if (!empty($userGroupIds)) {
            $query = sprintf('%s OR userGroupId IN (%s)', $query, implode(',', $userGroupIds));
        }
        $query = sprintf('SELECT permission FROM %s WHERE collectionId = %s AND (%s)', CollectionShare::TABLE, $collection->getId(), $query);

        /** @var string[] $permissions */
        $permissions = Db::get()->fetchCol($query);

        return in_array(Permission::EDIT, $permissions);
    }

    /**
     * Check if current user is owner/creator of given collection
     *
     * @param Collection $collection
     *
     * @return bool
     */
    public function isCurrentUserCollectionOwner(Collection $collection)
    {
        return $collection->getUserId() == $this->securityService->getPortalUser()->getId();
    }

    /**
     * @param string $name
     *
     * @return Collection
     *
     * @throws \Exception
     */
    public function create(string $name, int $siteId = null)
    {
        /** @var int $userId */
        $userId = $this->securityService->getPortalUser()->getId();
        /** @var Collection $collection */
        $collection = (new Collection())
            ->setUserId($userId)
            ->setCurrentSiteId($siteId ?? Site::getCurrentSite()->getId())
            ->setName($name)
            ->setCreationDate(time())
            ->setModificationDate(time());

        $this->entityManagerService->persist($collection);

        return $collection;
    }

    /**
     * @param Collection $collection
     * @param string $name
     *
     * @return Collection
     */
    public function rename(Collection $collection, string $name)
    {
        $collection
            ->setName($name)
            ->setModificationDate(time());

        $this->entityManagerService->persist($collection);

        return $collection;
    }

    /**
     * @param Collection $collection
     *
     * @return $this
     */
    public function delete(Collection $collection)
    {
        /** @var CollectionShare[] $collectionShares */
        $collectionShares = $this->entityManagerService->getManager()->getRepository(CollectionShare::class)->findBy([
            'collectionId' => $collection->getId(),
        ]);

        foreach ($collectionShares as $collectionShare) {
            $this->entityManagerService->remove($collectionShare, false);
        }

        /** @var ElementInterface[] $collectionItemElements */
        $collectionItemElements = [];
        /** @var CollectionItem[] $collectionItems */
        $collectionItems = $this->entityManagerService->getManager()->getRepository(CollectionItem::class)->findBy([
            'collection' => $collection,
        ]);

        foreach ($collectionItems as $collectionItem) {
            $collectionItemElements[] = $collectionItem->getElement();

            $this->entityManagerService->remove($collectionItem, false);
        }

        $this->entityManagerService->remove($collection, false);

        $this->flush();

        foreach ($collectionItemElements as $element) {
            $this->indexQueueService->updateIndexQueue($element, DatabaseConfig::QUEUE_TABLE_COLUMN_OPERATION_UPDATE, true);
        }

        return $this;
    }

    /**
     * @param Collection $collection
     * @param DataPoolConfigInterface $dataPoolConfig
     * @param ElementInterface[] $elements
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function addItemsToCollection(Collection $collection, DataPoolConfigInterface $dataPoolConfig, array $elements)
    {
        foreach ($elements as $element) {
            /** @var CollectionItem $collectionItem */
            $collectionItem = $this->getCollectionItem($collection, $dataPoolConfig, $element);

            if (!$collectionItem) {
                $collectionItem = (new CollectionItem())
                    ->setCollection($collection)
                    ->setDataPoolId($dataPoolConfig->getId())
                    ->setElementId($element->getId())
                    ->setElementType(Service::getElementType($element))
                    ->setElementSubType($element->getType());

                $this->entityManagerService->persist($collectionItem, false);
            }
        }

        $this->flush();

        foreach ($elements as $element) {
            $this
                ->indexQueueService
                ->updateIndexQueue($element, DatabaseConfig::QUEUE_TABLE_COLUMN_OPERATION_UPDATE, true);
        }

        return $this;
    }

    /**
     * @param Collection $collection
     * @param DataPoolConfigInterface $dataPoolConfig
     * @param ElementInterface[] $elements
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function removeItemsFromCollection(Collection $collection, DataPoolConfigInterface $dataPoolConfig, array $elements)
    {
        foreach ($elements as $element) {
            /** @var CollectionItem $collectionItem */
            $collectionItem = $this->getCollectionItem($collection, $dataPoolConfig, $element);

            if ($collectionItem) {
                $this->entityManagerService->remove($collectionItem, false);
            }
        }

        $this->flush();

        /** @var bool $performIndexRefreshBackup */
        $performIndexRefreshBackup = $this->indexQueueService->isPerformIndexRefresh();
        $this->indexQueueService->setPerformIndexRefresh(true);
        foreach ($elements as $element) {
            $this
                ->indexQueueService
                ->updateIndexQueue($element, DatabaseConfig::QUEUE_TABLE_COLUMN_OPERATION_UPDATE, true);
        }
        $this->indexQueueService->setPerformIndexRefresh($performIndexRefreshBackup);

        return $this;
    }

    /**
     * @param Collection $collection
     * @param DataPoolConfigInterface $dataPoolConfig
     * @param ElementInterface $element
     *
     * @return CollectionItem|null
     */
    public function getCollectionItem(Collection $collection, DataPoolConfigInterface $dataPoolConfig, ElementInterface $element)
    {
        return $this->entityManagerService->getManager()->getRepository(CollectionItem::class)->findOneBy([
            'collection' => $collection,
            'dataPoolId' => $dataPoolConfig->getId(),
            'elementId' => $element->getId(),
            'elementType' => Service::getElementType($element)
        ]);
    }

    /**
     * @param Collection $collection
     *
     * @return CollectionItem[]
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getCollectionItems(Collection $collection)
    {
        return $this->getCollectionItemsQueryBuilder($collection)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Collection $collection
     *
     * @return int
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCollectionItemsCount(Collection $collection): int
    {
        $result = $this->getCollectionItemsQueryBuilder($collection)
            ->select('count(collectionItem.elementId)')
            ->getQuery()->getSingleScalarResult();

        return intval($result);
    }

    /**
     * @param Collection $collection
     *
     * @return \Doctrine\ORM\QueryBuilder
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function getCollectionItemsQueryBuilder(Collection $collection)
    {
        return $this->entityManagerService->getManager()->createQueryBuilder()
            ->select('collectionItem')
            ->from(CollectionItem::class, 'collectionItem')
            ->where('collectionItem.collection = :collectionId')
            ->andWhere("collectionItem.elementType = 'asset' or (collectionItem.elementType = 'object' AND collectionItem.elementId IN (:objectIds))")
            ->setParameter('collectionId', $collection->getId())
            ->setParameter('objectIds', Db::get()->fetchCol("SELECT collection_item.elementId
                    FROM portal_engine_collection_item collection_item
                    INNER JOIN objects objects
                    WHERE collection_item.collectionId = :collectionId
                    AND collection_item.elementId = objects.o_id
                    AND collection_item.elementType = 'object'
                    AND objects.o_published = 1", ['collectionId' => $collection->getId()]));
    }

    /**
     * Get all collectionShares where userId is not like the current userId
     *
     * @param Collection $collection
     *
     * @return CollectionShare[]
     *
     * @throws \Exception
     */
    public function getCollectionShareList(Collection $collection)
    {
        /** @var int $userId */
        $userId = $this->securityService->getPortalUser()->getId();
        /** @var string $query */
        $query = sprintf('SELECT id FROM %s WHERE collectionId = %s AND (userId != %s OR userGroupId IS NOT NULL)',
            CollectionShare::TABLE,
            $collection->getId(),
            $userId
        );

        /** @var int[] $collectionShareIds */
        $collectionShareIds = Db::get()->fetchCol($query);

        return $this->entityManagerService->getManager()->getRepository(CollectionShare::class)->findBy([
            'id' => $collectionShareIds,
        ]);
    }

    /**
     * @param Collection $collection
     * @param int $userOrGroupId
     * @param string $userOrGroupType
     * @param string $permission
     * @param bool $save
     *
     * @return CollectionShare|null
     *
     * @throws \Exception
     */
    public function addCollectionSharing(Collection $collection, int $userOrGroupId, string $userOrGroupType, string $permission, $save = true)
    {
        if (!Permission::isValid($permission)) {
            throw new \Exception('permission not valid');
        }

        /** @var CollectionShare|null $collectionShare */
        $collectionShare = $this->getCollectionShare($collection, $userOrGroupId, $userOrGroupType);

        if (!$collectionShare) {
            $collectionShare = (new CollectionShare())
                ->setCollectionId($collection->getId())
                ->setPermission($permission)
                ->setCreationDate(time())
                ->setModificationDate(time());

            switch ($userOrGroupType) {
                case 'user':
                    $collectionShare->setUserId($userOrGroupId);
                    break;
                case 'group':
                    $collectionShare->setUserGroupId($userOrGroupId);
                    break;
                default:
                    throw new \Exception('userOrGroupType is not valid');
            }

            $this->entityManagerService->persist($collectionShare, $save);
        }

        return $collectionShare;
    }

    /**
     * @param CollectionShare $collectionShare
     * @param string $permission
     * @param bool $save
     *
     * @return CollectionShare|null
     *
     * @throws \Exception
     */
    public function updateCollectionSharing(CollectionShare $collectionShare, string $permission, $save = true)
    {
        if (!Permission::isValid($permission)) {
            throw new \Exception('permission not valid');
        }

        $collectionShare
            ->setPermission($permission)
            ->setModificationDate(time());

        $this->entityManagerService->persist($collectionShare, $save);

        return $collectionShare;
    }

    /**
     * @param CollectionShare $collectionShare
     * @param bool $save
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function deleteCollectionSharing(CollectionShare $collectionShare, $save = true)
    {
        $this->entityManagerService->remove($collectionShare, $save);

        return $this;
    }

    /**
     * @param Collection $collection
     * @param int $userOrGroupId
     * @param string $userOrGroupType
     *
     * @return CollectionShare|null
     *
     * @throws \Exception
     */
    public function getCollectionShare(Collection $collection, int $userOrGroupId, string $userOrGroupType)
    {
        /** @var array $criteria */
        $criteria = [];
        $criteria['collectionId'] = $collection->getId();

        switch ($userOrGroupType) {
            case 'user':
                $criteria['userId'] = $userOrGroupId;
                break;
            case 'group':
                $criteria['userGroupId'] = $userOrGroupId;
                break;
            default:
                throw new \Exception('userOrGroupType is not valid');
        }

        return $this->entityManagerService->getManager()->getRepository(CollectionShare::class)->findOneBy($criteria);
    }

    /**
     * @param Collection $collection
     *
     * @return CollectionShare|null
     *
     * @throws \Exception
     */
    public function getCollectionShareByCurrentUser(Collection $collection)
    {
        /** @var CollectionShare $collectionShare */
        $collectionShare = null;
        /** @var PortalUserInterface|PortalUser $portalUser */
        $portalUser = $this->securityService->getPortalUser();

        if (!$this->isCurrentUserCollectionOwner($collection)) {

            /** @var int[] $userGroupIds */
            $userGroupIds = array_map(function ($group) {
                return $group->getId();
            }, $portalUser->getGroups());

            /** @var string $query */
            $query = sprintf('userId = %s', $portalUser->getId());
            if (!empty($userGroupIds)) {
                $query = sprintf('%s OR userGroupId IN (%s)', $query, implode(',', $userGroupIds));
            }
            $query = sprintf('SELECT id FROM %s WHERE collectionId = %s AND (%s)', CollectionShare::TABLE, $collection->getId(), $query);

            /** @var int[] $collectionShareIds */
            $collectionShareIds = Db::get()->fetchCol($query);

            $collectionShare = $this->entityManagerService->getManager()->getRepository(CollectionShare::class)->findOneBy([
                'id' => $collectionShareIds,
            ]);
        }

        return $collectionShare;
    }

    /**
     * @return $this
     */
    public function flush()
    {
        $this->entityManagerService->flush();

        return $this;
    }

    /**
     * @param Collection $collection
     *
     * @return int[]
     *
     * @throws \Exception
     */
    public function getDataPoolConfigIdsByCollection(Collection $collection): array
    {
        $dataPoolIdQuery = $this->getCollectionItemsQueryBuilder($collection)
            ->select('collectionItem.dataPoolId')
            ->distinct(true)
            ->getQuery();
        $dataPoolIds = array_map(function ($item) {
            return $item['dataPoolId'];
        }, $dataPoolIdQuery->getArrayResult());

        if (empty($dataPoolIds)) {
            return [];
        }

        $query = sprintf(
            'SELECT id from documents where id in (%s) and published=1',
            implode(',', $dataPoolIds)
        );

        /** @var int[] $collectionShareIds */
        return Db::get()->fetchCol($query);
    }

    /**
     * @param Collection $collection
     *
     * @return DataPoolConfigInterface[]
     *
     * @throws \Exception
     */
    public function getDataPoolConfigsByCollection(Collection $collection, bool $respectLanguageVariants = true): array
    {
        $dataPoolIds = $this->getDataPoolConfigIdsByCollection($collection);

        $dataPoolConfigs = [];

        foreach ($dataPoolIds as $dataPoolId) {
            $dataPoolConfig = $this->dataPoolConfigService->getDataPoolConfigById($dataPoolId, $respectLanguageVariants);
            if ($dataPoolConfig && $this->authorizationChecker->isGranted(\Pimcore\Bundle\PortalEngineBundle\Enum\Permission::DATA_POOL_ACCESS, $dataPoolConfig->getId())) {
                $dataPoolConfigs[$dataPoolConfig->getId()] = $dataPoolConfig;
            }
        }

        return $dataPoolConfigs;
    }

    /**
     * @param Collection $collection
     *
     * @return int[]
     *
     * @throws \Exception
     */
    public function getItemIdsByDataPool(Collection $collection, DataPoolConfigInterface $dataPoolConfig): array
    {
        $query = sprintf(
            'SELECT distinct elementId FROM %s WHERE collectionId = ? and dataPoolId = ?',
            CollectionItem::TABLE
        );
        /** @var int[] $collectionShareIds */
        return Db::get()->fetchCol($query, [$collection->getId(), $dataPoolConfig->getId()]);
    }

    /**
     * @param PortalUserInterface $user
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function cleanupDeletedUser(PortalUserInterface $user)
    {
        $sql = sprintf('UPDATE %s SET userId = null WHERE userId = ?', Collection::TABLE);
        Db::get()->executeQuery($sql, [$user->getId()]);
    }

    /**
     * @param ElementInterface $element
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function cleanupDeletedElement(ElementInterface $element)
    {
        $sql = sprintf('DELETE FROM %s WHERE elementId = ? and elementType=?', CollectionItem::TABLE);
        Db::get()->executeQuery($sql, [$element->getId(), Service::getElementType($element)]);
    }
}
