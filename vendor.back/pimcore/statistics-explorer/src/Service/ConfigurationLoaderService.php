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

namespace Pimcore\Bundle\StatisticsExplorerBundle\Service;

use Pimcore\Bundle\StatisticsExplorerBundle\Entity\Configuration;
use Pimcore\Bundle\StatisticsExplorerBundle\Entity\ConfigurationShare;
use Pimcore\Bundle\StatisticsExplorerBundle\Events\LoadConfigurationEvent;
use Pimcore\Bundle\StatisticsExplorerBundle\PimcoreStatisticsExplorerBundle;
use Pimcore\Translation\Translator;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ConfigurationLoaderService
{
    const OWNER_SHIP_OWNER = 'owner';
    const OWNER_SHIP_SHARED_BY_USER = 'userShared';
    const OWNER_SHIP_OTHER = 'other';

    /**
     * @var ConfigurationEntityService
     */
    protected $configurationEntityService;

    /**
     * @var UserProviderLocatorService
     */
    protected $userProviderLocator;

    /**
     * @var EntityManagerService
     */
    protected $entityManagerService;

    /**
     * @var Configuration[]
     */
    protected $globalConfigurations;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param ConfigurationEntityService $configurationEntityService
     * @param UserProviderLocatorService $userProviderLocator
     * @param EntityManagerService $entityManagerService
     * @param Translator $translator
     * @param EventDispatcherInterface $eventDispatcher
     * @param array $globalConfigurationsConfig
     */
    public function __construct(ConfigurationEntityService $configurationEntityService, UserProviderLocatorService $userProviderLocator, EntityManagerService $entityManagerService, Translator $translator, EventDispatcherInterface $eventDispatcher, array $globalConfigurationsConfig)
    {
        $this->configurationEntityService = $configurationEntityService;
        $this->userProviderLocator = $userProviderLocator;
        $this->entityManagerService = $entityManagerService;
        $this->translator = $translator;
        $this->eventDispatcher = $eventDispatcher;

        $this->globalConfigurations = [];
        if ($globalConfigurationsConfig) {
            foreach ($globalConfigurationsConfig as $name => $configEntry) {
                $configuration = new Configuration();
                $configuration->setId('global_' . $name);
                $configuration->setName($name);
                $configuration->setContext($configEntry['context']);
                $configuration->setConfiguration($configEntry['configuration']);

                $this->globalConfigurations[$configuration->getId()] = $configuration;
            }
        }
    }

    public function getConfigurationById(string $id): ?Configuration
    {
        return $this->configurationEntityService->getById($id);
    }

    public function getConfigurationByIdOwnerAware(string $id, string $context): ?Configuration
    {
        $userProvider = $this->userProviderLocator->getUserProvider($context);
        $currentUserId = $userProvider->getCurrentUserId();

        return $this->configurationEntityService->getByIdOwnerAware($id, $currentUserId);
    }

    public function getConfigurationByIdPermissionAware(string $id, string $context): ?Configuration
    {
        $userProvider = $this->userProviderLocator->getUserProvider($context);
        $currentUserId = $userProvider->getCurrentUserId();

        $event = new LoadConfigurationEvent($id, $context);
        $this->eventDispatcher->dispatch($event);
        $configuration = $event->getConfiguration();
        if ($configuration) {
            return $configuration;
        }

        if (array_key_exists($id, $this->globalConfigurations)) {
            return $this->globalConfigurations[$id];
        }
        if ($configuration = $this->configurationEntityService->getByIdOwnerAware($id, $currentUserId)) {
            return $configuration;
        }

        return $this->configurationEntityService->getByIdPermissionAware($id, $context, $userProvider->getSharedWithCurrentUserCondition());
    }

    /**
     * @param string $context
     *
     * @throws \Exception
     */
    protected function checkContext(string $context)
    {
        $this->userProviderLocator->getUserProvider($context);
    }

    /**
     * @param string $context
     *
     * @return array
     *
     * @throws \Exception
     */
    public function loadConfigurationsForActiveUser(string $context): array
    {
        $userProvider = $this->userProviderLocator->getUserProvider($context);

        $configurations = [];

        $userId = $userProvider->getCurrentUserId();
        if ($userId) {
            $key = $this->translator->trans(PimcoreStatisticsExplorerBundle::TRANSLATION_PREFIX . 'lbl_own');
            $configurations[$key] = $this->configurationEntityService->getByOwnerId($context, $userId);
        }

        $key = $this->translator->trans(PimcoreStatisticsExplorerBundle::TRANSLATION_PREFIX . 'lbl_global');
        $configurations[$key] = array_merge(array_values($this->globalConfigurations), $this->configurationEntityService->getByOwnerId($context));

        $shareCondition = $userProvider->getSharedWithCurrentUserCondition();
        if ($shareCondition) {
            $key = $this->translator->trans(PimcoreStatisticsExplorerBundle::TRANSLATION_PREFIX . 'lbl_shared');
            $configurations[$key] = $this->configurationEntityService->getSharedWith($context, $shareCondition);
        }

        return $configurations;
    }

    public function deleteConfiguration(Configuration $configuration)
    {
        $this->checkContext($configuration->getContext());

        $currentUserId = $this->userProviderLocator->getUserProvider($configuration->getContext())->getCurrentUserId();
        if ($configuration->getOwnerId() == $currentUserId) {
            $this->configurationEntityService->delete($configuration);
        } else {
            $this->unShareConfiguration($configuration, $currentUserId, 'user');
        }
    }

    public function shareConfiguration(Configuration $configuration, int $shareWithId, string $shareWithType)
    {
        $this->checkContext($configuration->getContext());

        $share = $this->configurationEntityService->getShare($configuration, $shareWithId, $shareWithType);
        if (empty($share)) {
            $share = new ConfigurationShare();
            $share->setConfiguration($configuration);
            $share->setSharedWithId($shareWithId);
            $share->setSharedWithType($shareWithType);
            $this->configurationEntityService->persist($share);
        }
    }

    public function unShareConfiguration(Configuration $configuration, int $sharedWithId, string $sharedWithType)
    {
        $share = $this->configurationEntityService->getShare($configuration, $sharedWithId, $sharedWithType);
        if ($share) {
            $this->configurationEntityService->delete($share);
        }
    }

    public function createOrUpdateConfiguration(Configuration $configuration, array $shareWithIds): Configuration
    {
        $userProvider = $this->userProviderLocator->getUserProvider($configuration->getContext());
        $currentUserId = $userProvider->getCurrentUserId();

        if (empty($configuration->getOwnerId())) {
            $configuration->setOwnerId($currentUserId);
        }

        if ($configuration->getOwnerId() != $currentUserId) {
            throw new \Exception('Owner does not match current user id');
        }

        $this->entityManagerService->getManager()->beginTransaction();

        $this->configurationEntityService->persist($configuration);
        $this->configurationEntityService->cleanupShares($configuration);

        foreach ($shareWithIds as $type => $shareIds) {
            foreach ($shareIds as $shareId) {
                if ($shareId) {
                    $this->shareConfiguration($configuration, $shareId, $type);
                }
            }
        }
        $this->entityManagerService->getManager()->commit();

        return $configuration;
    }

    public function getSharesArray(Configuration $configuration): array
    {
        $sharesArray = [
            'user' => [],
            'role' => []
        ];

        $shares = $this->configurationEntityService->getShares($configuration);
        foreach ($shares as $share) {
            $sharesArray[$share->getSharedWithType()][] = $share->getSharedWithId();
        }

        return $sharesArray;
    }

    public function getOwnerShip(Configuration $configuration): string
    {
        $userProvider = $this->userProviderLocator->getUserProvider($configuration->getContext());
        $currentUserId = $userProvider->getCurrentUserId();

        if ($configuration->getOwnerId() === $currentUserId) {
            return self::OWNER_SHIP_OWNER;
        }

        $sharesArray = $this->getSharesArray($configuration);
        if (in_array($currentUserId, $sharesArray['user'])) {
            return self::OWNER_SHIP_SHARED_BY_USER;
        }

        return self::OWNER_SHIP_OTHER;
    }
}
