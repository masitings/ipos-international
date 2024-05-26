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

use Pimcore\Event\Admin\IndexActionSettingsEvent;
use Pimcore\Event\AdminEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AdminSettingsSubscriber implements EventSubscriberInterface
{
    /**
     * @var array
     */
    protected $possiblePortalDomains = [];

    public function __construct(array $possiblePortalDomains)
    {
        $this->possiblePortalDomains = $possiblePortalDomains;
    }

    public static function getSubscribedEvents()
    {
        return [
            AdminEvents::INDEX_ACTION_SETTINGS => 'getSettings',
        ];
    }

    public function getSettings(IndexActionSettingsEvent $event)
    {
        if (!empty($this->possiblePortalDomains)) {
            $event->addSetting('portalEngine', ['possiblePortalDomains' => $this->possiblePortalDomains]);
        }
    }
}
