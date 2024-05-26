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

use Pimcore\AssetMetadataClassDefinitionsBundle\Event\AssetMetadataConfigurationEvents;
use Pimcore\AssetMetadataClassDefinitionsBundle\Event\Model\Asset\ConfigurationEvent;
use Pimcore\Bundle\PortalEngineBundle\Enum\Index\DatabaseConfig;
use Pimcore\Bundle\PortalEngineBundle\Enum\Index\Statistics\ElasticSearchAlias;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Asset\IndexService as AssetIndexService;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\DataObject\IndexService as DataObjectIndexService;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\IndexQueueService;
use Pimcore\Event\AssetEvents;
use Pimcore\Event\DataObjectClassDefinitionEvents;
use Pimcore\Event\DataObjectEvents;
use Pimcore\Event\Model\AssetEvent;
use Pimcore\Event\Model\DataObject\ClassDefinitionEvent;
use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Event\Model\TagEvent;
use Pimcore\Event\TagEvents;
use Pimcore\Logger;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\Element\Service;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class IndexUpdateSubscriber
 *
 * @package Pimcore\Bundle\PortalEngineBundle\EventSubsriber
 */
class IndexUpdateSubscriber implements EventSubscriberInterface
{
    /** @var IndexQueueService */
    protected $indexQueueService;
    /** @var DataObjectIndexService */
    protected $dataObjectIndexService;
    /** @var AssetIndexService */
    protected $assetIndexService;

    /**
     * IndexUpdateSubscriber constructor.
     *
     * @param IndexQueueService $indexQueueService
     * @param DataObjectIndexService $dataObjectIndexService
     * @param AssetIndexService $assetIndexService
     */
    public function __construct(IndexQueueService $indexQueueService, DataObjectIndexService $dataObjectIndexService, AssetIndexService $assetIndexService)
    {
        $this->indexQueueService = $indexQueueService;
        $this->dataObjectIndexService = $dataObjectIndexService;
        $this->assetIndexService = $assetIndexService;
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
            DataObjectEvents::POST_UPDATE => 'updateDataObject',
            DataObjectEvents::POST_ADD => 'updateDataObject',
            DataObjectEvents::PRE_DELETE => 'deleteDataObject',
            DataObjectClassDefinitionEvents::POST_UPDATE => 'updateDataObjectMapping',
            DataObjectClassDefinitionEvents::POST_ADD => 'addDataObjectMapping',
            DataObjectClassDefinitionEvents::POST_DELETE => 'deleteDataObjectIndex',
            AssetEvents::POST_UPDATE => 'updateAsset',
            AssetEvents::POST_ADD => 'updateAsset',
            AssetEvents::POST_DELETE => 'deleteAsset',
            AssetMetadataConfigurationEvents::POST_UPDATE => 'updateAssetMapping',
            AssetMetadataConfigurationEvents::POST_DELETE => 'updateAssetMapping',
            TagEvents::PRE_DELETE => 'deleteTag',
            TagEvents::POST_ADD_TO_ELEMENT => 'updateTagAssignment',
            TagEvents::POST_REMOVE_FROM_ELEMENT => 'updateTagAssignment',
        ];
    }

    /**
     * @param DataObjectEvent $event
     */
    public function updateDataObject(DataObjectEvent $event)
    {
        $inheritanceBackup = AbstractObject::getGetInheritedValues();
        AbstractObject::setGetInheritedValues(true);

        $dataObject = $event->getObject();
        if ($dataObject instanceof AbstractObject) {
            $this->indexQueueService->updateIndexQueue($dataObject, DatabaseConfig::QUEUE_TABLE_COLUMN_OPERATION_UPDATE, true);
        }

        AbstractObject::setGetInheritedValues($inheritanceBackup);
    }

    /**
     * @param DataObjectEvent $event
     */
    public function deleteDataObject(DataObjectEvent $event)
    {
        $dataObject = $event->getObject();
        if ($dataObject instanceof AbstractObject) {
            $this->indexQueueService->updateIndexQueue($dataObject, DatabaseConfig::QUEUE_TABLE_COLUMN_OPERATION_DELETE, true);
        }
    }

    /**
     * @param ClassDefinitionEvent $event
     */
    public function addDataObjectMapping(ClassDefinitionEvent $event)
    {
        $classDefinition = $event->getClassDefinition();
        $this->dataObjectIndexService->updateMapping($classDefinition, true);
        $this->dataObjectIndexService->addClassDefinitionToAlias($classDefinition, ElasticSearchAlias::CLASS_DEFINITIONS);
    }

    /**
     * @param ClassDefinitionEvent $event
     */
    public function updateDataObjectMapping(ClassDefinitionEvent $event)
    {
        $classDefinition = $event->getClassDefinition();
        $this->dataObjectIndexService->updateMapping($classDefinition);
        $this->indexQueueService->updateDataObjects($classDefinition);
        $this->dataObjectIndexService->addClassDefinitionToAlias($classDefinition, ElasticSearchAlias::CLASS_DEFINITIONS);
    }

    /**
     * @param ClassDefinitionEvent $event
     */
    public function deleteDataObjectIndex(ClassDefinitionEvent $event)
    {
        $classDefinition = $event->getClassDefinition();
        try {
            $this->dataObjectIndexService->deleteIndex($classDefinition);
        } catch (\Exception $e) {
            Logger::err($e);
        }
        $this->dataObjectIndexService->removeClassDefinitionFromAlias($classDefinition, ElasticSearchAlias::CLASS_DEFINITIONS);
    }

    /**
     * @param AssetEvent $event
     */
    public function updateAsset(AssetEvent $event)
    {
        $asset = $event->getAsset();
        if ($asset instanceof Asset) {
            $this->indexQueueService->updateIndexQueue($asset, DatabaseConfig::QUEUE_TABLE_COLUMN_OPERATION_UPDATE, true);
        }
    }

    /**
     * @param AssetEvent $event
     */
    public function deleteAsset(AssetEvent $event)
    {
        $asset = $event->getAsset();
        if ($asset instanceof Asset) {
            $this->indexQueueService->updateIndexQueue($asset, DatabaseConfig::QUEUE_TABLE_COLUMN_OPERATION_DELETE, true);
        }
    }

    /**
     * @param ConfigurationEvent $event
     */
    public function updateAssetMapping(ConfigurationEvent $event)
    {
        $this->assetIndexService->updateMapping();
        $this->indexQueueService->updateAssets();
    }

    public function deleteTag(TagEvent $event)
    {
        $this->indexQueueService->updateByTag($event->getTag());
    }

    public function updateTagAssignment(TagEvent $event)
    {
        $element = Service::getElementById($event->getArgument('elementType'), $event->getArgument('elementId'));
        $this->indexQueueService->updateIndexQueue($element, DatabaseConfig::QUEUE_TABLE_COLUMN_OPERATION_UPDATE, true);
    }
}
