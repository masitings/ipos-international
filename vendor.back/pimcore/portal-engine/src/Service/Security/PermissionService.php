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

use FrontendPermissionToolkitBundle\Service;
use Pimcore\Bundle\PortalEngineBundle\Enum\Permission;
use Pimcore\Bundle\PortalEngineBundle\Event\Permission\AbstractEvent\DataPoolItemPermissionEvent;
use Pimcore\Bundle\PortalEngineBundle\Event\Permission\DataPoolAccessEvent;
use Pimcore\Bundle\PortalEngineBundle\Event\Permission\DataPoolCreateItemEvent;
use Pimcore\Bundle\PortalEngineBundle\Event\Permission\DataPoolDeleteItemEvent;
use Pimcore\Bundle\PortalEngineBundle\Event\Permission\DataPoolDownloadItemEvent;
use Pimcore\Bundle\PortalEngineBundle\Event\Permission\DataPoolEditItemEvent;
use Pimcore\Bundle\PortalEngineBundle\Event\Permission\DataPoolShowItemEvent;
use Pimcore\Bundle\PortalEngineBundle\Event\Permission\DataPoolSubfolderItemEvent;
use Pimcore\Bundle\PortalEngineBundle\Event\Permission\DataPoolUpdateItemEvent;
use Pimcore\Bundle\PortalEngineBundle\Event\Permission\DataPoolViewOwnedAssetsOnlyEvent;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\AssetConfig;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataPoolConfigInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\WorkspaceConfig;
use Pimcore\Bundle\PortalEngineBundle\Model\DataObject\PortalUserInterface;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolService;
use Pimcore\Bundle\PortalEngineBundle\Service\Download\DownloadProviderService;
use Pimcore\Bundle\PortalEngineBundle\Service\PublicShare\PublicShareService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\Authentication\UserProvider;
use Pimcore\Bundle\PortalEngineBundle\Service\Workflow\WorkflowService;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Tool;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class PermissionService
{
    /**
     * @var Service
     */
    protected $frontendPermissionService;

    /**
     * @var DataPoolConfigService
     */
    protected $dataPoolConfigService;

    /**
     * @var DataPoolService
     */
    protected $dataPoolService;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var DownloadProviderService
     */
    protected $downloadProviderService;

    /**
     * @var WorkflowService
     */
    protected $workflowService;

    /**
     * @var PublicShareService
     */
    protected $publicShareService;

    /**
     * @var bool[]
     */
    protected $dataPoolhasDownloadTypes = [];

    /**
     * @var UserProvider
     */
    protected $userProvider;

    /**
     * PermissionService constructor.
     *
     * @param Service $frontendPermissionService
     * @param DataPoolConfigService $dataPoolConfigService
     * @param DataPoolService $dataPoolService
     * @param EventDispatcherInterface $eventDispatcher
     * @param DownloadProviderService $downloadProviderService
     * @param WorkflowService $workflowService
     * @param PublicShareService $publicShareService
     */
    public function __construct(
        Service $frontendPermissionService,
        DataPoolConfigService $dataPoolConfigService,
        DataPoolService $dataPoolService,
        EventDispatcherInterface $eventDispatcher,
        DownloadProviderService $downloadProviderService,
        WorkflowService $workflowService,
        PublicShareService $publicShareService,
        UserProvider $userProvider
    ) {
        $this->frontendPermissionService = $frontendPermissionService;
        $this->dataPoolConfigService = $dataPoolConfigService;
        $this->dataPoolService = $dataPoolService;
        $this->eventDispatcher = $eventDispatcher;
        $this->downloadProviderService = $downloadProviderService;
        $this->workflowService = $workflowService;
        $this->publicShareService = $publicShareService;
        $this->userProvider = $userProvider;
    }

    /**
     * @param PortalUserInterface|Concrete $user
     * @param string $permission
     *
     * @return bool
     */
    public function isAllowed(PortalUserInterface $user, string $permission): bool
    {
        if (Tool::isFrontendRequestByAdmin()) {
            return true;
        }

        if ($user->getAdmin() || $user->isPortalShareUser()) {
            return true;
        }

        return $this->frontendPermissionService->isAllowed($user, $permission);
    }

    /**
     * @param PortalUserInterface|UserInterface $user
     * @param int $dataPoolId
     *
     * @return bool
     */
    public function isDataPoolAccessAllowed(PortalUserInterface $user, int $dataPoolId): bool
    {
        $allowed = $user instanceof PortalUserInterface
            && $this->isAllowed($user, Permission::DATA_POOL_ACCESS . Permission::PERMISSION_DELIMITER . $dataPoolId);

        $event = new DataPoolAccessEvent($allowed, $dataPoolId, $user);
        $this->eventDispatcher->dispatch($event);

        return $event->isAllowed();
    }

    /**
     * @param string $permission
     * @param PortalUserInterface $user
     * @param int $dataPoolId
     * @param string $fullPath
     * @param bool $forceWorkspacesUpdate
     * @param bool $respectWorkflowPermissions
     * @param bool $respectDataPoolWorkspaces
     * @param bool $respectUploadFolder
     *
     * @return bool
     */
    public function isPermissionAllowed(string $permission, PortalUserInterface $user, int $dataPoolId, string $fullPath, bool $forceWorkspacesUpdate = false, bool $respectWorkflowPermissions = false, bool $respectDataPoolWorkspaces = true, bool $respectUploadFolder = false): bool
    {
        /** @var bool $allowed */
        $allowed = true;

        if (!$this->isDataPoolAccessAllowed($user, $dataPoolId)) {
            $allowed = false;
        }

        $dataPoolConfig = $this->dataPoolConfigService->getDataPoolConfigById($dataPoolId);
        $dataPool = $this->dataPoolService->getDataPoolByConfig($dataPoolConfig);

        if (Permission::DOWNLOAD === $permission && !$this->hasDownloadTypes($dataPoolConfig)) {
            return false;
        }

        if ($allowed && $respectDataPoolWorkspaces) {
            $allowed = $this->checkIfPermissionAllowed(
                $permission,
                $dataPool->getSearchService()->getWorkspaceService()->getDataPoolWorkspaces($forceWorkspacesUpdate, $dataPoolConfig),
                $fullPath
            );
        }

        $isViewOwnedAssetsOnlyPermission = $permission === Permission::VIEW_OWNED_ASSET_ONLY;

        if (($allowed && !$isViewOwnedAssetsOnlyPermission) || (!$allowed && $isViewOwnedAssetsOnlyPermission)) {
            $allowed = $this->checkIfPermissionAllowed(
                $permission,
                $dataPool->getSearchService()->getWorkspaceService()->getUserWorkspaces($this->getOriginUserForPublicShare($user), $forceWorkspacesUpdate, $dataPoolConfig),
                $fullPath
            );
        }

        if ($allowed && $respectWorkflowPermissions) {
            $allowed = $this->checkIfPermissionAllowedInWorkflows(
                $permission,
                $fullPath
            );
        }

        if (!$allowed && $respectUploadFolder) {
            $allowed = $this->isPermissionAllowedInUploadFolder($permission, $user, $dataPoolId, $fullPath);
        }

        if ($eventClass = $this->getPermissionEventClass($permission)) {
            /**
             * @var DataPoolItemPermissionEvent $event
             */
            $event = new $eventClass($allowed, $dataPoolConfig->getId(), $fullPath, $dataPoolConfig->getElementType(), $user);
            $this->eventDispatcher->dispatch($event);
            $allowed = $event->isAllowed();
        }

        return $allowed;
    }

    public function isPermissionAllowedInUploadFolder(string $permission, PortalUserInterface $portalUser, int $dataPoolId, string $fullPath): bool
    {
        $dataPoolConfig = $this->dataPoolConfigService->getDataPoolConfigById($dataPoolId);
        if (!$dataPoolConfig instanceof AssetConfig) {
            return false;
        }

        if (!$uploadFolder = $dataPoolConfig->getUploadFolder()) {
            return false;
        }

        if (strpos($fullPath, $uploadFolder->getRealFullPath()) !== 0) {
            return false;
        }

        return $this->isPermissionAllowed($permission, $portalUser, $this->dataPoolConfigService->getCurrentDataPoolConfig()->getId(), $fullPath, false, false, false);
    }

    /**
     * @param string $permission
     *
     * @return string|null
     */
    protected function getPermissionEventClass(string $permission): ?string
    {
        if ($permission === Permission::VIEW) {
            return DataPoolShowItemEvent::class;
        } elseif ($permission === Permission::DOWNLOAD) {
            return DataPoolDownloadItemEvent::class;
        } elseif ($permission === Permission::DELETE) {
            return DataPoolDeleteItemEvent::class;
        } elseif ($permission === Permission::CREATE) {
            return DataPoolCreateItemEvent::class;
        } elseif ($permission === Permission::UPDATE) {
            return DataPoolUpdateItemEvent::class;
        } elseif ($permission === Permission::EDIT) {
            return DataPoolEditItemEvent::class;
        } elseif ($permission === Permission::SUBFOLDER) {
            return DataPoolSubfolderItemEvent::class;
        } elseif ($permission === Permission::VIEW_OWNED_ASSET_ONLY) {
            return DataPoolViewOwnedAssetsOnlyEvent::class;
        }

        return null;
    }

    /**
     * @param WorkspaceConfig $workspace
     * @param string $permission
     * @param string $fullPath
     *
     * @return bool
     */
    public function checkPermissionOfWorkspace(WorkspaceConfig $workspace, string $permission, ?string $fullPath = null)
    {
        $workspacePermissionMethod = null;

        switch ($permission) {
            case Permission::VIEW:
                $workspacePermissionMethod = 'getPermissionView';
                break;
            case Permission::DOWNLOAD:
                $workspacePermissionMethod = 'getPermissionDownload';
                break;
            case Permission::EDIT:
                $workspacePermissionMethod = 'getPermissionEdit';
                break;
            case Permission::UPDATE:
                $workspacePermissionMethod = 'getPermissionUpdate';
                break;
            case Permission::CREATE:
                $workspacePermissionMethod = 'getPermissionCreate';
                break;
            case Permission::DELETE:
                $workspacePermissionMethod = 'getPermissionDelete';
                break;
            case Permission::SUBFOLDER:
                $workspacePermissionMethod = 'getPermissionSubfolder';
                break;
            case Permission::VIEW_OWNED_ASSET_ONLY:
                $workspacePermissionMethod = 'getPermissionViewOwnedAssetsOnly';
                break;
        }

        if (!is_null($workspacePermissionMethod) && method_exists($workspace, $workspacePermissionMethod)) {
            return (bool)$workspace->$workspacePermissionMethod();
        }

        return false;
    }

    /**
     * @param string $permission
     * @param \Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\WorkspaceConfig[] $workspaces
     * @param string $fullPath
     *
     * @return bool
     */
    protected function checkIfPermissionAllowed(string $permission, array $workspaces, string $fullPath): bool
    {
        foreach ($workspaces as $workspace) {
            if (strpos($fullPath, $workspace->getFullPath()) === 0) {
                return $this->checkPermissionOfWorkspace($workspace, $permission, $fullPath);
            }
        }

        return false;
    }

    protected function checkIfPermissionAllowedInWorkflows(string $permission, string $fullPath): bool
    {
        $asset = Asset::getByPath($fullPath);
        if (empty($asset)) {
            return true;
        }

        return $this->workflowService->isPermissionAllowedInWorkflows($asset, $permission);
    }

    /**
     * @param DataPoolConfigInterface $dataPoolConfig
     *
     * @return bool
     */
    protected function hasDownloadTypes(DataPoolConfigInterface $dataPoolConfig): bool
    {
        if (!isset($this->dataPoolhasDownloadTypes[$dataPoolConfig->getId()])) {
            if ($publicShare = $this->publicShareService->getCurrentPublicShare()) {
                $downloadTypes = $this->downloadProviderService->getPublicShareAllowedDownloadTypes($dataPoolConfig, $publicShare);
            } else {
                $downloadTypes = $this->downloadProviderService->getDownloadTypes($dataPoolConfig, php_sapi_name() !== 'cli');
            }

            $this->dataPoolhasDownloadTypes[$dataPoolConfig->getId()] = sizeof($downloadTypes) > 0;
        }

        return $this->dataPoolhasDownloadTypes[$dataPoolConfig->getId()];
    }

    /**
     * @param PortalUserInterface $user
     * @param int $dataPoolId
     * @param string $fullPath
     * @param bool $respectWorkflowPermissions
     * @param bool $respectDataPoolWorkspaces
     * @param bool $respectUploadFolder
     *
     * @return array
     */
    public function getPermissionsForUser(PortalUserInterface $user, int $dataPoolId, string $fullPath, bool $respectWorkflowPermissions = false, bool $respectDataPoolWorkspaces = true, bool $respectUploadFolder = false)
    {
        $permissions = [
            Permission::VIEW,
            Permission::DOWNLOAD,
            Permission::EDIT,
            Permission::UPDATE,
            Permission::CREATE,
            Permission::DELETE,
            Permission::SUBFOLDER
        ];

        $allowed = [];

        foreach ($permissions as $permission) {
            $allowed[$permission] = $this->isPermissionAllowed($permission, $user, $dataPoolId, $fullPath, false, $respectWorkflowPermissions, $respectDataPoolWorkspaces, $respectUploadFolder);
        }

        return $allowed;
    }

    /**
     * Return portal user that created public share, if we are in public share context
     * Otherwise return given user
     *
     * @param PortalUserInterface $portalUser
     *
     * @return PortalUserInterface
     */
    protected function getOriginUserForPublicShare(PortalUserInterface $portalUser): PortalUserInterface
    {
        if ($portalUser->isPortalShareUser() && $publicShare = $this->publicShareService->getCurrentPublicShare()) {
            $originalUser = $this->userProvider->getById($publicShare->getUserId());
            if ($originalUser) {
                return $originalUser;
            }
        }

        return $portalUser;
    }
}
