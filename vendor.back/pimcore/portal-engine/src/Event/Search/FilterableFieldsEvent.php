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
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Field\FilterableField;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Can be used to modify the filter attribute select boxes in the data pool documents.
 * You will find a description and example on how it works in the portal engine docs.
 */
class FilterableFieldsEvent extends Event
{
    /** @var FilterableField[] */
    protected $filterableFields = [];
    /** @var DataPoolConfigInterface */
    protected $dataPoolConfig;

    /**
     * FilterableFieldsEvent constructor.
     *
     * @param FilterableField[] $filterableFields
     * @param DataPoolConfigInterface $dataPoolConfig
     */
    public function __construct(array $filterableFields, DataPoolConfigInterface $dataPoolConfig)
    {
        $this->filterableFields = $filterableFields;
        $this->dataPoolConfig = $dataPoolConfig;
    }

    /**
     * @return FilterableField[]
     */
    public function getFilterableFields(): array
    {
        return $this->filterableFields;
    }

    /**
     * @param FilterableField[] $filterableFields
     *
     * @return FilterableFieldsEvent
     */
    public function setFilterableFields(array $filterableFields): self
    {
        $this->filterableFields = $filterableFields;

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
