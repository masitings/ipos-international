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
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\AssetConfig;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataPoolConfigInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\DataObject\PortalUserInterface;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\PortalConfig\PortalConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\PermissionService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\Traits\SecurityServiceAware;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

/**
 * Class DataPoolAssetUploadFolderReviewingVoter
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\Security\Voter
 */
class DataPoolAssetUploadFolderReviewingVoter extends Voter
{
    use SecurityServiceAware;

    /**
     * @var PortalConfigService
     */
    protected $portalConfigService;
    /**
     * @var Security
     */
    protected $security;
    /**
     * @var PermissionService
     */
    protected $permissionService;
    /**
     * @var DataPoolConfigService
     */
    protected $dataPoolConfigService;

    /**
     * DataPoolAssetUploadFolderReviewingVoter constructor.
     *
     * @param PortalConfigService $portalConfigService
     * @param Security $security
     * @param PermissionService $permissionService
     * @param DataPoolConfigService $dataPoolConfigService
     */
    public function __construct(PortalConfigService $portalConfigService, Security $security, PermissionService $permissionService, DataPoolConfigService $dataPoolConfigService)
    {
        $this->portalConfigService = $portalConfigService;
        $this->security = $security;
        $this->permissionService = $permissionService;
        $this->dataPoolConfigService = $dataPoolConfigService;
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
        return $this->portalConfigService->isPortalEngineSite()
            && $attribute === Permission::DATA_POOL_ASSET_UPLOAD_FOLDER_REVIEWING;
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
        /** @var bool $allowed */
        $allowed = true;

        try {
            if (!$this->security->isGranted(Permission::DATA_POOL_ACCESS)) {
                throw new \Exception('dataPool access not granted');
            }

            /** @var DataPoolConfigInterface $dataPoolConfig */
            $dataPoolConfig = $this->dataPoolConfigService->getCurrentDataPoolConfig();
            if (!$dataPoolConfig instanceof AssetConfig) {
                throw new \Exception('current dataPoolConfig is not a AssetConfig');
            }

            /** @var PortalUserInterface $user */
            $user = $this->securityService->getPortalUser();
            if (!$this->permissionService->isAllowed($user, Permission::DATA_POOL_ASSET_UPLOAD_FOLDER_REVIEWING . Permission::PERMISSION_DELIMITER . $dataPoolConfig->getId())) {
                throw new \Exception('permission not allowed');
            }
        } catch (\Exception $e) {
            $allowed = false;
        }

        return $allowed;
    }
}
