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

namespace Pimcore\Bundle\PortalEngineBundle\Service\BatchTask;

use Pimcore\Bundle\PortalEngineBundle\Entity\BatchTask;
use Pimcore\Bundle\PortalEngineBundle\Entity\BatchTaskProcessedItem;
use Pimcore\Bundle\PortalEngineBundle\Enum\BatchTask\State;
use Pimcore\Bundle\PortalEngineBundle\Service\BatchTask\ProcessNotificationActionHandler\ProcessNotificationActionInterface;
use Pimcore\Bundle\PortalEngineBundle\Service\Entity\EntityManagerService;
use Pimcore\Bundle\PortalEngineBundle\Traits\LoggerAware;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BatchTaskService
{
    use LoggerAware;

    /**
     * @var EntityManagerService
     */
    protected $entityManagerService;

    /**
     * @var int
     */
    protected $cleanupUncompletedTasksAfterHours;

    /**
     * @var int
     */
    protected $cleanupFinishedTasksAfterHours;

    /**
     * @var ProcessNotificationActionInterface[]
     */
    protected $processNotificationActionHandler = [];

    /**
     * @var BatchTask|null
     */
    protected $terminateBatchTask;

    /**
     * @param EntityManagerService $entityManagerService
     */
    public function __construct(EntityManagerService $entityManagerService, int $cleanupUncompletedTasksAfterHours, int $cleanupFinishedTasksAfterHours)
    {
        $this->entityManagerService = $entityManagerService;
        $this->cleanupUncompletedTasksAfterHours = $cleanupUncompletedTasksAfterHours;
        $this->cleanupFinishedTasksAfterHours = $cleanupFinishedTasksAfterHours;
    }

    public function saveBatchTask(BatchTask $batchTask)
    {
        $this->entityManagerService->persist($batchTask);
    }

    public function prepareBatchTask(string $userId, string $type, int $totalItems, array $payload = []): BatchTask
    {
        $batchTask = (new BatchTask())
            ->setUserId($userId)
            ->setType($type)
            ->setState(State::PREPARING)
            ->setTotalItems($totalItems)
            ->setPayload($payload);

        $this->entityManagerService->persist($batchTask);

        return $batchTask;
    }

    public function startBatchTask(BatchTask $batchTask)
    {
        $batchTask->setState(State::RUNNING);
        $this->entityManagerService->persist($batchTask);
    }

    public function checkBatchTaskFinished(BatchTask $batchTask): bool
    {
        if ($batchTask->getState() === State::FINISHED) {
            return true;
        }

        $finishedItemsCount = $this->getFinishedItemsCount($batchTask);
        if ($finishedItemsCount == $batchTask->getTotalItems()) {
            $this->entityManagerService->getManager()->transactional(function () use ($batchTask) {
                // reduce table size - we do not need this information anymore if all items are processed
                $this->deleteProcecssedItems($batchTask);
                $batchTask->setState(State::FINISHED);
                $this->entityManagerService->persist($batchTask);
            });

            return true;
        }

        return false;
    }

    /**
     * @param BatchTask $batchTask
     *
     * @return int
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getFinishedItemsCount(BatchTask $batchTask): int
    {
        if ($batchTask->getState() === State::FINISHED) {
            return $batchTask->getTotalItems();
        }

        return intval(
            $this->entityManagerService->getManager()->getConnection()->fetchOne(
                'select count(*) from '.BatchTaskProcessedItem::TABLE.' where taskId=?',
                [$batchTask->getId()]
            )
        );
    }

    public function getTaskById(int $id): ?BatchTask
    {
        /**
         * @var BatchTask|null $result
         */
        $result = $this->entityManagerService->getManager()->getRepository(BatchTask::class)->find($id);

        return $result;
    }

    public function deleteBatchTask(BatchTask $batchTask)
    {
        $this->entityManagerService->remove($batchTask);
    }

    /**
     * @param BatchTask $batchTask
     * @param int $itemIndex
     *
     * @return bool
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function isItemIndexProcessed(BatchTask $batchTask, int $itemIndex): bool
    {
        $batchTaskId = $this->entityManagerService->getManager()->getConnection()->fetchOne(
            'select id from '.BatchTask::TABLE.' where id=?',
            [$batchTask->getId()]
        );

        // do not process any item anymore if task was deleted/stopped
        if (empty($batchTaskId)) {
            $this->logger->debug(
                sprintf('[Task ID %s] skip item index %s as task was already deleted/stopped', $batchTask->getId(), $itemIndex)
            );

            return true;
        }

        $checkedItemIndex = $this->entityManagerService->getManager()->getConnection()->fetchOne(
            'select itemIndex from '.BatchTaskProcessedItem::TABLE.' where taskId=? and itemIndex = ?',
            [$batchTask->getId(), $itemIndex]
        );

        return $checkedItemIndex !== false;
    }

    public function markItemIndexAsProcessed(BatchTask $batchTask, int $itemIndex)
    {
        $processedItem = new BatchTaskProcessedItem($batchTask->getId(), $itemIndex);
        $this->entityManagerService->persist($processedItem);
    }

    public function createParallelChunks(array $items, int $chunkSize)
    {
        return array_chunk($items, $chunkSize, true);
    }

    public function createSequentialChunk(array $items, $chunkSize)
    {
        $chunk = array_slice($items, 0, $chunkSize, true);
        $remaining = array_slice($items, $chunkSize, null, true);

        return [$chunk, $remaining];
    }

    /**
     * @param string $userId
     *
     * @return BatchTask[]
     */
    public function getBatchTasksFromUser(string $userId): array
    {
        return $this->entityManagerService->getManager()->getRepository(BatchTask::class)->findBy(
            ['userId' => $userId],
            ['createdAt' => 'asc']
        );
    }

    public function processNotificationAction(int $taskId): Response
    {
        $task = $this->getTaskById($taskId);

        if (empty($task)) {
            throw new NotFoundHttpException('Task not found.');
        }

        foreach ($this->processNotificationActionHandler as $handler) {
            if ($handler->supports($task)) {
                $this->terminateBatchTask = $task;

                return $handler->handle($task);
            }
        }
        if (empty($task)) {
            throw new NotFoundHttpException('No handler was able to process notification for batch task of type ' . $task->getType() . '.');
        }
    }

    public function terminateBatchTask(BatchTask $batchTask = null)
    {
        $batchTask = $batchTask ?? $this->terminateBatchTask;

        if (empty($batchTask)) {
            return;
        }

        foreach ($this->processNotificationActionHandler as $handler) {
            if ($handler->supports($batchTask)) {
                $handler->terminate($batchTask);
            }
        }
    }

    public function addProcessNotificationActionHandler(ProcessNotificationActionInterface $handler)
    {
        $this->processNotificationActionHandler[] = $handler;
    }

    public function cleanupOutdatedBatchTasks()
    {
        $outdatedUncompletedTasks = $this->entityManagerService->getManager()->createQueryBuilder()
            ->select('task')
            ->from(BatchTask::class, 'task')
            ->where('task.modifiedAt < :timestamp and task.state in(:state)')
            ->setParameter('timestamp', time() - 60 * 60 * $this->cleanupUncompletedTasksAfterHours)
            ->setParameter('state', [State::PREPARING, State::RUNNING])
            ->getQuery()
            ->getResult();

        $outdatedFinishedTasks = $this->entityManagerService->getManager()->createQueryBuilder()
            ->select('task')
            ->from(BatchTask::class, 'task')
            ->where('task.modifiedAt < :timestamp and task.state = :state')
            ->setParameter('timestamp', time() - 60 * 60 * $this->cleanupFinishedTasksAfterHours)
            ->setParameter('state', State::FINISHED)
            ->getQuery()
            ->getResult();

        $outdatedTasks = array_merge($outdatedUncompletedTasks, $outdatedFinishedTasks);

        foreach ($outdatedTasks as $task) {
            $this->logger->info('Terminate outdated batch task ID ' . $task->getId());
            $this->terminateBatchTask($task);
            $this->deleteBatchTask($task);
        }
    }

    /**
     * @param BatchTask $batchTask
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function deleteProcecssedItems(BatchTask $batchTask)
    {
        $this->entityManagerService->getManager()->getConnection()->executeStatement(
            'delete from '.BatchTaskProcessedItem::TABLE.' where taskId=?',
            [$batchTask->getId()]
        );
    }
}
