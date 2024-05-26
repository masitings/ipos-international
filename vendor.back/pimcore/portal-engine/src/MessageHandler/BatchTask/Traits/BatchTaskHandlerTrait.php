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

namespace Pimcore\Bundle\PortalEngineBundle\MessageHandler\BatchTask\Traits;

use Pimcore\Bundle\PortalEngineBundle\Entity\BatchTask;
use Pimcore\Bundle\PortalEngineBundle\Model\DataObject\PortalUserInterface;
use Pimcore\Bundle\PortalEngineBundle\Service\BatchTask\BatchTaskService;
use Pimcore\Bundle\PortalEngineBundle\Service\PublicShare\PublicShareService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\Authentication\UserProvider;
use Pimcore\Bundle\PortalEngineBundle\Traits\LoggerAware;

trait BatchTaskHandlerTrait
{
    use LoggerAware;

    /**
     * @var BatchTaskService
     */
    protected $batchTaskService;

    /**
     * @var UserProvider
     */
    protected $userProvider;

    /**
     * @var PublicShareService
     */
    protected $publicShareService;

    /**
     * @param int $taskId
     *
     * @return [Batch]
     */
    public function getBatchTask(int $taskId): ?BatchTask
    {
        if (!$batchTask = $this->batchTaskService->getTaskById($taskId)) {
            $this->logger->notice(
                sprintf(
                    '[Task ID %s] Skip message handling as task is not available anymore',
                    $taskId
                )
            );

            return null;
        }

        return $batchTask;
    }

    public function getUserFromBatchTask(BatchTask $batchTask): ?PortalUserInterface
    {
        if (!$user = $this->userProvider->getById($batchTask->getUserId(), true)) {
            $this->logger->info(
                sprintf(
                    '[Task ID %s] Skip message handling as user %s is not available anymore',
                    $batchTask->getId(),
                    $batchTask->getUserId()
                )
            );

            return null;
        }

        return $user;
    }

    public function setupPublicShare(BatchTask $batchTask)
    {
        $this->publicShareService->setupByBatchTask($batchTask);
    }

    /**
     * @param BatchTaskService $batchTaskService
     * @required
     */
    public function setBatchTaskService(BatchTaskService $batchTaskService)
    {
        $this->batchTaskService = $batchTaskService;
    }

    /**
     * @param UserProvider $userProvider
     * @required
     */
    public function setUserProvider(UserProvider $userProvider): void
    {
        $this->userProvider = $userProvider;
    }

    /**
     * @param PublicShareService $publicShareService
     * @required
     */
    public function setPublicShareService(PublicShareService $publicShareService): void
    {
        $this->publicShareService = $publicShareService;
    }
}
