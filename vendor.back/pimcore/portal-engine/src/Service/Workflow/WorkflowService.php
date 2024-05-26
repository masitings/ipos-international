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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Workflow;

use Pimcore\Bundle\PortalEngineBundle\Enum\DataPool\TranslatorDomain;
use Pimcore\Bundle\PortalEngineBundle\Enum\Permission;
use Pimcore\Bundle\PortalEngineBundle\EventSubscriber\SaveUserSubscriber;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\TranslatorService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\SecurityService;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Document\PageSnippet;
use Pimcore\Model\Element\AbstractElement;
use Pimcore\Model\Element\ElementInterface;
use Pimcore\Model\Element\Note;
use Pimcore\Model\Element\Service;
use Pimcore\Model\User;
use Pimcore\Workflow\ActionsButtonService;
use Pimcore\Workflow\EventSubscriber\NotesSubscriber;
use Pimcore\Workflow\Manager;
use Pimcore\Workflow\Place\StatusInfo;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Workflow\Registry;

class WorkflowService
{
    /**
     * @var Manager
     */
    protected $workflowManager;

    /**
     * @var Registry
     */
    protected $workflowRegistry;

    /**
     * @var ActionsButtonService
     */
    protected $actionsButtonService;

    /**
     * @var StatusInfo
     */
    protected $placeStatusInfo;

    /**
     * @var TranslatorService
     */
    protected $translatorService;

    /**
     * @var SecurityService
     */
    protected $securityService;

    protected $tokenStorage;

    /**
     * WorkflowService constructor.
     *
     * @param Manager $workflowManager
     * @param Registry $workflowRegistry
     * @param ActionsButtonService $actionsButtonService
     * @param StatusInfo $placeStatusInfo
     * @param TranslatorService $translatorService
     * @param SecurityService $securityService
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        Manager $workflowManager,
        Registry $workflowRegistry,
        ActionsButtonService $actionsButtonService,
        StatusInfo $placeStatusInfo,
        TranslatorService $translatorService,
        SecurityService $securityService,
        TokenStorageInterface $tokenStorage
    ) {
        $this->workflowManager = $workflowManager;
        $this->workflowRegistry = $workflowRegistry;
        $this->actionsButtonService = $actionsButtonService;
        $this->placeStatusInfo = $placeStatusInfo;
        $this->translatorService = $translatorService;
        $this->securityService = $securityService;
        $this->tokenStorage = $tokenStorage;
    }

    public function getWorkflowDetails(AbstractElement $element): array
    {
        $workflows = [];
        foreach ($this->workflowManager->getAllWorkflows() as $workflowName) {
            $workflow = $this->workflowManager->getWorkflowIfExists($element, $workflowName);
            $workflowConfig = $this->workflowManager->getWorkflowConfig($workflowName);

            if (empty($workflow)) {
                continue;
            }
            $allowedTransitions = $this->actionsButtonService->getAllowedTransitions($workflow, $element);
            $globalActions = $this->actionsButtonService->getGlobalActions($workflow, $element);

            if (!sizeof($allowedTransitions) && !sizeof($globalActions)) {
                continue;
            }

            $workflows[] = [
                'name' => $workflow->getName(),
                'label' => $workflowConfig->getLabel(),
                'allowedTransitions' => $allowedTransitions,
                'globalActions' => $globalActions,
            ];
        }

        return [
            'workflow' => $workflows,
            'statusInfo' => $this->placeStatusInfo->getAllPalacesHtml($element),
            'history' => $this->getWorkflowHistory($element)
        ];
    }

    protected function getWorkflowHistory(AbstractElement $element)
    {
        $notes = new Note\Listing;
        $notes->setCondition('ctype=? and cid=? and type=? order by date desc', [
            Service::getType($element),
            $element->getId(),
            'Status Update'
        ])
        ->setLimit(100);

        $history = [];
        foreach ($notes as $note) {
            $user = User::getById($note->getUser());
            $history[] = [
                'title' => $note->getTitle(),
                'description' => $note->getDescription(),
                'user' => $user ? $user->getName() : '',
                'date' => $note->getDate(),
            ];
        }

        return $history;
    }

    /**
     * @param AbstractElement $element
     *
     * @return Note|null
     */
    protected function getLastWorkflowNote(AbstractElement $element)
    {
        $notes = new Note\Listing;
        $notes->setCondition(
            'ctype=? and cid=? and type=? order by date desc',
            [
                Service::getType($element),
                $element->getId(),
                'Status Update'
            ]
        )->setLimit(1);

        return $notes->current();
    }

    /**
     * @param Asset|Concrete|PageSnippet $subject
     * @param string $workflowName
     * @param string $transitionName
     *
     * @return string|null potential error message
     *
     * @throws \Exception
     */
    public function applyTransition(AbstractElement $subject, string $workflowName, string $transitionName, array $data = []): ?string
    {
        $workflow = $this->workflowRegistry->get($subject, $workflowName);

        $transitionLabel = $transitionName;
        if ($transition = $this->workflowManager->getTransitionByName($workflowName, $transitionName)) {
            $transitionLabel = $this->translatorService->translate($transition->getLabel(), TranslatorDomain::DOMAIN_WORKFLOW_TRANSITION);
        }

        if ($workflow->can($subject, $transitionName)) {
            try {
                $this->workflowManager->applyWithAdditionalData($workflow, $subject, $transitionName, $this->transformAddtionalData($data), true);
                if ($note = $this->getLastWorkflowNote($subject)) {
                    $note->setUser($this->securityService->getPimcoreUserId());
                    $note->save();
                }
            } catch (\Exception $e) {
                return $this->translatorService->translate('transition-failed', TranslatorDomain::DOMAIN_WORKFLOW) . ': ' . $transitionLabel;
            }
        } else {
            return $this->translatorService->translate('transition-not-allowed', TranslatorDomain::DOMAIN_WORKFLOW) . ': ' . $transitionLabel;
        }

        return null;
    }

    /**
     * @param Asset|Concrete|PageSnippet $subject
     * @param string $workflowName
     * @param string $globalActionName
     *
     * @return string|null potential error message
     *
     * @throws \Exception
     */
    public function applyGlobalAction(AbstractElement $subject, string $workflowName, string $globalActionName, array $data = []): ?string
    {
        $workflow = $this->workflowRegistry->get($subject, $workflowName);

        $globalActionLabel = $globalActionName;
        if ($transition = $this->workflowManager->getTransitionByName($workflowName, $globalActionName)) {
            $globalActionLabel = $this->translatorService->translate($transition->getLabel(), TranslatorDomain::DOMAIN_WORKFLOW_TRANSITION);
        }

        try {
            $this->workflowManager->applyGlobalAction($workflow, $subject, $globalActionName, $this->transformAddtionalData($data), true);
        } catch (\Exception $e) {
            return $this->translatorService->translate('transition-failed', TranslatorDomain::DOMAIN_WORKFLOW) . ': ' . $globalActionLabel;
        }

        return null;
    }

    protected function transformAddtionalData(array $data): array
    {
        if (isset($data['comment'])) {
            return [NotesSubscriber::ADDITIONAL_DATA_NOTES_COMMENT => $data['comment']];
        }

        return [];
    }

    public function hasWorkflowWithPermissions(ElementInterface $element): bool
    {
        $resetToken = false;
        if (!$this->tokenStorage->getToken()) {
            $this->tokenStorage->setToken(new AnonymousToken('some_secret', SaveUserSubscriber::FALLBACK_USER_NAME));
            $resetToken = true;
        }

        $workflows = $this->workflowManager->getAllWorkflowsForSubject($element);

        if ($resetToken) {
            $this->tokenStorage->setToken(null);
        }

        foreach ($workflows as $workflow) {
            $places = $this->workflowManager->getPlaceConfigsByWorkflowName($workflow->getName());
            foreach ($places as $place) {
                $permissions = $place->getPlaceConfigArray()['permissions'] ?? [];
                if (sizeof($permissions) > 0) {
                    return true;
                }
            }
        }

        return false;
    }

    public function isPermissionAllowedInWorkflows(ElementInterface $element, string $permission): bool
    {
        // other portal engine permissions are not mapable to workflow permissions
        if (!in_array($permission, [Permission::VIEW, Permission::UPDATE, Permission::DELETE, Permission::EDIT])) {
            return true;
        }

        $permissionMapping = [
            Permission::VIEW => 'view',
            Permission::UPDATE => 'publish',
            Permission::DELETE => 'delete',
            Permission::EDIT => 'publish',
        ];

        foreach ($this->workflowManager->getAllWorkflowsForSubject($element) as $workflow) {
            $marking = $workflow->getMarking($element);

            if (!sizeof($marking->getPlaces())) {
                continue;
            }

            foreach ($this->workflowManager->getOrderedPlaceConfigs($workflow, $marking) as $placeConfig) {
                if (!empty($placeConfig->getPermissions($workflow, $element))) {
                    $permissions = $placeConfig->getUserPermissions($workflow, $element);

                    return isset($permissions[$permissionMapping[$permission]])
                        ? $permissions[$permissionMapping[$permission]]
                        : true;
                }
            }
        }

        return true;
    }
}
