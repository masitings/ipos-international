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
 * Fires before the data for assets gets updated in the elasticsearch index.
 * Can be used to add additional customized attributes in the search index.
 * You will find a description and example on how it works in the portal engine docs.
 */
class UpdateIndexDataEvent extends Event
{
    /** @var Asset */
    protected $asset;
    /** @var array */
    protected $customFields;

    /**
     * UpdateIndexDataEvent constructor.
     *
     * @param Asset $asset
     * @param array $customFields
     */
    public function __construct(Asset $asset, array $customFields)
    {
        $this->asset = $asset;
        $this->customFields = $customFields;
    }

    /**
     * @return Asset
     */
    public function getAsset(): Asset
    {
        return $this->asset;
    }

    /**
     * @return array
     */
    public function getCustomFields(): array
    {
        return $this->customFields;
    }

    /**
     * @param array $customFields
     *
     * @return UpdateIndexDataEvent
     */
    public function setCustomFields(array $customFields): self
    {
        $this->customFields = $customFields;

        return $this;
    }
}
