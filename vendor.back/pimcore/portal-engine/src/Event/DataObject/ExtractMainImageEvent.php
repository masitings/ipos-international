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

namespace Pimcore\Bundle\PortalEngineBundle\Event\DataObject;

use Pimcore\Model\Asset\Image;
use Pimcore\Model\DataObject\AbstractObject;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Fires when the main image gets extracted from data objects for the elasticsearch index.
 * Can be used to modify the logic which image will be used as main image.
 * You will find a description and example on how it works in the portal engine docs.
 */
class ExtractMainImageEvent extends Event
{
    /**
     * @var AbstractObject
     */
    protected $object;

    /**
     * @var Image|null
     */
    protected $mainImage;

    /**
     * ExtractNameEvent constructor.
     *
     * @param $object
     */
    public function __construct(AbstractObject $object)
    {
        $this->object = $object;
    }

    /**
     * @return Image|null
     */
    public function getMainImage()
    {
        return $this->mainImage;
    }

    /**
     * @param Image|null $mainImage
     */
    public function setMainImage(Image $mainImage = null): void
    {
        $this->mainImage = $mainImage;
    }

    /**
     * @return AbstractObject
     */
    public function getObject(): AbstractObject
    {
        return $this->object;
    }

    /**
     * @param AbstractObject $object
     */
    public function setObject(AbstractObject $object): void
    {
        $this->object = $object;
    }
}
