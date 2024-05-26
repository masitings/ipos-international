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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Download;

use Pimcore\Bundle\PortalEngineBundle\Entity\PublicShare;
use Pimcore\Bundle\PortalEngineBundle\Enum\Permission;
use Pimcore\Bundle\PortalEngineBundle\Event\Download\DownloadSourcesEvent;
use Pimcore\Bundle\PortalEngineBundle\Event\Download\DownloadTypesEvent;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataPoolConfigInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadAccess;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadConfig;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadType;
use Pimcore\Bundle\PortalEngineBundle\Service\Download\Provider\DownloadProviderInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class DownloadProviderService
{
    protected $eventDispatcher;
    protected $authorizationChecker;

    public function __construct(EventDispatcherInterface $eventDispatcher, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @var DownloadProviderInterface[]
     */
    protected $providers = [];

    /**
     * @param DownloadProviderInterface $downloadProvider
     */
    public function addDownloadProvider(DownloadProviderInterface $downloadProvider)
    {
        $this->providers[] = $downloadProvider;
    }

    /**
     * @param DataPoolConfigInterface $config
     * @param bool $checkPermissions
     *
     * @return DownloadType[]
     */
    public function getDownloadTypes(DataPoolConfigInterface $config, bool $checkPermissions = true): array
    {
        $downloadTypes = [];

        foreach ($this->providers as $provider) {
            if (!$provider->canProvide($config)) {
                continue;
            }

            $downloadTypes = array_filter(array_merge($downloadTypes, $provider->provide($config, $checkPermissions)));
        }

        if ($checkPermissions) {
            $downloadTypes = array_values(array_filter($downloadTypes, function (DownloadType $downloadType) use ($config) {
                return $this->authorizationChecker->isGranted(Permission::DOWNLOAD, DownloadAccess::fromDownloadType($config->getId(), $downloadType));
            }));
        }

        $event = new DownloadTypesEvent($config, $downloadTypes);
        $this->eventDispatcher->dispatch($event);

        return $event->getDownloadTypes();
    }

    /**
     * @param DataPoolConfigInterface $dataPoolConfig
     * @param PublicShare $publicShare
     *
     * @return DownloadType[]
     */
    public function getPublicShareAllowedDownloadTypes(DataPoolConfigInterface $dataPoolConfig, PublicShare $publicShare)
    {
        $downloadTypes = [];
        $configs = $publicShare->getConfigs();
        $dataPoolDownloadConfigs = $configs[$dataPoolConfig->getId()] ?? [];

        if (!empty($dataPoolDownloadConfigs)) {
            /**
             * @var DownloadConfig[] $dataPoolDownloadConfigs
             */
            $dataPoolDownloadConfigs = array_map(function ($dataPoolDownloadConfig) {
                $config = new DownloadConfig();
                $config->add($dataPoolDownloadConfig);

                return $config;
            }, $dataPoolDownloadConfigs);

            $potentialDownloadTypes = $this->getDownloadTypes($dataPoolConfig, false);

            foreach ($potentialDownloadTypes as $downloadType) {
                foreach ($dataPoolDownloadConfigs as $dataPoolDownloadConfig) {
                    if (
                        $dataPoolDownloadConfig->getType() == $downloadType->getType()
                        && $dataPoolDownloadConfig->getAttribute() == $downloadType->getAttribute()
                        && $dataPoolDownloadConfig->getLocalized() == $downloadType->getLocalized()
                    ) {
                        $formats = $downloadType->getFormats();
                        $formats = array_filter($formats, function ($format) use ($dataPoolDownloadConfig) {
                            return $format['id'] === $dataPoolDownloadConfig->getFormat();
                        });
                        $formats = array_values($formats);
                        if (sizeof($formats)) {
                            $downloadType->setFormats($formats);
                            $downloadTypes[] = $downloadType;
                        }
                    }
                }
            }
        }

        return $downloadTypes;
    }

    /**
     * @param DownloadConfig $config
     * @param $source
     *
     * @return array
     */
    public function getSources(DataPoolConfigInterface $dataPoolConfig, DownloadConfig $config, $source)
    {
        $sources = [];

        foreach ($this->providers as $provider) {
            if (!$provider->canExtractSource($dataPoolConfig, $config, $source)) {
                continue;
            }

            $extractedSource = $provider->extractSource($dataPoolConfig, $config, $source);

            if (!is_array($extractedSource)) {
                $extractedSource = [$extractedSource];
            }

            $sources = array_merge($sources, array_filter($extractedSource));
        }

        $event = new DownloadSourcesEvent($dataPoolConfig, $config, $source, $sources);
        $this->eventDispatcher->dispatch($event);

        return $event->getSources();
    }

    /**
     * @return DownloadProviderInterface[]
     */
    public function getProviders(): array
    {
        return $this->providers;
    }
}
