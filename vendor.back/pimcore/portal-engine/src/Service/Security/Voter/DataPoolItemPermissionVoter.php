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
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\PortalConfig\PortalConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\PermissionService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\Traits\SecurityServiceAware;
use Pimcore\Model\Asset;
use Pimcore\Model\Element\ElementInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class DataPoolItemPermissionVoter extends Voter
{
    use SecurityServiceAware;

    const PERMISSIONS = [
        Permission::CREATE,
        Permission::DELETE,
        Permission::EDIT,
        Permission::VIEW,
        Permission::UPDATE,
        Permission::DOWNLOAD,
        Permission::SUBFOLDER,
        Permission::VIEW_OWNED_ASSET_ONLY,
    ];

    /**
     * @var PortalConfigService
     */
    protected $portalConfigService;

    /**
     * @var DataPoolConfigService
     */
    protected $dataPoolConfigService;

    /**
     * @var PermissionService
     */
    protected $permissionService;

    /**
     * PortalAccessVoter constructor.
     *
     * @param DataPoolConfigService $dataPoolConfigService
     * @param Security $security
     */
    public function __construct(PortalConfigService $portalConfigService, DataPoolConfigService $dataPoolConfigService, PermissionService $permissionService)
    {
        $this->portalConfigService = $portalConfigService;
        $this->dataPoolConfigService = $dataPoolConfigService;
        $this->permissionService = $permissionService;
    }

    protected function supports($attribute, $subject)
    {
        return $this->portalConfigService->isPortalEngineSite()
            && in_array($attribute, self::PERMISSIONS)
            && (is_string($subject) || $subject instanceof ElementInterface);
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
        $dataPoolConfig = $this->dataPoolConfigService->getCurrentDataPoolConfig();
        if (empty($dataPoolConfig)) {
            return false;
        }
        $fullPath = $subject instanceof ElementInterface ? $subject->getRealFullPath() : $subject;
        $respectWorkflowPermissions = $subject instanceof Asset;
        $respectUploadFolderPermissions = $subject instanceof Asset;

        return $this->permissionService->isPermissionAllowed(
            $attribute,
            $this->securityService->getPortalUser(),
            $dataPoolConfig->getId(),
            $fullPath,
            false,
            $respectWorkflowPermissions,
            true,
            $respectUploadFolderPermissions
        );
    }
}
