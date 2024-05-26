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
use Pimcore\Bundle\PortalEngineBundle\Model\DataObject\PortalUserInterface;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\PortalConfig\PortalConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\PermissionService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\Traits\SecurityServiceAware;
use Pimcore\Tool;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class DataPoolAccessVoter extends Voter
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
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var PermissionService
     */
    protected $permissionService;

    /**
     * DataPoolAccessVoter constructor.
     *
     * @param PortalConfigService $portalConfigService
     * @param DataPoolConfigService $dataPoolConfigService
     * @param EventDispatcherInterface $eventDispatcher
     * @param PermissionService $permissionService
     */
    public function __construct(PortalConfigService $portalConfigService, DataPoolConfigService $dataPoolConfigService, EventDispatcherInterface $eventDispatcher, PermissionService $permissionService)
    {
        $this->portalConfigService = $portalConfigService;
        $this->dataPoolConfigService = $dataPoolConfigService;
        $this->eventDispatcher = $eventDispatcher;
        $this->permissionService = $permissionService;
    }

    protected function supports($attribute, $subject)
    {
        return ($this->portalConfigService->isPortalEngineSite() || Tool::isFrontendRequestByAdmin() || $this->securityService->isAdminRestApiCall())
               && $attribute === Permission::DATA_POOL_ACCESS;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if (Tool::isFrontendRequestByAdmin()) {
            return true;
        }
        $currentDataPoolConfigId = $this->dataPoolConfigService->getCurrentDataPoolConfig() ? $this->dataPoolConfigService->getCurrentDataPoolConfig()->getId() : 0;
        $dataPoolId = !empty($subject) ? $subject : $currentDataPoolConfigId;

        $user = $this->securityService->getPortalUser();
        if (!$user instanceof PortalUserInterface) {
            return false;
        }

        return $this->permissionService->isDataPoolAccessAllowed($user, $dataPoolId);
    }
}
