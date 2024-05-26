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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Security\Voter;

use Pimcore\Bundle\PortalEngineBundle\Enum\Permission;
use Pimcore\Bundle\PortalEngineBundle\Event\Permission\DataPoolVersionHistoryEvent;
use Pimcore\Bundle\PortalEngineBundle\Model\DataObject\PortalUserInterface;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolService;
use Pimcore\Bundle\PortalEngineBundle\Service\PortalConfig\PortalConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\PermissionService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\Traits\SecurityServiceAware;
use Pimcore\Model\Element\ElementInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class DataPoolVersionHistoryVoter extends Voter
{
    use SecurityServiceAware;

    /**
     * @var PortalConfigService
     */
    protected $portalConfigService;

    /**
     * @var DataPoolConfigService
     */
    protected $dataPoolConfigService;

    /**
     * @var DataPoolService
     */
    protected $dataPoolService;

    /**
     * @var Security
     */
    protected $security;

    /**
     * @var PermissionService
     */
    protected $permissionService;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * DataPoolVersionHistoryVoter constructor.
     *
     * @param PortalConfigService $portalConfigService
     * @param DataPoolConfigService $dataPoolConfigService
     * @param Security $security
     * @param DataPoolService $dataPoolService
     * @param PermissionService $permissionService
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(PortalConfigService $portalConfigService, DataPoolConfigService $dataPoolConfigService, Security $security, DataPoolService $dataPoolService, PermissionService $permissionService, EventDispatcherInterface $eventDispatcher)
    {
        $this->portalConfigService = $portalConfigService;
        $this->dataPoolConfigService = $dataPoolConfigService;
        $this->dataPoolService = $dataPoolService;
        $this->security = $security;
        $this->permissionService = $permissionService;
        $this->eventDispatcher = $eventDispatcher;
    }

    protected function supports($attribute, $subject)
    {
        return $this->portalConfigService->isPortalEngineSite()
               && $attribute === Permission::VERSION_HISTORY;
    }

    /**
     * @param string $attribute
     * @param ElementInterface $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $allowed = true;
        if (!$this->security->isGranted(Permission::DATA_POOL_ACCESS)) {
            $allowed = false;
        }

        $dataPoolConfig = $this->dataPoolConfigService->getCurrentDataPoolConfig();

        if (!$dataPoolConfig->getEnableVersionHistory()) {
            $allowed = false;
        }

        if ($allowed) {
            /**
             * @var PortalUserInterface $user
             */
            $user = $this->securityService->getPortalUser();
            $allowed = $this->permissionService->isAllowed($user, Permission::VERSION_HISTORY . Permission::PERMISSION_DELIMITER . $dataPoolConfig->getId());
        }

        $event = new DataPoolVersionHistoryEvent($allowed, $dataPoolConfig->getId(), $this->securityService->getPortalUser());
        $this->eventDispatcher->dispatch($event);

        return $event->isAllowed();
    }
}
