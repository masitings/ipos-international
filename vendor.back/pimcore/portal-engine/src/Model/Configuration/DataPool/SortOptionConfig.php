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

class SortOptionConfig
{
    /**
     * @var string
     */
    protected $direction;

    /**
     * @var string
     */
    protected $field;

    /**
     * @var string
     */
    protected $paramName;

    /**
     * SortOptionConfig constructor.
     *
     * @param string $direction
     * @param string $field
     * @param string $paramName
     */
    public function __construct(string $direction, string $field, string $paramName)
    {
        $this->direction = $direction;
        $this->field = $field;
        $this->paramName = $paramName;
    }

    /**
     * @return string
     */
    public function getDirection(): string
    {
        return $this->direction;
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function getParamName(): string
    {
        return $this->paramName;
    }
}
