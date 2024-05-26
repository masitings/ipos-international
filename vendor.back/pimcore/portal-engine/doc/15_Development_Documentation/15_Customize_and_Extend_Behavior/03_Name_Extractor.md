# Name Extractor for Pimcore Elements

The name extractor is used to extract a name for data objects and assets. This name will be displayed and used at the 
following positions in the frontend:

* Displayed within the data pool listings.
* Displayed for relations of elements on data object detail pages. 
* Used within the full text search to find the element.
* Can be configured as sort option or filter in the data pool configuration document if needed.

By default, the service works as following:

* **Assets**: The file name will be used as name.
* **Data Object**: If the given data object class has a "name" attribute and a name is entered in the data object use it, 
   otherwise the key of the data object will be used as fallback (works with a localized name field too).

It's possible to change this logic through events:

* Pimcore\Bundle\PortalEngineBundle\Event\Asset\ExtractNameEvent
* Pimcore\Bundle\PortalEngineBundle\Event\DataObject\ExtractNameEvent

## Example

This example event subscriber will append the production year to the name of `Car` data objects.

```php
<?php

namespace AppBundle\EventListener;

use Pimcore\Bundle\PortalEngineBundle\Event\DataObject\ExtractNameEvent;
use Pimcore\Model\DataObject\Car;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PortalEngineExtractCarNameSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            ExtractNameEvent::class  => 'onExtractName'
        ];
    }

    public function onExtractName(ExtractNameEvent $event)
    {
        $car = $event->getObject();
        if(!$car instanceof Car) {
            return;
        }

        $name = $car->getName($event->getLocale()) ;

        if($productionYear = $car->getProductionYear()) {
            $name .= ' (' . $productionYear . ')';
        }

        $event->setName($name);
    }
}
```

```yaml
# add this to your container service definition
services:
    AppBundle\EventListener\PortalEngineExtractCarNameSubscriber:
        tags:
            - { name: kernel.event_subscriber }
```

### Update Elasticsearch Index

If you change the logic for the name extraction process do not forget to update the index (at least for the affected elements). 

```
# update all index mappings and put all elements in the queue (restrictable with corresponding command options and arguments)
bin/console portal-engine:update:index

# process index queue with 3 parallel processes
bin/console portal-engine:update:process-index-queue --processes=3
```

