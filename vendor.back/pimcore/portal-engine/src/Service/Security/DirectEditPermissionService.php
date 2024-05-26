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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Security;

use Pimcore\Bundle\DirectEditBundle\Service\Permission\PermissionServiceInterface;
use Pimcore\Bundle\PortalEngineBundle\Enum\Permission;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\ElementDataPoolConfigResolver;
use Pimcore\Model\Asset;

class DirectEditPermissionService implements PermissionServiceInterface
{
    const PREFIX = 'portal_engine_';

    protected $securityService;
    protected $permissionService;
    protected $dataPoolConfigService;
    protected $elementDataPoolConfigResolver;

    public function __construct(
        SecurityService $securityService,
        PermissionService $permissionService,
        DataPoolConfigService $dataPoolConfigService,
        ElementDataPoolConfigResolver $elementDataPoolConfigResolver
    ) {
        $this->securityService = $securityService;
        $this->permissionService = $permissionService;
        $this->dataPoolConfigService = $dataPoolConfigService;
        $this->elementDataPoolConfigResolver = $elementDataPoolConfigResolver;
    }

    public function hasAssetPermission(Asset $asset): bool
    {
        $dataPoolConfig = $this->elementDataPoolConfigResolver->getDataPoolConfigForElement($asset) ?: $this->dataPoolConfigService->getCurrentDataPoolConfig();

        if (!$dataPoolConfig) {
            return false;
        }

        return $this->permissionService->isPermissionAllowed(
            Permission::UPDATE,
            $this->securityService->getPortalUser(),
            $dataPoolConfig->getId(),
            $asset->getRealFullPath()
        );
    }

    public function getUserId(): ?string
    {
        return (string)$this->securityService->getPortalUser()->getId();
    }

    public function getApplicationPrefix(): string
    {
        return self::PREFIX;
    }

    public function mapPimcoreUserId(): int
    {
        return $this->securityService->getPimcoreUserId();
    }
}
