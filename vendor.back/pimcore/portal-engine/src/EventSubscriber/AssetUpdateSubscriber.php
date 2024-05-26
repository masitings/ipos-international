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

use Pimcore\Bundle\PortalEngineBundle\Service\StatisticsTracker\Elasticsearch\AssetUpdateTracker;
use Pimcore\Event\AssetEvents;
use Pimcore\Event\Model\AssetEvent;
use Pimcore\Model\Asset;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class AssetUpdateSubscriber
 *
 * @package Pimcore\Bundle\PortalEngineBundle\EventSubscriber
 */
class AssetUpdateSubscriber implements EventSubscriberInterface
{
    /** @var AssetUpdateTracker */
    protected $assetUpdateTracker;

    /**
     * AssetUpdateSubscriber constructor.
     *
     * @param AssetUpdateTracker $assetUpdateTracker
     */
    public function __construct(AssetUpdateTracker $assetUpdateTracker)
    {
        $this->assetUpdateTracker = $assetUpdateTracker;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * ['eventName' => 'methodName']
     *  * ['eventName' => ['methodName', $priority]]
     *  * ['eventName' => [['methodName1', $priority], ['methodName2']]]
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            AssetEvents::POST_UPDATE => 'updateAsset',
        ];
    }

    /**
     * @param AssetEvent $event
     */
    public function updateAsset(AssetEvent $event)
    {
        $asset = $event->getAsset();
        if ($asset instanceof Asset) {
            $this->assetUpdateTracker->trackEvent(['asset' => $asset]);
        }
    }
}
