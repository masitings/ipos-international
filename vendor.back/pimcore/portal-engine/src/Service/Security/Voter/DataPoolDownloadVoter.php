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
use Pimcore\Bundle\PortalEngineBundle\Event\Permission\DataPoolDownloadAccessEvent;
use Pimcore\Bundle\PortalEngineBundle\Model\DataObject\PortalUserInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadAccess;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\Download\DownloadProviderService;
use Pimcore\Bundle\PortalEngineBundle\Service\PortalConfig\PortalConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\PublicShare\PublicShareService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\PermissionService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\Traits\SecurityServiceAware;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class DataPoolDownloadVoter extends Voter
{
    use SecurityServiceAware;

    protected $portalConfigService;
    protected $dataPoolConfigService;
    protected $permissionService;
    protected $downloadProviderService;
    protected $publicShareService;
    protected $eventDispatcher;

    public function __construct(
        PortalConfigService $portalConfigService,
        DataPoolConfigService $dataPoolConfigService,
        PermissionService $permissionService,
        DownloadProviderService $downloadProviderService,
        PublicShareService $publicShareService,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->portalConfigService = $portalConfigService;
        $this->dataPoolConfigService = $dataPoolConfigService;
        $this->permissionService = $permissionService;
        $this->downloadProviderService = $downloadProviderService;
        $this->publicShareService = $publicShareService;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     *
     * @return bool
     */
    protected function supports($attribute, $subject)
    {
        return
            $this->portalConfigService->isPortalEngineSite() &&
            $attribute === Permission::DOWNLOAD &&
            $subject instanceof DownloadAccess;
    }

    /**
     * @param string $attribute
     * @param DownloadAccess $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $this->securityService->getPortalUser();

        $allowed =
            $user instanceof PortalUserInterface &&
            $this->permissionService->isAllowed($user, $subject->toPermission());

        $downloadTypeFormatPermissions = $this->getDownloadTypeFormatPerimssions($subject);
        if (!isset($downloadTypeFormatPermissions[$subject->toPermission()])) {
            $allowed = false;
        }

        if (!$allowed && $user instanceof PortalUserInterface) {
            // If download type itself is forbidden but one of its download formats is allowed, the download is permitted.
            $allowed = $this->isAnyFormatAllowed($subject, $user, $downloadTypeFormatPermissions);
        } elseif ($allowed) {
            // Do not allow formats which are not specificed in data pool or public share
            $allowed = $this->isAnyFormatAllowed($subject, $user, $downloadTypeFormatPermissions, false);
        }

        $event = new DataPoolDownloadAccessEvent($allowed, $subject, $token);
        $this->eventDispatcher->dispatch($event);

        return $event->isAllowed();
    }

    protected function isAnyFormatAllowed(DownloadAccess $downloadAccess, PortalUserInterface $user, array $downloadTypeFormatPermissions, bool $checkFormatPermissions = true): bool
    {
        foreach ($downloadAccess->getFormatAccess() as $formatAccess) {
            if (!isset($downloadTypeFormatPermissions[$downloadAccess->toPermission()][$formatAccess->getFormat()])) {
                continue;
            }
            $allowed = !$checkFormatPermissions || $this->permissionService->isAllowed($user, $formatAccess->toPermission());
            if ($allowed) {
                return true;
            }
        }

        return false;
    }

    protected function getDownloadTypeFormatPerimssions(DownloadAccess $downloadAccess)
    {
        $dataPoolConfig = $this->dataPoolConfigService->getDataPoolConfigById($downloadAccess->getDataPoolConfigId());
        if ($publicShare = $this->publicShareService->getCurrentPublicShare()) {
            $downloadTypes = $this->downloadProviderService->getPublicShareAllowedDownloadTypes($dataPoolConfig, $publicShare);
        } else {
            $downloadTypes = $this->downloadProviderService->getDownloadTypes($dataPoolConfig, false);
        }

        $result = [];
        foreach ($downloadTypes as $downloadType) {
            $formats = array_map(function (array $format) {
                return $format['id'];
            }, $downloadType->getFormats());

            $formats = array_flip($formats);

            $result[DownloadAccess::fromDownloadType($downloadAccess->getDataPoolConfigId(), $downloadType)->toPermission()] = $formats;
        }

        return $result;
    }
}
