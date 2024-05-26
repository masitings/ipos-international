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

use Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse;
use Pimcore\Bundle\PortalEngineBundle\Enum\BatchTask\State;
use Pimcore\Bundle\PortalEngineBundle\Enum\BatchTask\Type;
use Pimcore\Bundle\PortalEngineBundle\Model\Rest\ApiPayload;
use Pimcore\Bundle\PortalEngineBundle\Service\BatchTask\BatchTaskService;
use Pimcore\Bundle\PortalEngineBundle\Service\PublicShare\PublicShareService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\SecurityService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/batch-task", condition="request.attributes.get('isPortalEngineSite')")
 */
class BatchTaskController extends AbstractRestApiController
{
    /**
     * @Route("/list",
     *     name="pimcore_portalengine_rest_api_batch_task_list"
     * )
     *
     * @throws \Exception
     */
    public function listAction(Request $request, BatchTaskService $batchTaskService, TranslatorInterface $translator, SecurityService $securityService, PublicShareService $publicShareService)
    {
        $batchTasks = $batchTaskService->getBatchTasksFromUser($securityService->getPortalUser()->getPortalUserId());

        $tasks = [];
        $hasQueuedTasks = false;
        foreach ($batchTasks as $batchTask) {
            $hasQueuedTasks = $hasQueuedTasks || $batchTask->getState() !== State::FINISHED;

            $notificationMessage = $translator->trans(
                'portal-engine.batch-task-notification.'.$batchTask->getType() . '.'. $batchTask->getState(),
                [':batchTaskId:' => $batchTask->getId()]
            );

            $notificationLinkText = null;
            $notificationLink = null;

            if ($batchTask->getState() === State::FINISHED && !$batchTask->getDisableNotificationAction()) {
                $notificationLinkText = $translator->trans(
                    'portal-engine.batch-task-notification.'.$batchTask->getType() . '.'. $batchTask->getState() . '.link-text',
                    [':batchTaskId:' => $batchTask->getId()]
                );

                $params = ['batchTaskId' => $batchTask->getId()];

                if ($share = $publicShareService->getCurrentPublicShare()) {
                    $params['publicShareHash'] = $share->getHash();
                }

                $notificationLink = $this->generateUrl(
                    'pimcore_portalengine_rest_api_batch_task_process_notification_action',
                    $params
                );
            } elseif ($batchTask->getState() === State::FINISHED) {
                $notificationMessage = $translator->trans(
                    'portal-engine.batch-task-notification.'.$batchTask->getType() . '.'. $batchTask->getState() . '.no-action',
                    [':batchTaskId:' => $batchTask->getId()]
                );

                $notificationLinkText = $translator->trans(
                    'portal-engine.batch-task-notification.'.$batchTask->getType() . '.'. $batchTask->getState() . '.no-action.link-text',
                    [':batchTaskId:' => $batchTask->getId()]
                );
            }

            $tasks[] = [
                'id' => $batchTask->getId(),
                'userId' => $batchTask->getUserId(),
                'state' => $batchTask->getState(),
                'type' => Type::BATCH_TASK_NOTIFICATION_TYPE,
                'subType' => $batchTask->getType(),
                'totalItems' => $batchTask->getTotalItems(),
                'finishedItems' => $batchTaskService->getFinishedItemsCount($batchTask),
                'notificationMessage' => $notificationMessage,
                'notificationLink' => $notificationLink,
                'notificationLinkText' => $notificationLinkText,
                'disableDeleteConfirmation' => $batchTask->getDisableDeleteConfirmation(),
                'createdAt' => $batchTask->getCreatedAt(),
            ];
        }

        return new JsonResponse(
            [
                'success' => true,
                'data' => [
                    'hasQueuedTasks' => $hasQueuedTasks,
                    'tasks' => $tasks
                ]
            ]
        );
    }

    /**
     * @Route(
     *     "/delete/{batchTaskId}",
     *     name="pimcore_portalengine_rest_api_batch_task_delete",
     *     requirements={"batchTaskId"="\d+"}
     * )
     */
    public function deleteAction(Request $request, BatchTaskService $batchTaskService, SecurityService $securityService, $batchTaskId): JsonResponse
    {
        /** @var ApiPayload $apiPayload */
        $apiPayload = new ApiPayload([]);

        $user = $securityService->getPortalUser();

        try {
            $batchTask = $batchTaskService->getTaskById($batchTaskId);
            if (empty($batchTask) || $batchTask->getUserId() != $user->getPortalUserId()) {
                return new JsonResponse($apiPayload);
            }

            $batchTaskService->terminateBatchTask($batchTask);
            $batchTaskService->deleteBatchTask($batchTask);
        } catch (\Exception $e) {
            $apiPayload->handleOutputErrorException($e);
        }

        return new JsonResponse(
            $apiPayload
        );
    }

    /**
     * @Route("/process-notification-action/{batchTaskId}",
     *     name="pimcore_portalengine_rest_api_batch_task_process_notification_action"
     * )
     *
     * @throws \Exception
     */
    public function processNotificationAction(BatchTaskService $batchTaskService, int $batchTaskId)
    {
        return $batchTaskService->processNotificationAction($batchTaskId);
    }
}
