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

namespace Pimcore\Bundle\PortalEngineBundle\Event\Search;

use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataPoolConfigInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Field\ListableField;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Can be used to modify the grid configuration attributes select box in the data pool documents.
 * You will find a description and example on how it works in the portal engine docs.
 */
class ListableFieldsEvent extends Event
{
    /** @var ListableField[] */
    protected $listableFields = [];
    /** @var DataPoolConfigInterface */
    protected $dataPoolConfig;

    /**
     * ListableFieldsEvent constructor.
     *
     * @param ListableField[] $listableFields
     * @param DataPoolConfigInterface $dataPoolConfig
     */
    public function __construct(array $listableFields, DataPoolConfigInterface $dataPoolConfig)
    {
        $this->listableFields = $listableFields;
        $this->dataPoolConfig = $dataPoolConfig;
    }

    /**
     * @return ListableField[]
     */
    public function getListableFields(): array
    {
        return $this->listableFields;
    }

    /**
     * @param ListableField[] $listableFields
     *
     * @return ListableFieldsEvent
     */
    public function setListableFields(array $listableFields): self
    {
        $this->listableFields = $listableFields;

        return $this;
    }

    /**
     * @return DataPoolConfigInterface
     */
    public function getDataPoolConfig(): DataPoolConfigInterface
    {
        return $this->dataPoolConfig;
    }
}
