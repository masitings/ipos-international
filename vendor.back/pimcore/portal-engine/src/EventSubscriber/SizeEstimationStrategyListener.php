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

use Pimcore\Bundle\PortalEngineBundle\Event\DataObject\ExtractMappingEvent;
use Pimcore\Bundle\PortalEngineBundle\Event\DataObject\UpdateIndexDataEvent;
use Pimcore\Bundle\PortalEngineBundle\Service\Download\SizeEstimation\SizeEstimationStrategyInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SizeEstimationStrategyListener implements EventSubscriberInterface
{
    protected $sizeEstimationStrategy;

    public function __construct(SizeEstimationStrategyInterface $sizeEstimationStrategy)
    {
        $this->sizeEstimationStrategy = $sizeEstimationStrategy;
    }

    public static function getSubscribedEvents()
    {
        return [
            ExtractMappingEvent::class => 'onExtractMapping',
            UpdateIndexDataEvent::class => 'onUpdateIndexData'
        ];
    }

    public function onExtractMapping(ExtractMappingEvent $event)
    {
        $mappings = $event->getCustomFieldsMapping();

        $mappings = array_merge($mappings, $this->sizeEstimationStrategy->getCustomDataObjectMappingForIndex($event->getClassDefinition()));

        $event->setCustomFieldsMapping($mappings);
    }

    public function onUpdateIndexData(UpdateIndexDataEvent $event)
    {
        $data = $event->getCustomFields();

        $event->setCustomFields(array_replace($data, $this->sizeEstimationStrategy->getCustomDataObjectDataForIndex($event->getDataObject())));
    }
}
