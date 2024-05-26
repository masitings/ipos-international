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

namespace Pimcore\Bundle\PortalEngineBundle\Event\Asset;

use Pimcore\Model\Asset;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Fires when the name of an asset gets extracted.
 * Can be used to customize the name extraction for assets.
 * You will find a description and example on how it works in the portal engine docs.
 */
class ExtractNameEvent extends Event
{
    /**
     * @var Asset
     */
    protected $asset;

    /**
     * @var string|null
     */
    protected $name;

    /**
     * @var string
     */
    protected $locale;

    /**
     * ExtractNameEvent constructor.
     *
     * @param $asset
     */
    public function __construct(Asset $asset, string $locale = null)
    {
        $this->asset = $asset;
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
     * @return Asset
     */
    public function getAsset(): Asset
    {
        return $this->asset;
    }

    /**
     * @return string|null
     */
    public function getLocale(): ?string
    {
        return $this->locale;
    }
}
