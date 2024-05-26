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

use Pimcore\Model\DataObject\Concrete;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Fires before the data for assets gets updated in the elasticsearch index.
 * Can be used to add additional customized attributes in the search index.
 * You will find a description and example on how it works in the portal engine docs.
 */
class UpdateIndexDataEvent extends Event
{
    /** @var Concrete */
    protected $dataObject;
    /** @var array */
    protected $customFields;

    /**
     * UpdateIndexDataEvent constructor.
     *
     * @param Concrete $dataObject
     * @param array $customFields
     */
    public function __construct(Concrete $dataObject, array $customFields)
    {
        $this->dataObject = $dataObject;
        $this->customFields = $customFields;
    }

    /**
     * @return Concrete
     */
    public function getDataObject(): Concrete
    {
        return $this->dataObject;
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
