# Main Image Extractor for Data Objects

The main image extractor is used to extract the main image of a data object. The main image will be visible for example 
in the data pool listings and in the download cart.

By default, the first found image of the data object will be used (depending on the class definition layout). The 
following Pimcore data types are respected:

* image
* hotspotimage
* imageGallery

## Change Behaviour via Event

The `Pimcore\Bundle\PortalEngineBundle\Event\DataObject\ExtractMainImageEvent` can be used to customize the logic.

### Example

```php
<?php

namespace AppBundle\EventListener;

use Pimcore\Bundle\PortalEngineBundle\Event\DataObject\ExtractMainImageEvent;
use Pimcore\Model\Asset;
use Pimcore\Model\Asset\Image;
use Pimcore\Model\DataObject\Car;
use Pimcore\Model\DataObject\Data\ImageGallery;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PortalEngineExtractCarMainImageSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            ExtractMainImageEvent::class  => 'onExtractMainImage'
        ];
    }

    public function onExtractMainImage(ExtractMainImageEvent $event)
    {
        $car = $event->getObject();
        if(!$car instanceof Car) {
            return;
        }

        $image = $this->getFirstGalleryImage($car);
        $image = $image ?? Asset::getById(123); //simplified fall back image example

        $event->setMainImage($image);
    }

    protected function getFirstGalleryImage(Car $car): ?Image
    {
        $imageGallery = $car->getGallery();
        if ($imageGallery instanceof ImageGallery) {
            foreach ($imageGallery->getItems() as $hotSpotImage) {
                if ($hotSpotImage && $hotSpotImage->getImage()) {
                    return $hotSpotImage->getImage();
                }
            }
        }

        return null;
    }
}
```

```yaml
# example service definition
services:
    AppBundle\EventListener\PortalEngineExtractCarMainImageSubscriber:
        tags:
          - { name: kernel.event_subscriber }
```