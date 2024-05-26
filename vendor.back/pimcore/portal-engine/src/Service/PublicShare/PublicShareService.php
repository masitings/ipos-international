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

namespace Pimcore\Bundle\PortalEngineBundle\Service\PublicShare;

use Pimcore\Bundle\PortalEngineBundle\Entity\BatchTask;
use Pimcore\Bundle\PortalEngineBundle\Entity\Collection;
use Pimcore\Bundle\PortalEngineBundle\Entity\PublicShare;
use Pimcore\Bundle\PortalEngineBundle\Entity\PublicShareItem;
use Pimcore\Bundle\PortalEngineBundle\Enum\Index\DatabaseConfig;
use Pimcore\Bundle\PortalEngineBundle\Exception\PublicShareExpiredException;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataPoolConfigInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\DataObject\PortalUserInterface;
use Pimcore\Bundle\PortalEngineBundle\Service\Collection\CollectionService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\Entity\EntityManagerService;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\IndexQueueService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\SecurityService;
use Pimcore\Db;
use Pimcore\Model\DataObject\PortalUser;
use Pimcore\Model\Element\ElementInterface;
use Pimcore\Model\Element\Service;
use Pimcore\Model\Site;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class PublicShareService
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\PublicShare
 */
class PublicShareService
{
    /** @var EntityManagerService */
    protected $entityManagerService;
    /** @var CollectionService */
    protected $collectionService;
    /** @var SecurityService */
    protected $securityService;
    /** @var IndexQueueService */
    protected $indexQueueService;
    /** @var DataPoolConfigService */
    protected $dataPoolConfigService;
    /** @var PublicShare|null */
    protected $currentPublicShare;
    /** @var SessionInterface */
    protected $session;

    /**
     * PublicShareService constructor.
     *
     * @param EntityManagerService $entityManagerService
     * @param CollectionService $collectionService
     * @param SecurityService $securityService
     * @param IndexQueueService $indexQueueService
     * @param DataPoolConfigService $dataPoolConfigService
     * @param SessionInterface $session
     */
    public function __construct(EntityManagerService $entityManagerService, CollectionService $collectionService, SecurityService $securityService, IndexQueueService $indexQueueService, DataPoolConfigService $dataPoolConfigService, SessionInterface $session)
    {
        $this->entityManagerService = $entityManagerService;
        $this->collectionService = $collectionService;
        $this->securityService = $securityService;
        $this->indexQueueService = $indexQueueService;
        $this->dataPoolConfigService = $dataPoolConfigService;
        $this->session = $session;
    }

    /**
     * @param string $name
     * @param array $downloadConfigs
     * @param null $expiryDate
     * @param bool $showTermsText
     * @param null $termsText
     * @param Collection|null $collection
     *
     * @return PublicShare
     *
     * @throws \Exception
     */
    public function create(string $name, array $downloadConfigs, $expiryDate = null, $showTermsText = false, $termsText = null, $collection = null)
    {
        /** @var PublicShare $publicShare */
        $publicShare = (new PublicShare())
            ->setCollectionId($collection ? $collection->getId() : null)
            ->setHash($this->getUniqueHash())
            ->setName($name)
            ->setUserId($this->securityService->getPortalUser()->getId())
            ->setCurrentSiteId(Site::getCurrentSite()->getId())
            ->setExpiryDate($expiryDate)
            ->setShowTermsText($showTermsText)
            ->setTermsText($termsText)
            ->setConfigs($downloadConfigs)
            ->setCreationDate(time())
            ->setModificationDate(time());

        $this->entityManagerService->persist($publicShare);

        return $publicShare;
    }

    /**
     * @param PublicShare $publicShare
     *
     * @return $this
     */
    public function delete(PublicShare $publicShare)
    {
        /** @var ElementInterface[] $publicShareItemElements */
        $publicShareItemElements = [];
        /** @var PublicShareItem[] $publicShareItems */
        $publicShareItems = $this->entityManagerService->getManager()->getRepository(PublicShareItem::class)->findBy([
            'publicShare' => $publicShare
        ]);

        foreach ($publicShareItems as $publicShareItem) {
            $publicShareItemElements[] = $publicShareItem->getElement();

            $this->entityManagerService->remove($publicShareItem, false);
        }

        $this->entityManagerService->remove($publicShare, false);

        $this->entityManagerService->flush();

        foreach ($publicShareItemElements as $element) {
            $this->indexQueueService->updateIndexQueue($element, DatabaseConfig::QUEUE_TABLE_COLUMN_OPERATION_UPDATE, true);
        }

        return $this;
    }

    /**
     * @param PortalUserInterface $user
     *
     * @return $this
     */
    public function cleanupDeletedUser(PortalUserInterface $user)
    {
        /** @var PublicShare[] $publicShares */
        $publicShares = $this->entityManagerService->getManager()->getRepository(PublicShare::class)->findBy([
            'userId' => $user->getId()
        ]);

        foreach ($publicShares as $publicShare) {
            $this->delete($publicShare);
        }

        return $this;
    }

    /**
     * @param Collection $collection
     *
     * @return $this
     */
    public function cleanupDeletedCollection(Collection $collection)
    {
        /** @var PublicShare[] $publicShares */
        $publicShares = $this->entityManagerService->getManager()->getRepository(PublicShare::class)->findBy([
            'collectionId' => $collection->getId()
        ]);

        foreach ($publicShares as $publicShare) {
            $this->delete($publicShare);
        }

        return $this;
    }

    /**
     * @param PublicShare $publicShare
     * @param DataPoolConfigInterface $dataPoolConfig
     * @param array $elements
     *
     * @return $this
     */
    public function addItemsToPublicShare(PublicShare $publicShare, DataPoolConfigInterface $dataPoolConfig, array $elements)
    {
        foreach ($elements as $element) {
            /** @var PublicShareItem $publicShareItem */
            $publicShareItem = $this->getPublicShareItem($publicShare, $dataPoolConfig, $element);

            if (!$publicShareItem) {
                /** @var PublicShareItem $publicShareItem */
                $publicShareItem = (new PublicShareItem())
                    ->setPublicShare($publicShare)
                    ->setElementId($element->getId())
                    ->setElementType(Service::getElementType($element))
                    ->setElementSubType($element->getType())
                    ->setDataPoolId($dataPoolConfig->getId());

                $this->entityManagerService->persist($publicShareItem, false);
            }
        }

        $this->entityManagerService->flush();

        foreach ($elements as $element) {
            $this
                ->indexQueueService
                ->updateIndexQueue($element, DatabaseConfig::QUEUE_TABLE_COLUMN_OPERATION_UPDATE, true);
        }

        return $this;
    }

    /**
     * @param PublicShare $publicShare
     * @param string $name
     * @param array $downloadConfigs
     * @param null $expiryDate
     * @param bool $showTermsText
     * @param null $termsText
     *
     * @return PublicShare
     */
    public function updatePublicShare(PublicShare $publicShare, string $name, array $downloadConfigs, $expiryDate = null, $showTermsText = false, $termsText = null)
    {
        $publicShare
            ->setName($name)
            ->setConfigs($downloadConfigs)
            ->setExpiryDate($expiryDate)
            ->setShowTermsText($showTermsText)
            ->setTermsText($termsText)
            ->setModificationDate(time());

        $this->entityManagerService->persist($publicShare);

        return $publicShare;
    }

    /**
     * @param PublicShare $publicShare
     *
     * @return bool
     */
    public function isCurrentUserPublicShareOwner(PublicShare $publicShare)
    {
        return $publicShare->getUserId() == $this->securityService->getPortalUser()->getId();
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     *
     * @throws \Exception
     */
    public function getPublicShareQuery()
    {
        return $builder = $this->entityManagerService->getManager()->createQueryBuilder()
            ->select('p')
            ->from(PublicShare::class, 'p')
            ->where('p.userId = :userId')
            ->andWhere('p.currentSiteId = :currentSiteId')
            ->orderBy('p.creationDate', 'DESC')
            ->setParameter('userId', $this->securityService->getPortalUser()->getId())
            ->setParameter('currentSiteId', Site::getCurrentSite()->getId());
    }

    /**
     * @param PublicShare $publicShare
     *
     * @return PublicShareItem[]
     *
     * @throws \Exception
     */
    public function getPublicShareItems(PublicShare $publicShare)
    {
        /** @var PublicShareItem[] $publicShareItems */
        $publicShareItems = [];

        if ($publicShare->getCollectionId() && $collection = $this->collectionService->getCollectionById($publicShare->getCollectionId(), true, false)) {
            foreach ($this->collectionService->getCollectionItems($collection) as $collectionItem) {
                $publicShareItems[] = (new PublicShareItem())
                    ->setPublicShare($publicShare)
                    ->setDataPoolId($collectionItem->getDataPoolId())
                    ->setElementId($collectionItem->getElementId())
                    ->setElementType($collectionItem->getElementType())
                    ->setElementSubType($collectionItem->getElementSubType());
            }
        } else {
            $publicShareItems = $this->getPublicShareQueryBuilder($publicShare)
                ->getQuery()
                ->getResult();
        }

        return $publicShareItems;
    }

    /**
     * @param PublicShare $publicShare
     * @param DataPoolConfigInterface $dataPoolConfig
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getItemIdsByDataPool(PublicShare $publicShare, DataPoolConfigInterface $dataPoolConfig): array
    {
        if ($publicShare->getCollectionId() && $collection = $this->collectionService->getCollectionById($publicShare->getCollectionId(), true, false)) {
            return $this->collectionService->getItemIdsByDataPool($collection, $dataPoolConfig);
        } else {
            $query = sprintf(
                'SELECT distinct elementId FROM %s WHERE publicShareId = ? and dataPoolId = ?',
                PublicShareItem::TABLE
            );
            /** @var int[] $collectionShareIds */
            return Db::get()->fetchCol($query, [$publicShare->getId(), $dataPoolConfig->getId()]);
        }
    }

    /**
     * @param PublicShare $publicShare
     * @param DataPoolConfigInterface $dataPoolConfig
     * @param ElementInterface $element
     *
     * @return object|null
     */
    public function getPublicShareItem(PublicShare $publicShare, DataPoolConfigInterface $dataPoolConfig, ElementInterface $element)
    {
        return $this->entityManagerService->getManager()->getRepository(PublicShareItem::class)->findOneBy([
            'publicShare' => $publicShare,
            'dataPoolId' => $dataPoolConfig->getId(),
            'elementId' => $element->getId(),
            'elementType' => Service::getElementType($element)
        ]);
    }

    /**
     * @param int $id
     *
     * @return PublicShare|null
     */
    public function getById(int $id)
    {
        return $this->entityManagerService->getManager()->getRepository(PublicShare::class)->findOneBy([
            'id' => $id
        ]);
    }

    /**
     * @param string $name
     *
     * @return PublicShare|null
     */
    public function getByName(string $name)
    {
        return $this->entityManagerService->getManager()->getRepository(PublicShare::class)->findOneBy([
            'name' => $name,
            'userId' => $this->securityService->getPortalUser()->getId()
        ]);
    }

    /**
     * @param string $hash
     *
     * @return PublicShare|null
     */
    public function getByHash(string $hash = null)
    {
        /** @var PublicShare|null $publicShare */
        $publicShare = null;

        if (32 === strlen($hash)) {
            $publicShare = $this->entityManagerService->getManager()->getRepository(PublicShare::class)->findOneBy([
                'hash' => $hash
            ]);
        }

        return $publicShare;
    }

    /**
     * @param PublicShare $publicShare
     *
     * @return int[]
     *
     * @throws \Exception
     */
    public function getDataPoolConfigIdsByPublicShare(PublicShare $publicShare): array
    {
        if ($publicShare->getCollectionId() && $collection = $this->collectionService->getCollectionById($publicShare->getCollectionId(), true, false)) {

            /** @var int[] $dataPoolConfigsIds */
            $dataPoolConfigsIds = $this->collectionService->getDataPoolConfigIdsByCollection($collection);
        } else {
            $query = sprintf(
                'SELECT distinct dataPoolId FROM %s WHERE publicShareId = ?',
                PublicShareItem::TABLE
            );
            /** @var int[] $dataPoolConfigsIds */
            $dataPoolConfigsIds = Db::get()->fetchCol($query, $publicShare->getId());
        }

        return $dataPoolConfigsIds;
    }

    /**
     * @param PublicShare $publicShare
     *
     * @return DataPoolConfigInterface[]
     *
     * @throws \Exception
     */
    public function getDataPoolConfigsByPublicShare(PublicShare $publicShare): array
    {
        /** @var int[] $dataPoolIds */
        $dataPoolIds = $this->getDataPoolConfigIdsByPublicShare($publicShare);
        /** @var DataPoolConfigInterface[] $dataPoolConfigs */
        $dataPoolConfigs = [];

        foreach ($dataPoolIds as $dataPoolId) {
            $dataPoolConfig = $this->dataPoolConfigService->getDataPoolConfigById($dataPoolId);
            if ($dataPoolConfig) {
                $dataPoolConfigs[$dataPoolConfig->getId()] = $dataPoolConfig;
            }
        }

        return $dataPoolConfigs;
    }

    /**
     * @param string|null $hash
     *
     * @return PublicShare
     *
     * @throws NotFoundHttpException
     */
    public function validateByHash(?string $hash)
    {
        /** @var PublicShare $publicShare */
        $publicShare = $this->getByHash($hash);
        if (!$publicShare) {
            throw new NotFoundHttpException('PublicShare not found.');
        }
        if ($publicShare->isExpired()) {
            $exception = new PublicShareExpiredException('PublicShare is expired.');
            $exception->setPublicShare($publicShare);
            throw $exception;
        }

        return $publicShare;
    }

    public function setUpPublicShare(PublicShare $publicShare)
    {
        if (!$this->session->isStarted()) {
            $this->session->start();
        }

        $portalUser = $this->createPublicShareUserInstance($publicShare->getHash() . '_' . $this->session->getId());
        //dummy session value as otherwise the session does not get persisted
        $this->session->set('pimcorePortalEngineUserRequested', true);
        $this->securityService->setPortalUser($portalUser);
        $this->setCurrentPublicShare($publicShare);
    }

    public function resetPublicShare()
    {
        if (!$this->session->isStarted()) {
            $this->session->start();
        }
        $this->session->set('pimcorePortalEngineUserRequested', false);
        $this->securityService->setPortalUser(null);
        $this->setCurrentPublicShare(null);
    }

    public function setupByBatchTask(BatchTask $batchTask)
    {
        $payload = $batchTask->getPayload();
        $hash = $payload[\Pimcore\Bundle\PortalEngineBundle\Enum\BatchTask\Payload\PublicShare::PUBLIC_SHARE_HASH] ?? null;
        if ($hash && $publicShare = $this->getByHash($hash)) {
            $this->setUpPublicShare($publicShare);
        } else {
            $this->resetPublicShare();
        }
    }

    public function createPublicShareUserInstance(string $userId): PortalUserInterface
    {
        return (new PortalUser())
            ->setPublicShareUserId($userId)
            ->setPortalShareUser(true);
    }

    /**
     * @param PublicShare $publicShare
     * @param ElementInterface $element
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function isElementInPublicShare(PublicShare $publicShare, ElementInterface $element)
    {
        /** @var string $elementType */
        $elementType = Service::getElementType($element);

        if ($publicShare->getCollectionId() && $collection = $this->collectionService->getCollectionById($publicShare->getCollectionId(), false, false)) {
            $isElementInPublicShare = (bool)Db::get()->fetchOne('SELECT COUNT(*)
                FROM portal_engine_collection_item
                WHERE collectionId = :collectionId
                AND elementId = :elementId
                AND elementType = :elementType', [
                'collectionId' => $collection->getId(),
                'elementId' => $element->getId(),
                'elementType' => $elementType
            ]);
        } else {
            $isElementInPublicShare = (bool)Db::get()->fetchOne('SELECT COUNT(*)
                FROM portal_engine_public_share_item
                WHERE publicShareId = :publicShareId
                AND elementId = :elementId
                AND elementType = :elementType', [
                'publicShareId' => $publicShare->getId(),
                'elementId' => $element->getId(),
                'elementType' => $elementType
            ]);
        }

        return $isElementInPublicShare;
    }

    /**
     * @return PublicShare|null
     */
    public function getCurrentPublicShare(): ?PublicShare
    {
        return $this->currentPublicShare;
    }

    /**
     * @param PublicShare|null $currentPublicShare
     *
     * @return PublicShareService
     */
    public function setCurrentPublicShare(?PublicShare $currentPublicShare): self
    {
        $this->currentPublicShare = $currentPublicShare;

        return $this;
    }

    public function getCurrentPublicShareHash(): ?string
    {
        return $this->getCurrentPublicShare() ? $this->getCurrentPublicShare()->getHash() : null;
    }

    /**
     * @param PublicShare $publicShare
     *
     * @return \Doctrine\ORM\QueryBuilder
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function getPublicShareQueryBuilder(PublicShare $publicShare)
    {
        return $this->entityManagerService->getManager()->createQueryBuilder()
            ->select('publicShareItem')
            ->from(PublicShareItem::class, 'publicShareItem')
            ->where('publicShareItem.publicShare = :publicShareId')
            ->andWhere("publicShareItem.elementType = 'asset' OR (publicShareItem.elementType = 'object' AND publicShareItem.elementId IN (:elementIds))")
            ->setParameter('publicShareId', $publicShare->getId())
            ->setParameter('elementIds', Db::get()->fetchCol("SELECT public_share_item.elementId
                    FROM portal_engine_public_share_item public_share_item
                    INNER JOIN objects objects
                    WHERE public_share_item.publicShareId = :publicShareId
                    AND public_share_item.elementId = objects.o_id
                    AND public_share_item.elementType = 'object'
                    AND objects.o_published = 1", ['publicShareId' => $publicShare->getId()]));
    }

    /**
     * @return string
     */
    protected function getUniqueHash()
    {
        /** @var string $uniqueId */
        $uniqueId = md5(uniqid());

        while ($this->getByHash($uniqueId)) {
            $uniqueId = $this->getUniqueHash();
        }

        return $uniqueId;
    }
}
