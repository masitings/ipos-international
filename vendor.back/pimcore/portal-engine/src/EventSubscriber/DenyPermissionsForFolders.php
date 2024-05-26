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

namespace Pimcore\Bundle\PortalEngineBundle\EventSubscriber;

use Pimcore\Bundle\PortalEngineBundle\Event\Permission\AbstractEvent\DataPoolItemPermissionEvent;
use Pimcore\Bundle\PortalEngineBundle\Event\Permission\DataPoolDeleteItemEvent;
use Pimcore\Bundle\PortalEngineBundle\Event\Permission\DataPoolSubfolderItemEvent;
use Pimcore\Bundle\PortalEngineBundle\Event\Permission\DataPoolUpdateItemEvent;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\AssetConfig;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataPoolConfigInterface;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DenyPermissionsForFolders implements EventSubscriberInterface
{
    protected $dataPoolConfigService;

    public function __construct(DataPoolConfigService $dataPoolConfigService)
    {
        $this->dataPoolConfigService = $dataPoolConfigService;
    }

    public static function getSubscribedEvents()
    {
        return [
            DataPoolUpdateItemEvent::class => 'denyAccessForRootAndUploadFolder',
            DataPoolDeleteItemEvent::class => 'denyAccessForRootAndUploadFolder',
            DataPoolSubfolderItemEvent::class => 'denyAccessForUploadFolderOnly',
        ];
    }

    public function denyAccessForRootAndUploadFolder(DataPoolItemPermissionEvent $event)
    {
        $config = $this->dataPoolConfigService->getDataPoolConfigById($event->getDataPoolId());

        $this->denyAccessForRootFolder($config, $event);
        $this->denyAccessForUploadFolder($config, $event);
    }

    public function denyAccessForUploadFolderOnly(DataPoolItemPermissionEvent $event)
    {
        $config = $this->dataPoolConfigService->getDataPoolConfigById($event->getDataPoolId());

        $this->denyAccessForUploadFolder($config, $event);
    }

    protected function denyAccessForRootFolder(DataPoolConfigInterface $dataPoolConfig, DataPoolItemPermissionEvent $event)
    {
        $workspaces = $dataPoolConfig->getWorkspaces();

        if (!empty($workspaces)) {
            foreach ($workspaces as $workspace) {
                if ($workspace->getFullPath() === $event->getSubjectFullPath()) {
                    $event->setAllowed(false);

                    return;
                }
            }
        }
    }

    protected function denyAccessForUploadFolder(DataPoolConfigInterface $dataPoolConfig, DataPoolItemPermissionEvent $event)
    {
        if (
            !$dataPoolConfig instanceof AssetConfig ||
            !$dataPoolConfig->getUploadFolder() ||
            $dataPoolConfig->getUploadFolder()->getRealFullPath() !== $event->getSubjectFullPath()
        ) {
            return;
        }

        $event->setAllowed(false);
    }
}
