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

namespace Pimcore\Bundle\PortalEngineBundle\Service\SavedSearch;

use Pimcore\Bundle\PortalEngineBundle\Entity\SavedSearch;
use Pimcore\Bundle\PortalEngineBundle\Entity\SavedSearchShare;
use Pimcore\Bundle\PortalEngineBundle\Model\DataObject\PortalUserInterface;
use Pimcore\Bundle\PortalEngineBundle\Service\Entity\EntityManagerService;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\IndexQueueService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\SecurityService;
use Pimcore\Db;
use Pimcore\Model\DataObject\PortalUser;
use Pimcore\Model\Site;

/**
 * Class SavedSearchService
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\SavedSearch
 */
class SavedSearchService
{
    /** @var EntityManagerService */
    protected $entityManagerService;
    /** @var SecurityService */
    protected $securityService;
    /** @var IndexQueueService */
    protected $indexQueueService;

    /**
     * SavedSearchService constructor.
     *
     * @param EntityManagerService $entityManagerService
     * @param SecurityService $securityService
     * @param IndexQueueService $indexQueueService
     */
    public function __construct(EntityManagerService $entityManagerService, SecurityService $securityService, IndexQueueService $indexQueueService)
    {
        $this->entityManagerService = $entityManagerService;
        $this->securityService = $securityService;
        $this->indexQueueService = $indexQueueService;
    }

    /**
     * @param int $id
     *
     * @return SavedSearch|null
     *
     * @throws \Exception
     */
    public function getSavedSearchById(int $id)
    {
        /** @var SavedSearch|null $savedSearch */
        $savedSearch = null;
        /** @var int[] $savedSearchIds */
        $savedSearchIds = $this->getSavedSearchIdsFromSavedSearchShareTable();

        if (in_array($id, $savedSearchIds)) {
            $savedSearch = $this->entityManagerService->getManager()->getRepository(SavedSearch::class)->findOneBy([
                'id' => $id,
                'currentSiteId' => Site::getCurrentSite()->getId(),
            ]);
        }

        return $savedSearch;
    }

    /**
     * @param string $name
     *
     * @return SavedSearch|null
     *
     * @throws \Exception
     */
    public function getSavedSearchByName(string $name)
    {
        return $this->entityManagerService->getManager()->getRepository(SavedSearch::class)->findOneBy([
            'id' => $this->getSavedSearchIdsFromSavedSearchShareTable(),
            'name' => $name,
            'currentSiteId' => Site::getCurrentSite()->getId()
        ]);
    }

    /**
     * @return SavedSearch[]
     *
     * @throws \Exception
     */
    public function getCurrentUserSavedSearches()
    {
        return $this->entityManagerService->getManager()->getRepository(SavedSearch::class)->findBy([
            'id' => $this->getSavedSearchIdsFromSavedSearchShareTable(),
            'currentSiteId' => Site::getCurrentSite()->getId()
        ]);
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     *
     * @throws \Exception
     */
    public function getSavedSearchQuery()
    {
        return $builder = $this->entityManagerService->getManager()->createQueryBuilder()
            ->select('s')
            ->from(SavedSearch::class, 's')
            ->where('s.id IN (:ids)')
            ->andWhere('s.currentSiteId = :currentSiteId')
            ->orderBy('s.creationDate', 'DESC')
            ->setParameter('ids', $this->getSavedSearchIdsFromSavedSearchShareTable())
            ->setParameter('currentSiteId', Site::getCurrentSite()->getId());
    }

    /**
     * Get all access-able savedSearchIds from savedSearchShareTable for current user and userGroups
     *
     * @return int[]
     *
     * @throws \Exception
     */
    public function getSavedSearchIdsFromSavedSearchShareTable()
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
        $query = sprintf('SELECT searchId FROM %s WHERE (%s)', SavedSearchShare::TABLE, $query);

        /** @var int[] $savedSearchIds */
        $savedSearchIds = Db::get()->fetchCol($query);

        return $savedSearchIds;
    }

    /**
     * @param SavedSearch $savedSearch
     *
     * @return bool
     */
    public function isCurrentUserSavedSearchOwner(SavedSearch $savedSearch)
    {
        return $savedSearch->getUserId() == $this->securityService->getPortalUser()->getId();
    }

    /**
     * @param string $name
     * @param string $urlQuery
     *
     * @return SavedSearch
     *
     * @throws \Exception
     */
    public function create(string $name, string $urlQuery)
    {
        /** @var int $userId */
        $userId = $this->securityService->getPortalUser()->getId();
        /** @var SavedSearch $savedSearch */
        $savedSearch = (new SavedSearch())
            ->setUserId($userId)
            ->setCurrentSiteId(Site::getCurrentSite()->getId())
            ->setName($name)
            ->setUrlQuery($urlQuery)
            ->setCreationDate(time())
            ->setModificationDate(time());

        $this->entityManagerService->persist($savedSearch);

        /** @var SavedSearchShare $savedSearchShare */
        $savedSearchShare = (new SavedSearchShare())
            ->setSearchId($savedSearch->getId())
            ->setUserId($userId)
            ->setCreationDate(time())
            ->setModificationDate(time());

        $this->entityManagerService->persist($savedSearchShare);

        return $savedSearch;
    }

    /**
     * @param SavedSearch $savedSearch
     * @param string $name
     *
     * @return SavedSearch
     */
    public function rename(SavedSearch $savedSearch, string $name)
    {
        $savedSearch
            ->setName($name)
            ->setModificationDate(time());

        $this->entityManagerService->persist($savedSearch);

        return $savedSearch;
    }

    /**
     * @param SavedSearch $savedSearch
     *
     * @return $this
     */
    public function delete(SavedSearch $savedSearch)
    {
        /** @var SavedSearchShare[] $savedSearchShares */
        $savedSearchShares = $this->entityManagerService->getManager()->getRepository(SavedSearchShare::class)->findBy([
            'searchId' => $savedSearch->getId(),
        ]);

        foreach ($savedSearchShares as $savedSearchShare) {
            $this->entityManagerService->remove($savedSearchShare, false);
        }

        $this->entityManagerService->remove($savedSearch, false);

        $this->flush();

        return $this;
    }

    /**
     * Get all savedSearchShares where userId is not like the current userId
     *
     * @param SavedSearch $savedSearch
     *
     * @return SavedSearchShare[]
     *
     * @throws \Exception
     */
    public function getSavedSearchShareList(SavedSearch $savedSearch)
    {
        /** @var int $userId */
        $userId = $this->securityService->getPortalUser()->getId();
        /** @var string $query */
        $query = sprintf('SELECT id FROM %s WHERE searchId = %s AND (userId != %s OR userGroupId IS NOT NULL)',
            SavedSearchShare::TABLE,
            $savedSearch->getId(),
            $userId
        );
        /** @var int[] $savedSearchShareIds */
        $savedSearchShareIds = Db::get()->fetchCol($query);

        return $this->entityManagerService->getManager()->getRepository(SavedSearchShare::class)->findBy([
            'id' => $savedSearchShareIds,
        ]);
    }

    /**
     * @param SavedSearch $savedSearch
     * @param int $userOrGroupId
     * @param string $userOrGroupType
     * @param bool $save
     *
     * @return SavedSearchShare|null
     *
     * @throws \Exception
     */
    public function addSavedSearchSharing(SavedSearch $savedSearch, int $userOrGroupId, string $userOrGroupType, $save = true)
    {
        /** @var SavedSearchShare|null $savedSearchShare */
        $savedSearchShare = $this->getSavedSearchShare($savedSearch, $userOrGroupId, $userOrGroupType);

        if (!$savedSearchShare) {
            /** @var int $userId */
            $userId = $this->securityService->getPortalUser()->getId();

            $savedSearchShare = (new SavedSearchShare())
                ->setSearchId($savedSearch->getId())
                ->setCreationDate(time())
                ->setModificationDate(time());

            switch ($userOrGroupType) {
                case 'user':
                    $savedSearchShare->setUserId($userOrGroupId);
                    break;
                case 'group':
                    $savedSearchShare->setUserGroupId($userOrGroupId);
                    break;
                default:
                    throw new \Exception('userOrGroupType is not valid');
            }

            $this->entityManagerService->persist($savedSearchShare, $save);
        }

        return $savedSearchShare;
    }

    /**
     * @param SavedSearchShare $savedSearchShare
     * @param bool $save
     *
     * @return $this
     */
    public function deleteSavedSearchSharing(SavedSearchShare $savedSearchShare, $save = true)
    {
        $this->entityManagerService->remove($savedSearchShare, $save);

        return $this;
    }

    /**
     * @param SavedSearch $savedSearch
     * @param int $userOrGroupId
     * @param string $userOrGroupType
     *
     * @return SavedSearchShare|null
     *
     * @throws \Exception
     */
    public function getSavedSearchShare(SavedSearch $savedSearch, int $userOrGroupId, string $userOrGroupType)
    {
        /** @var array $criteria */
        $criteria = [];
        $criteria['searchId'] = $savedSearch->getId();

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

        return $this->entityManagerService->getManager()->getRepository(SavedSearchShare::class)->findOneBy($criteria);
    }

    /**
     * @param SavedSearch $savedSearch
     *
     * @return SavedSearchShare|null
     *
     * @throws \Exception
     */
    public function getSavedSearchShareByCurrentUser(SavedSearch $savedSearch)
    {
        /** @var SavedSearchShare $savedSearchShare */
        $savedSearchShare = null;
        /** @var PortalUserInterface|PortalUser $portalUser */
        $portalUser = $this->securityService->getPortalUser();

        if (!$this->isCurrentUserSavedSearchOwner($savedSearch)) {

            /** @var int[] $userGroupIds */
            $userGroupIds = array_map(function ($group) {
                return $group->getId();
            }, $portalUser->getGroups());

            /** @var string $query */
            $query = sprintf('userId = %s', $portalUser->getId());
            if (!empty($userGroupIds)) {
                $query = sprintf('%s OR userGroupId IN (%s)', $query, implode(',', $userGroupIds));
            }
            $query = sprintf('SELECT id FROM %s WHERE searchId = %s AND (%s)', SavedSearchShare::TABLE, $savedSearch->getId(), $query);

            /** @var int[] $savedSearchShareIds */
            $savedSearchShareIds = Db::get()->fetchCol($query);

            $savedSearchShare = $this->entityManagerService->getManager()->getRepository(SavedSearchShare::class)->findOneBy([
                'id' => $savedSearchShareIds,
            ]);
        }

        return $savedSearchShare;
    }

    /**
     * @return $this
     */
    public function flush()
    {
        $this->entityManagerService->flush();

        return $this;
    }
}
