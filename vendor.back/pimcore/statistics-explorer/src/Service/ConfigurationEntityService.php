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

namespace Pimcore\Bundle\StatisticsExplorerBundle\Service;

use Doctrine\ORM\Query\ResultSetMapping;
use Pimcore\Bundle\StatisticsExplorerBundle\Entity\Configuration;
use Pimcore\Bundle\StatisticsExplorerBundle\Entity\ConfigurationShare;

class ConfigurationEntityService
{
    /**
     * @var EntityManagerService
     */
    protected $entityManagerService;

    public function __construct(EntityManagerService $entityManagerService)
    {
        $this->entityManagerService = $entityManagerService;
    }

    public function getById(string $configurationId): ?Configuration
    {
        return $this->entityManagerService->getManager()->getRepository(Configuration::class)
            ->findOneBy(['id' => $configurationId]);
    }

    public function getByIdOwnerAware(string $configurationId, int $ownerId): ?Configuration
    {
        return $this->entityManagerService->getManager()->getRepository(Configuration::class)
            ->findOneBy(['id' => $configurationId, 'ownerId' => $ownerId]);
    }

    public function getByIdPermissionAware(string $configurationId, string $context, array $permissionCondition): ?Configuration
    {
        $results = $this->getSharedWith($context, $permissionCondition, $configurationId);
        if ($results) {
            return reset($results);
        }

        return null;
    }

    public function getByOwnerId(string $context, ?int $ownerId = null): array
    {
        $condition = ['context' => $context];
        if ($ownerId) {
            $condition['ownerId'] = $ownerId;
        } else {
            $condition['ownerId'] = null;
        }

        return $this->entityManagerService->getManager()->getRepository(Configuration::class)
            ->findBy($condition);
    }

    public function getSharedWith(string $context, array $conditions, ?string $configurationId = null): array
    {
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult(Configuration::class, 'c');
        $rsm->addFieldResult('c', 'id', 'id');
        $rsm->addFieldResult('c', 'context', 'context');
        $rsm->addFieldResult('c', 'ownerId', 'ownerId');
        $rsm->addFieldResult('c', 'name', 'name');
        $rsm->addFieldResult('c', 'modificationDate', 'modificationDate');
        $rsm->addFieldResult('c', 'configuration', 'configuration');

        $whereParts = [];
        $parameters = [];

        $where = 'context = ?';
        $parameters[] = $context;

        if ($conditions) {
            foreach ($conditions as $condition) {
                $whereParts[] = '(s.sharedWithType = ? AND s.sharedWithId = ?)';
                $parameters[] = $condition[0];
                $parameters[] = $condition[1];
            }

            $where .= ' AND (' . implode(' OR ', $whereParts) . ')';
        }

        if ($configurationId) {
            $where .= ' AND c.id = ?';
            $parameters[] = $configurationId;
        }

        $query = $this->entityManagerService->getManager()->createNativeQuery(
            'SELECT * FROM ' . Configuration::TABLE . ' c ' .
            ' INNER JOIN ' . ConfigurationShare::TABLE . ' s ON c.id = s.configurationId' .
            ' WHERE ' . $where,
            $rsm
        );

        $query->setParameters($parameters);

//        p_r($query->getResult());

        return $query->getResult();
    }

    public function getShare(Configuration $configuration, int $sharedWithId, string $sharedWithType): ?ConfigurationShare
    {
        return $this->entityManagerService->getManager()->getRepository(ConfigurationShare::class)
            ->findOneBy(['configuration' => $configuration, 'sharedWithId' => $sharedWithId, 'sharedWithType' => $sharedWithType]);
    }

    /**
     * @param Configuration $configuration
     *
     * @return ConfigurationShare[]
     */
    public function getShares(Configuration $configuration): array
    {
        return $this->entityManagerService->getManager()->getRepository(ConfigurationShare::class)
            ->findBy(['configuration' => $configuration]);
    }

    public function cleanupShares(Configuration $configuration)
    {
        $query = $this->entityManagerService->getManager()->createQueryBuilder();
        $query->delete(ConfigurationShare::class, 'c');
        $query->where('c.configuration = :configurationId');
        $query->setParameter('configurationId', $configuration->getId());
        $query->getQuery()->execute();
    }

    public function persist($configuration)
    {
        $this->entityManagerService->persist($configuration);
    }

    public function delete($configuration)
    {
        $this->entityManagerService->remove($configuration);
    }
}
