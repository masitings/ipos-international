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

namespace Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Search;

use ONGR\ElasticsearchDSL\Query\Compound\BoolQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\ExistsQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\TermQuery;
use Pimcore\Bundle\PortalEngineBundle\Enum\ElasticSearchFields;
use Pimcore\Bundle\PortalEngineBundle\Enum\Permission;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataPoolConfigInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\WorkspaceConfig;
use Pimcore\Bundle\PortalEngineBundle\Model\DataObject\PortalUserGroupInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\DataObject\PortalUserInterface;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\TranslatorService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\PermissionService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\SecurityService;
use Pimcore\Model\DataObject\Data\ElementMetadata;
use Pimcore\Model\Element\ElementInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class WorkspaceService
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Search
 */
abstract class AbstractWorkspaceService implements WorkspaceServiceInterface
{
    /** @var DataPoolConfigService */
    protected $dataPoolConfigService;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var TranslatorService
     */
    protected $translatorService;

    /**
     * @return WorkspaceConfig[][]
     */
    protected $userWorkspaces = [];

    /**
     * @return WorkspaceConfig[][]
     */
    protected $dataPoolWorkspaces = [];

    /**
     * @var PermissionService
     */
    protected $permissionService;

    /**
     * @var SecurityService
     */
    protected $securityService;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * AbstractWorkspaceService constructor.
     *
     * @param DataPoolConfigService $dataPoolConfigService
     * @param TokenStorageInterface $tokenStorage
     * @param EventDispatcherInterface $eventDispatcher
     * @param TranslatorService $translatorService
     * @param PermissionService $permissionService
     * @param SecurityService $securityService
     * @param RequestStack $requestStack
     */
    public function __construct(
        DataPoolConfigService $dataPoolConfigService,
        TokenStorageInterface $tokenStorage,
        EventDispatcherInterface $eventDispatcher,
        TranslatorService $translatorService,
        PermissionService $permissionService,
        SecurityService $securityService,
        RequestStack $requestStack
    ) {
        $this->dataPoolConfigService = $dataPoolConfigService;
        $this->tokenStorage = $tokenStorage;
        $this->eventDispatcher = $eventDispatcher;
        $this->translatorService = $translatorService;
        $this->permissionService = $permissionService;
        $this->securityService = $securityService;
        $this->requestStack = $requestStack;
    }

    /**
     * @return BoolQuery|null
     *
     * @throws \Exception
     */
    public function getElasticSearchWorkspaceQuery(string $permission = Permission::VIEW)
    {
        /** @var BoolQuery|null $elasticSearchWorkspaceQuery */
        $elasticSearchWorkspaceQuery = null;

        $dataPoolWorkspaceQuery = $this->createWorkspacesQuery($this->getDataPoolWorkspaces(), $permission);
        $userWorkspaceQuery = $this->createWorkspacesQuery($this->getUserWorkspaces(), $permission);

        $boolQuery = new BoolQuery;
        if ($dataPoolWorkspaceQuery) {
            $boolQuery->add($dataPoolWorkspaceQuery, BoolQuery::FILTER);
        }
        if ($userWorkspaceQuery) {
            $boolQuery->add($userWorkspaceQuery, BoolQuery::FILTER);
        }

        return $this->filterEmptyBoolQuery($boolQuery);
    }

    /**
     * @param WorkspaceConfig[] $workspaces
     *
     * @return string
     */
    public function getRootPathFromWorkspaces(array $workspaces): string
    {
        $rootPath = null;
        foreach ($workspaces as $workspace) {
            if (!$workspace->getPermissionView()) {
                continue;
            }
            $workspacePath = $workspace->getFullPath();
            if (is_null($rootPath)) {
                $rootPath = $workspacePath;
            } else {
                $rootPath = $this->getCommonStartPath($rootPath, $workspacePath);
            }
        }

        return !empty($rootPath) ? $rootPath : '/';
    }

    protected function getCommonStartPath(string $path1, string $path2)
    {
        $path1 = explode('/', $path1);
        $path2 = explode('/', $path2);

        $resultPathParts = [];
        foreach ($path1 as $i => $pathPart) {
            if (!isset($path2[$i]) || $path2[$i] != $pathPart) {
                break;
            }

            $resultPathParts[] = $pathPart;
        }

        return implode('/', $resultPathParts);
    }

    /**
     * @param string $rootPath
     *
     * @return string
     */
    public function getRootNameFromRootPath(string $rootPath): string
    {
        if ($rootPath === '/') {
            return $this->translatorService->translate('Home', 'tree');
        }
        $parts = explode('/', $rootPath);

        return $parts[sizeof($parts) - 1];
    }

    /**
     * @param WorkspaceConfig[] $workspaces
     *
     * @return null|BoolQuery
     */
    protected function createWorkspacesQuery(array $workspaces, string $permission)
    {
        $elasticSearchWorkspaceQuery = new BoolQuery();

        $hasAllowedWorkspaces = false;
        foreach ($workspaces as $workspace) {
            if ($this->permissionService->checkPermissionOfWorkspace($workspace, $permission)) {
                $hasAllowedWorkspaces = true;
                break;
            }
        }

        if ($hasAllowedWorkspaces) {
            foreach ($workspaces as $workspace) {
                $fullPathField = ElasticSearchFields::SYSTEM_FIELDS . '.' . ElasticSearchFields::SYSTEM_FIELDS_FULL_PATH;

                if ($this->permissionService->checkPermissionOfWorkspace($workspace, $permission) && $workspace->getFullPath() === '/') {
                    // elasticsearch path hierarchy tokenizer is not able to find the root path, therefore this dummy query is added
                    $elasticSearchWorkspaceQuery->add(new ExistsQuery($fullPathField), BoolQuery::SHOULD);
                } elseif ($this->permissionService->checkPermissionOfWorkspace($workspace, $permission)) {
                    $elasticSearchWorkspaceQuery->add(new TermQuery($fullPathField, $workspace->getFullPath()), BoolQuery::SHOULD);
                } else {
                    $elasticSearchWorkspaceQuery->add(new TermQuery($fullPathField, $workspace->getFullPath()), BoolQuery::MUST_NOT);
                }
            }
        } else { // do not show anything if no workspaces are configured.
            $termQuery = new TermQuery(ElasticSearchFields::SYSTEM_FIELDS . '.' . ElasticSearchFields::SYSTEM_FIELDS_FULL_PATH, -1);
            $elasticSearchWorkspaceQuery->add($termQuery, BoolQuery::MUST);
        }

        return $this->filterEmptyBoolQuery($elasticSearchWorkspaceQuery);
    }

    /**
     * @param BoolQuery $boolQuery
     *
     * @return null|BoolQuery
     */
    private function filterEmptyBoolQuery(BoolQuery $boolQuery)
    {
        if (empty($boolQuery->toArray())) {
            return null;
        }

        return $boolQuery;
    }

    /**
     * @inheritDoc
     */
    public function getDataPoolWorkspaces(bool $forceWorkspacesRefresh = false, ?DataPoolConfigInterface $dataPoolConfig = null): array
    {
        $dataPoolConfig = $dataPoolConfig ?: $this->dataPoolConfigService->getCurrentDataPoolConfig();

        if ($forceWorkspacesRefresh || !isset($this->dataPoolWorkspaces[$dataPoolConfig->getId()])) {
            $workspaces = $dataPoolConfig->getWorkspaces();
            $workspaces = $this->dispatchResolveDataPoolWorkspaceEvent($workspaces, $dataPoolConfig);
            $this->sortWorkspaces($workspaces);
            $this->dataPoolWorkspaces[$dataPoolConfig->getId()] = $workspaces;
        }

        return $this->dataPoolWorkspaces[$dataPoolConfig->getId()];
    }

    /**
     * @param WorkspaceConfig[] $workspaceConfigs
     * @param DataPoolConfigInterface|null $dataPoolConfig
     *
     * @return $this
     */
    public function setDataPoolWorkspaces(array $workspaceConfigs, ?DataPoolConfigInterface $dataPoolConfig = null)
    {
        $dataPoolConfig = $dataPoolConfig ?: $this->dataPoolConfigService->getCurrentDataPoolConfig();

        $this->dataPoolWorkspaces[$dataPoolConfig->getId()] = $workspaceConfigs;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getUserWorkspaces(PortalUserInterface $user = null, bool $forceWorkspacesRefresh = false, ?DataPoolConfigInterface $dataPoolConfig = null): array
    {
        $dataPoolConfig = $dataPoolConfig ?: $this->dataPoolConfigService->getCurrentDataPoolConfig();

        if ($forceWorkspacesRefresh || !isset($this->userWorkspaces[$dataPoolConfig->getId()])) {
            $workspaces = [];

            $user = $user ?? $this->securityService->getPortalUser();

            if ($user instanceof PortalUserInterface) {
                if ($user->getAdmin()) {
                    return [$this->createAllowAllWorkspaceConfig()];
                }

                if ($user->isPortalShareUser()) {
                    return [$this->createPublicShareWorkspaceConfig()];
                }

                $allWorkspaceDefinitions = $this->getWorkspaceDefinitionFromUser($user);

                foreach ($user->getGroups() ?? [] as $userGroup) {
                    if ($groupWorkspaces = $this->getWorkspaceDefinitionFromUserGroup($userGroup)) {
                        $allWorkspaceDefinitions = array_merge($allWorkspaceDefinitions, $groupWorkspaces);
                    }
                }

                foreach ($allWorkspaceDefinitions ?? [] as $workspace) {
                    if ($element = $workspace->getElement()) {
                        $workspaces[] = $this->createWorkspaceFromElementMetadata($element, $workspace);
                    }
                }
            }

            $workspaces = $this->dispatchResolveUserWorkspaceEvent($workspaces, $user);
            $this->sortWorkspaces($workspaces);
            $this->userWorkspaces[$dataPoolConfig->getId()] = $workspaces;
        }

        return $this->userWorkspaces[$dataPoolConfig->getId()];
    }

    /**
     * @return ElementMetadata[]
     */
    abstract protected function getWorkspaceDefinitionFromUser(PortalUserInterface $user);

    /**
     * @return ElementMetadata[]
     */
    abstract protected function getWorkspaceDefinitionFromUserGroup(PortalUserGroupInterface $userGroup);

    /**
     * @param WorkspaceConfig[] $workspaces
     * @param DataPoolConfigInterface $dataPoolConfig
     *
     * @return WorkspaceConfig[]
     */
    abstract protected function dispatchResolveDataPoolWorkspaceEvent(array $workspaces, DataPoolConfigInterface $dataPoolConfig): array;

    /**
     * @param ElementInterface $element
     * @param ElementMetadata $elementMetadata
     *
     * @return WorkspaceConfig
     */
    protected function createWorkspaceFromElementMetadata(ElementInterface $element, ElementMetadata $elementMetadata): WorkspaceConfig
    {
        return new WorkspaceConfig(
            $element->getRealFullPath(),
            (bool)$elementMetadata->getPermission_view(),
            (bool)$elementMetadata->getPermission_download()
        );
    }

    /**
     * @param WorkspaceConfig[] $workspaces
     * @param PortalUserInterface $user
     *
     * @return WorkspaceConfig[]
     */
    abstract protected function dispatchResolveUserWorkspaceEvent(array $workspaces, PortalUserInterface $user): array;

    protected function createAllowAllWorkspaceConfig(): WorkspaceConfig
    {
        return new WorkspaceConfig(
            '/',
            true,
            true
        );
    }

    protected function createPublicShareWorkspaceConfig(): WorkspaceConfig
    {
        return $this->createAllowAllWorkspaceConfig();
    }

    /**
     * @param WorkspaceConfig[] $workspaces
     *
     * @return void
     */
    public function sortWorkspaces(array &$workspaces): void
    {
        usort($workspaces, function (WorkspaceConfig $a, WorkspaceConfig $b) {
            return strcmp($b->getFullPath(), $a->getFullPath());
        });
    }
}
