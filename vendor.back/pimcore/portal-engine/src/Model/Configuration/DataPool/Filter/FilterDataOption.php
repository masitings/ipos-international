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

namespace Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Filter;

/**
 * Class FilterDataOption
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Filter
 */
class FilterDataOption
{
    /** @var mixed */
    protected $value;
    /** @var string */
    protected $label;
    /** @var int */
    protected $count;

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     *
     * @return FilterDataOption
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     *
     * @return FilterDataOption
     */
    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @param int $count
     *
     * @return FilterDataOption
     */
    public function setCount(int $count): self
    {
        $this->count = $count;

        return $this;
    }
}
