# Extending Search Index

The portal engine is powered by Elasticsearch for search, listings and filters. Therefore, it's needed to store the data 
from Pimcore elements into Elasticsearch indices.

Take a look at [Index Management](../../05_Administration_of_Portals/10_Index_Management.md) for an introduction on how 
to index the data into Elasticsearch and keep it up to date. There you will find also more information about the structure 
of the indices.


## Extending Search Index via Events

The regular index update process stores a defined set of standard data types in the Elasticsearch index which makes it 
possible to find, filter, sort and list them in the portal engine.

It is possible to extend the index with custom attributes if needed. For this purpose the following events exist. You 
will find code examples at the end of this section.


### UpdateIndexDataEvent

This event can be used to store additional fields in the search index. Depending on if you would like to index additional 
data for assets or data objects use one of the following two events.

* `Pimcore\Bundle\PortalEngineBundle\Event\Asset\UpdateIndexDataEvent`
* `Pimcore\Bundle\PortalEngineBundle\Event\DataObject\UpdateIndexDataEvent`

If you take a look at the source of a portal engine document within Elasticsearch you will find a structure like this:

```json
{
          "system_fields" : {
            "id" : 145,
            "creationDate" : "2019-05-24T15:42:20+0200",
            "modificationDate" : "2019-08-23T15:15:54+0200",
            "type" : "image",
            "key" : "abandoned-automobile-automotive-1082654.jpg",
            ...
          },
          "standard_fields" : [ ... ],
          "custom_fields" : [ ]
}
```

This is used to separate the data into three sections:

###### system_fields

Base system fields which are the same for all assets or data objects (like id, creationDate, fullPath...).

###### standard_fields

All data object or asset metadata types which are supported out of the box depending on your data model.

###### custom_fields

This is the place where you are able to add data via the `UpdateIndexDataEvent`. As soon as additional fields are added 
they are searchable through the full text search (depending on the mapping of the fields).


### ExtractMappingEvent

With this event it's possible to define the [mapping](https://www.elastic.co/guide/en/elasticsearch/reference/current/mapping.html) 
of the additional custom fields. Again there are separate events for assets and data objects.

* `Pimcore\Bundle\PortalEngineBundle\Event\Asset\ExtractMappingEvent`
* `Pimcore\Bundle\PortalEngineBundle\Event\DataObject\ExtractMappingEvent`


### FilterableFieldsEvent

Now as the mapping is defined and the data is indexed, it is possible to create a filter on this data. The 
`Pimcore\Bundle\PortalEngineBundle\Event\Search\FilterableFieldsEvent` can be used to make the new fields appear in the 
filter field selection of the [data pool config document](../../05_Administration_of_Portals/05_Configuration/README.md).


### SortableFieldsEvent

The `Pimcore\Bundle\PortalEngineBundle\Event\Search\SortableFieldsEvent` works quite the same way like the `FilterableFieldsEvent` 
but defines fields which can appear in the list of sortable fields.


### ListableFieldsEvent

The last event is the `Pimcore\Bundle\PortalEngineBundle\Event\Search\ListableFieldsEvent`. This can be used if you would 
like to display the additional fields in the list view of the data pool listings.


### Example 1: Assets

The following example creates an EventSubscriber which adds an additional field to make it listable, filterable and 
sortable. The logic applies to assets and divides the assets into file size groups:

* small: < 300KB
* medium: 300KB - 3MB
* big: > 3MB

```php
<?php

namespace AppBundle\EventListener;

use Pimcore\Bundle\PortalEngineBundle\Event\Asset\ExtractMappingEvent;
use Pimcore\Bundle\PortalEngineBundle\Event\Asset\UpdateIndexDataEvent;
use Pimcore\Bundle\PortalEngineBundle\Event\Search\FilterableFieldsEvent;
use Pimcore\Bundle\PortalEngineBundle\Event\Search\ListableFieldsEvent;
use Pimcore\Bundle\PortalEngineBundle\Event\Search\SortableFieldsEvent;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\AssetConfig;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataPoolConfigInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Field\FilterableField;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Field\ListableField;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Field\SortableField;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\Asset;
use Pimcore\Model\Asset\Folder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PortalEngineFileSizeIndexSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            UpdateIndexDataEvent::class  => 'onUpdateIndexData',
            ExtractMappingEvent::class   => 'onExtractMapping',
            FilterableFieldsEvent::class => 'onGetFilterableFields',
            SortableFieldsEvent::class   => 'onGetSortableFields',
            ListableFieldsEvent::class   => 'onGetListableFields',
        ];
    }

    public function onUpdateIndexData(UpdateIndexDataEvent $event)
    {
        $asset = $event->getAsset();
        if($asset instanceof Folder) {
            return;
        }

        // Ensure that you take the original array and extend it.
        $customFields = $event->getCustomFields();

        $fileSize = $event->getAsset()->getFileSize();
        $fileSizeSelection = null;
        if($fileSize < 3*1000) {
            $fileSizeSelection = 'small';
        } elseif($fileSize <= 3*1000*1000) {
            $fileSizeSelection = 'medium';
        } else {
            $fileSizeSelection = 'big';
        }

        $customFields['fileSizeSelection'] = $fileSizeSelection;

        $event->setCustomFields($customFields);
    }

    public function onExtractMapping(ExtractMappingEvent $event)
    {
        // Ensure that you take the original array and extend it.
        $customFieldsMapping = $event->getCustomFieldsMapping();

        /**
         * Take a look at the Elasticsearch docs how mapping works.
         * A 'keyword' field would be best for regular select and multi select filters.
         * For full text search it is possible to define sub fields with special Elasticsearch analyzers too.
         */
        $customFieldsMapping['fileSizeSelection'] = [
            'type' => 'keyword'
        ];

        $event->setCustomFieldsMapping($customFieldsMapping);
    }

    public function onGetFilterableFields(FilterableFieldsEvent $event)
    {
        // should be visible for asset pools only
        if(!$event->getDataPoolConfig() instanceof AssetConfig) {
            return;
        }

        // This array contains all filterable fields. Therefore it would be possible to remove fields too if needed.
        $filterableFields = $event->getFilterableFields();

        $filterableFields[] = (new FilterableField())
            # technical name (will be used for GET parameter and translation key)
            ->setName('fileSizeSelection')
            # nice name for the select list
            ->setTitle('File Size Selection')
            # full path of the field within the Elasticsearch document
            ->setPath('custom_fields.fileSizeSelection');

        $event->setFilterableFields($filterableFields);
    }

    public function onGetSortableFields(SortableFieldsEvent $event)
    {
        // should be visible for asset pools only
        if(!$event->getDataPoolConfig() instanceof AssetConfig) {
            return;
        }

        // This array contains all sortable fields. Therefore it would be possible to remove fields too if needed.
        $sortableFields = $event->getSortableFields();

        $sortableFields[] = (new SortableField())
            # technical name (will be used for GET parameter and translation key)
            ->setName('fileSizeSelection')
            # nice name
            ->setTitle('File Size Selection')
            # full path of the field within the Elasticsearch document
            ->setPath('custom_fields.fileSizeSelection');

        $event->setSortableFields($sortableFields);
    }

    public function onGetListableFields(ListableFieldsEvent $event)
    {
        // should be visible for asset pools only
        if(!$event->getDataPoolConfig() instanceof AssetConfig) {
            return;
        }

        // This array contains all listable fields. Therefore it would be possible to remove fields too if needed.
        $listableFields = $event->getListableFields();

        $listableFields[] = (new ListableField())
            # technical name (will be used for GET parameter and translation key)
            ->setName('fileSizeSelection')
            # nice name
            ->setTitle('File Size Selection')
            # full path of the field within the Elasticsearch document
            ->setPath('custom_fields.fileSizeSelection');

        $event->setListableFields($listableFields);
    }
}


```

```yaml
# service definition

services:
    _defaults:
        autowire: true

    AppBundle\EventListener\PortalEngineFileSizeIndexSubscriber:
        tags:
            - { name: kernel.event_subscriber }
```


### Example 2: Data Objects

In this example a "User Owner" field will be provided for car data pool documents. "Owner" is defined as Pimcore user 
name of the creator of the car data object.


```php
<?php

namespace AppBundle\EventListener;

use Pimcore\Bundle\PortalEngineBundle\Event\DataObject\ExtractMappingEvent;
use Pimcore\Bundle\PortalEngineBundle\Event\DataObject\UpdateIndexDataEvent;
use Pimcore\Bundle\PortalEngineBundle\Event\Search\FilterableFieldsEvent;
use Pimcore\Bundle\PortalEngineBundle\Event\Search\ListableFieldsEvent;
use Pimcore\Bundle\PortalEngineBundle\Event\Search\SortableFieldsEvent;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataObjectConfig;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataPoolConfigInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Field\FilterableField;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Field\ListableField;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Field\SortableField;
use Pimcore\Model\DataObject\Car;
use Pimcore\Model\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PortalEngineCarOwnerSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            UpdateIndexDataEvent::class  => 'onUpdateIndexData',
            ExtractMappingEvent::class   => 'onExtractMapping',
            FilterableFieldsEvent::class => 'onGetFilterableFields',
            SortableFieldsEvent::class   => 'onGetSortableFields',
            ListableFieldsEvent::class   => 'onGetListableFields',
        ];
    }

    public function onUpdateIndexData(UpdateIndexDataEvent $event)
    {
        $car = $event->getDataObject();
        if(!$car instanceof Car) {
            return;
        }

        // Ensure that you take the original array and extend it.
        $customFields = $event->getCustomFields();

        $owner = User::getById($car->getUserOwner());

        $customFields['userOwner'] = $owner ? $owner->getName() : 'system';

        $event->setCustomFields($customFields);
    }

    public function onExtractMapping(ExtractMappingEvent $event)
    {
        if($event->getClassDefinition()->getId() !== 'CAR') {
            return;
        }

        // Ensure that you take the original array and extend it.
        $customFieldsMapping = $event->getCustomFieldsMapping();

        /**
         * Take a look at the Elasticsearch docs how mapping works.
         * A 'keyword' field would be best for regular select and multi select filters.
         * For full text search it is possible to define sub fields with special Elasticsearch analyzers too.
         */
        $customFieldsMapping['userOwner'] = [
            'type' => 'keyword'
        ];

        $event->setCustomFieldsMapping($customFieldsMapping);
    }

    public function onGetFilterableFields(FilterableFieldsEvent $event)
    {
        // should be visible for car data pools only
        if(!$this->isCarDataPool($event->getDataPoolConfig())) {
            return;
        }

        // This array contains all filterable fields. Therefore it would be possible to remove fields too if needed.
        $filterableFields = $event->getFilterableFields();

        $filterableFields[] = (new FilterableField())
            ->setName('userOwner')
            ->setTitle('User Owner')
            ->setPath('custom_fields.userOwner');

        $event->setFilterableFields($filterableFields);
    }

    public function onGetSortableFields(SortableFieldsEvent $event)
    {
        // should be visible for car data pools only
        if(!$this->isCarDataPool($event->getDataPoolConfig())) {
            return;
        }

        // This array contains all sortable fields. Therefore it would be possible to remove fields too if needed.
        $sortableFields = $event->getSortableFields();

        $sortableFields[] = (new SortableField())
            ->setName('owner')
            ->setTitle('User Owner')
            ->setPath('custom_fields.userOwner');

        $event->setSortableFields($sortableFields);
    }

    public function onGetListableFields(ListableFieldsEvent $event)
    {
        // should be visible for car data pools only
        if(!$this->isCarDataPool($event->getDataPoolConfig())) {
            return;
        }

        // This array contains all listable fields. Therefore it would be possible to remove fields too if needed.
        $listableFields = $event->getListableFields();

        $listableFields[] = (new ListableField())
            ->setName('owner')
            ->setTitle('User Owner')
            ->setPath('custom_fields.userOwner');

        $event->setListableFields($listableFields);
    }

    protected function isCarDataPool(DataPoolConfigInterface $dataPoolConfig): bool
    {
        return $dataPoolConfig instanceof DataObjectConfig && $dataPoolConfig->getDataObjectClass() === 'CAR';
    }
}

```

#### Update index mapping and data

Call the following console commands as soon as the event subscriber is set up in the symfony container configuration.

```
bin/console portal-engine:update:index update-asset-index # update asset index mapping and put all assets into the queue
bin/console portal-engine:update:process-index-queue --processes=3 # process index queue with 3 parallel processes
```

If the first command fails with a mapping error please call the following commands to delete and recreate the index 
instead. This might happen as Elasticsearch does not allow field mapping updates under certain circumstances.
```
bin/console portal-engine:update:index-recreate update-asset-index # recreate asset index with new maping and put all assets into the queue
bin/console portal-engine:update:process-index-queue --processes=3 # process index queue with 3 parallel processes
```

#### Configure the new field in data pool configuration document

As soon as all these steps are finished the new field should appear in the select lists of the data pool configuration 
document. Configure the new field there and the new options should appear in the frontend.


## Index Update Console Commands

For more details on how the index update console commands work take a look at the 
[Index Management](../../05_Administration_of_Portals/10_Index_Management.md) section.