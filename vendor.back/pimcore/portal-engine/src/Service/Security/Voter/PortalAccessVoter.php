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
use Pimcore\Bundle\PortalEngineBundle\Event\Permission\PortalAccessEvent;
use Pimcore\Bundle\PortalEngineBundle\Model\DataObject\PortalUserInterface;
use Pimcore\Bundle\PortalEngineBundle\Service\PortalConfig\PortalConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\PermissionService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\Traits\SecurityServiceAware;
use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class PortalAccessVoter extends Voter
{
    use SecurityServiceAware;

    /**
     * @var PortalConfigService
     */
    protected $portalConfigService;

    /**
     * @var Security $security
     */
    protected $security;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var PermissionService
     */
    protected $permissionService;

    /**
     * PortalAccessVoter constructor.
     *
     * @param PortalConfigService $portalConfigService
     * @param Security $security
     * @param EventDispatcherInterface $eventDispatcher
     * @param PermissionService $permissionService
     */
    public function __construct(PortalConfigService $portalConfigService, Security $security, EventDispatcherInterface $eventDispatcher, PermissionService $permissionService)
    {
        $this->portalConfigService = $portalConfigService;
        $this->security = $security;
        $this->eventDispatcher = $eventDispatcher;
        $this->permissionService = $permissionService;
    }

    protected function supports($attribute, $subject)
    {
        return $this->portalConfigService->isPortalEngineSite()
               && $attribute === Permission::PORTAL_ACCESS;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $portalId = $this->portalConfigService->getCurrentPortalConfig()->getPortalId();

        /**
         * @var PortalUserInterface|Concrete $user
         */
        $user = $this->securityService->getPortalUser();

        $allowed = $user instanceof PortalUserInterface
            && $this->permissionService->isAllowed($user, Permission::PORTAL_ACCESS . Permission::PERMISSION_DELIMITER . $portalId);

        $event = new PortalAccessEvent($allowed, $portalId, $token);
        $this->eventDispatcher->dispatch($event);

        return $event->isAllowed();
    }
}
