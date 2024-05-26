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

use Pimcore\Bundle\PortalEngineBundle\Service\Collection\CollectionService;
use Pimcore\Event\AssetEvents;
use Pimcore\Event\DataObjectEvents;
use Pimcore\Event\Model\AssetEvent;
use Pimcore\Event\Model\DataObjectEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @package Pimcore\Bundle\PortalEngineBundle\EventListener
 */
class DeleteElementSubscriber implements EventSubscriberInterface
{
    /**
     * @var CollectionService
     */
    protected $collectionService;

    /**
     * @param CollectionService $collectionService
     */
    public function __construct(CollectionService $collectionService)
    {
        $this->collectionService = $collectionService;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            DataObjectEvents::POST_DELETE => 'onDataObjectPostDelete',
            AssetEvents::POST_DELETE => 'onAssetPostDelete',
        ];
    }

    /**
     * @param DataObjectEvent $dataObjectEvent
     *
     * @throws \Exception
     */
    public function onDataObjectPostDelete(DataObjectEvent $dataObjectEvent)
    {
        $object = $dataObjectEvent->getElement();
        $this->collectionService->cleanupDeletedElement($object);
    }

    /**
     * @param DataObjectEvent $dataObjectEvent
     *
     * @throws \Exception
     */
    public function onAssetPostDelete(AssetEvent $assetEvent)
    {
        $asset = $assetEvent->getElement();
        $this->collectionService->cleanupDeletedElement($asset);
    }
}
