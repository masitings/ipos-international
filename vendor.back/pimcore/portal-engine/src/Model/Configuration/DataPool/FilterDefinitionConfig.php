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

namespace Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool;

class FilterDefinitionConfig
{
    /**
     * @var string
     */
    protected $filterType;

    /**
     * @var string
     */
    protected $filterAttribute;

    /**
     * @var string
     */
    protected $filterParamName;

    /**
     * FilterDefinitionConfig constructor.
     *
     * @param string $filterType
     * @param string $filterAttribute
     */
    public function __construct(string $filterType, string $filterAttribute, string $filterParamName)
    {
        $this->filterType = $filterType;
        $this->filterAttribute = $filterAttribute;
        $this->filterParamName = $filterParamName;
    }

    /**
     * @return string
     */
    public function getFilterType(): string
    {
        return $this->filterType;
    }

    /**
     * @return string
     */
    public function getFilterAttribute(): string
    {
        return $this->filterAttribute;
    }

    /**
     * @return string
     */
    public function getFilterParamName(): string
    {
        return $this->filterParamName;
    }
}
