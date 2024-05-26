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
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Field\SortableField;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Can be used to modify the sort options field select box in the data pool documents.
 * You will find a description and example on how it works in the portal engine docs.
 */
class SortableFieldsEvent extends Event
{
    /** @var SortableField[] */
    protected $sortableFields = [];
    /** @var DataPoolConfigInterface */
    protected $dataPoolConfig;

    /**
     * SortableFieldsEvent constructor.
     *
     * @param SortableField[] $sortableFields
     * @param DataPoolConfigInterface $dataPoolConfig
     */
    public function __construct(array $sortableFields, DataPoolConfigInterface $dataPoolConfig)
    {
        $this->sortableFields = $sortableFields;
        $this->dataPoolConfig = $dataPoolConfig;
    }

    /**
     * @return SortableField[]
     */
    public function getSortableFields(): array
    {
        return $this->sortableFields;
    }

    /**
     * @param SortableField[] $sortableFields
     *
     * @return SortableFieldsEvent
     */
    public function setSortableFields(array $sortableFields): self
    {
        $this->sortableFields = $sortableFields;

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
