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

namespace Pimcore\Bundle\PortalEngineBundle\EventSubscriber;

use Pimcore\Bundle\PortalEngineBundle\Entity\BatchTask;
use Pimcore\Bundle\PortalEngineBundle\Message\BatchTask\Interfaces\BatchTaskMessageInterface;
use Pimcore\Bundle\PortalEngineBundle\Message\BatchTask\Interfaces\SequentialBatchTaskMessageInterface;
use Pimcore\Bundle\PortalEngineBundle\Message\BatchTask\Interfaces\SplittedBatchTaskMessageInterface;
use Pimcore\Bundle\PortalEngineBundle\Message\BatchTask\Interfaces\TriggerFinishedMessageBatchTaskMessageInterface;
use Pimcore\Bundle\PortalEngineBundle\Service\BatchTask\BatchTaskService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Class IndexUpdateListener
 *
 * @package Pimcore\Bundle\PortalEngineBundle\EventListener
 */
class BatchTaskSubscriber implements EventSubscriberInterface
{
    /**
     * @var BatchTaskService
     */
    protected $batchTaskService;

    /**
     * @var MessageBusInterface
     */
    protected $messageBus;

    /**
     * @var BatchTask
     */
    protected $terminateBatchTask;

    /**
     * BatchTaskSubscriber constructor.
     *
     * @param BatchTaskService $batchTaskService
     * @param MessageBusInterface $messageBus
     */
    public function __construct(BatchTaskService $batchTaskService, MessageBusInterface $messageBus)
    {
        $this->batchTaskService = $batchTaskService;
        $this->messageBus = $messageBus;
    }

    public static function getSubscribedEvents()
    {
        return [
            WorkerMessageFailedEvent::class => 'onBatchTaskMessageFailed',
            WorkerMessageHandledEvent::class => 'onWorkerMessageHandled',
            TerminateEvent::class => 'onTerminate',
        ];
    }

    /**
     * Mark batch tasks with failed items as finished as otherwise they will run forever.
     *
     * @param WorkerMessageFailedEvent $event
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function onBatchTaskMessageFailed(WorkerMessageFailedEvent $event)
    {
        $message = $event->getEnvelope()->getMessage();
        if (!$message instanceof BatchTaskMessageInterface) {
            return;
        }

        if ($event->willRetry()) {
            return;
        }

        if (!$batchTask = $this->batchTaskService->getTaskById($message->getTaskId())) {
            return;
        }

        foreach (array_keys($message->getItems()) as $itemIndex) {
            if (!$this->batchTaskService->isItemIndexProcessed($batchTask, $itemIndex)) {
                $this->batchTaskService->markItemIndexAsProcessed($batchTask, $itemIndex);
            }
        }

        $this->checkBatchTaskFinished($batchTask, $message);
    }

    public function onWorkerMessageHandled(WorkerMessageHandledEvent $event)
    {
        $message = $event->getEnvelope()->getMessage();
        if (!$message instanceof BatchTaskMessageInterface) {
            return;
        }

        if ($message instanceof SplittedBatchTaskMessageInterface) {
            return;
        }

        if (!$batchTask = $this->batchTaskService->getTaskById($message->getTaskId())) {
            return;
        }

        if ($message instanceof SequentialBatchTaskMessageInterface && $message->hasRemainingItems()) {
            $remainingMessage = $message->createRemainingMessage($this->batchTaskService);
            $this->messageBus->dispatch($remainingMessage);

            return;
        }

        $this->checkBatchTaskFinished($batchTask, $message);
    }

    public function onTerminate(TerminateEvent $event)
    {
        $this->batchTaskService->terminateBatchTask();
    }

    public function setTerminateBatchTask(BatchTask $batchTask)
    {
        $this->terminateBatchTask = $batchTask;
    }

    protected function checkBatchTaskFinished(BatchTask $batchTask, BatchTaskMessageInterface $message)
    {
        $this->batchTaskService->checkBatchTaskFinished($batchTask);

        if ($message instanceof TriggerFinishedMessageBatchTaskMessageInterface) {
            $finishedMessage = $message->createFinishedMessage();
            $this->messageBus->dispatch($finishedMessage);
        }
    }
}
