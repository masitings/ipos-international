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

namespace Pimcore\Bundle\PortalEngineBundle\Service\DataObject;

use Pimcore\Bundle\PortalEngineBundle\Enum\ImageThumbnails;
use Pimcore\Bundle\PortalEngineBundle\Event\DataObject\ExtractMainImageEvent;
use Pimcore\Model\Asset\Image;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Data\Hotspotimage;
use Pimcore\Model\DataObject\Data\ImageGallery;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class MainImageExtractorService
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * NameExtractorService constructor.
     *
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param AbstractObject $object
     *
     * @return string|null
     */
    public function extractThumbnail(AbstractObject $object)
    {
        /** @var string|null $thumbnailPath */
        $thumbnailPath = null;
        /** @var Image|null $mainImage */
        $mainImage = $this->extractMainImage($object);

        if ($mainImage instanceof Image) {
            $thumbnailPath = $mainImage->getThumbnail(ImageThumbnails::ELEMENT_TEASER)->getPath();
        }

        return $thumbnailPath;
    }

    /**
     * @param AbstractObject $object
     *
     * @return \Pimcore\Model\Asset\Image|null
     */
    public function extractMainImage(AbstractObject $object)
    {
        $event = new ExtractMainImageEvent($object);
        $this->eventDispatcher->dispatch($event);

        /** @var Image|null $mainImage */
        $mainImage = $event->getMainImage();

        if (is_null($mainImage)) {
            $mainImage = $this->getFirstImageFromDataObject($object);
        }

        return $mainImage;
    }

    /**
     * Get first found Asset/Image in dataObject image/hotspotimage/imageGallery fields
     *
     * @param Concrete|AbstractObject $dataObject
     *
     * @return Image|null
     */
    protected function getFirstImageFromDataObject($dataObject)
    {
        /** @var Image|null $image */
        $image = null;

        foreach ($dataObject->getClass()->getFieldDefinitions() as $fieldDefinition) {

            /** @var string $getter */
            $getter = 'get' . ucfirst($fieldDefinition->getName());

            switch ($fieldDefinition->getFieldtype()) {
                case 'image':
                    $image = $dataObject->$getter();
                    break;

                case 'hotspotimage':
                    /** @var Hotspotimage|null $hotSpotImage */
                    $hotSpotImage = $dataObject->$getter();
                    if ($hotSpotImage) {
                        $image = $hotSpotImage->getImage();
                    }
                    break;

                case 'imageGallery':
                    /** @var ImageGallery $imageGallery */
                    $imageGallery = $dataObject->$getter();
                    if ($imageGallery instanceof ImageGallery) {
                        foreach ($imageGallery->getItems() as $hotSpotImage) {
                            if ($hotSpotImage && $hotSpotImage->getImage()) {
                                $image = $hotSpotImage->getImage();
                                break;
                            }
                        }
                    }
                    break;
            }

            if ($image instanceof Image) {
                break;
            }
        }

        return $image;
    }
}
