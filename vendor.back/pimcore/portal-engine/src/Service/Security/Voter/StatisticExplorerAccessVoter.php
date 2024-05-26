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
use Pimcore\Bundle\PortalEngineBundle\Service\PortalConfig\PortalConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\PermissionService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\Traits\SecurityServiceAware;
use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

/**
 * Class StatisticExplorerAccessVoter
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\Security\Voter
 */
class StatisticExplorerAccessVoter extends Voter
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
     * @var PermissionService
     */
    protected $permissionService;

    /**
     * StatisticExplorerAccessVoter constructor.
     *
     * @param PortalConfigService $portalConfigService
     * @param Security $security
     * @param PermissionService $permissionService
     */
    public function __construct(PortalConfigService $portalConfigService, Security $security, PermissionService $permissionService)
    {
        $this->portalConfigService = $portalConfigService;
        $this->security = $security;
        $this->permissionService = $permissionService;
    }

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed $subject The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool True if the attribute and subject are supported, false otherwise
     */
    protected function supports($attribute, $subject)
    {
        return $this->portalConfigService->isPortalEngineSite() && $attribute === Permission::STATISTIC_EXPLORER_ACCESS;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
     *
     * @param string $attribute
     * @param mixed $subject
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var int $portalId */
        $portalId = $this->portalConfigService->getCurrentPortalConfig()->getPortalId();
        /** @var PortalUserInterface|Concrete $user */
        $user = $this->securityService->getPortalUser();

        return $user instanceof PortalUserInterface && $this->permissionService->isAllowed($user, Permission::STATISTIC_EXPLORER_ACCESS . Permission::PERMISSION_DELIMITER . $portalId);
    }
}
