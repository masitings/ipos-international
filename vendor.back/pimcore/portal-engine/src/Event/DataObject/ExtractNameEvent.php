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

use Pimcore\Model\DataObject\AbstractObject;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Fires when the name of an asset gets extracted.
 * Can be used to customize the name extraction for assets.
 * You will find a description and example on how it works in the portal engine docs.
 */
class ExtractNameEvent extends Event
{
    /**
     * @var AbstractObject
     */
    protected $object;

    /**
     * @var string|null
     */
    protected $locale;

    /**
     * @var string|null
     */
    protected $name;

    /**
     * ExtractNameEvent constructor.
     *
     * @param AbstractObject $object
     * @param string|null $locale
     */
    public function __construct(AbstractObject $object, string $locale = null)
    {
        $this->object = $object;
        $this->locale = $locale;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return AbstractObject
     */
    public function getObject(): AbstractObject
    {
        return $this->object;
    }

    /**
     * @return string|null
     */
    public function getLocale(): ?string
    {
        return $this->locale;
    }
}
