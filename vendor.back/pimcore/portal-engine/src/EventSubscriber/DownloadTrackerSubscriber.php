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

use Pimcore\Bundle\PortalEngineBundle\Event\Download\DownloadAssetEvent;
use Pimcore\Bundle\PortalEngineBundle\Service\StatisticsTracker\Elasticsearch\DownloadTracker;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DownloadTrackerSubscriber implements EventSubscriberInterface
{
    protected $downloadTracker;

    /**
     * @param DownloadTracker $downloadTracker
     */
    public function __construct(DownloadTracker $downloadTracker)
    {
        $this->downloadTracker = $downloadTracker;
    }

    public static function getSubscribedEvents()
    {
        return [
            DownloadAssetEvent::class => 'onDownloadAsset',
        ];
    }

    /**
     * @param DownloadAssetEvent $event
     *
     * @throws \Exception
     */
    public function onDownloadAsset(DownloadAssetEvent $event)
    {
        $this->downloadTracker->trackEvent([
            'downloadable' => $event->getDownloadable(),
            'context' => $event->getDownloadContext()
        ]);
    }
}
