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
 * Class FilterData
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Filter
 */
class FilterData
{
    /** @var string|array */
    protected $currentValue;
    /** @var bool */
    protected $visible;
    /** @var FilterDataOption[] */
    protected $options;

    /**
     * @return array|string
     */
    public function getCurrentValue()
    {
        return $this->currentValue;
    }

    /**
     * @param array|string $currentValue
     *
     * @return FilterData
     */
    public function setCurrentValue($currentValue)
    {
        $this->currentValue = $currentValue;

        return $this;
    }

    /**
     * @return bool
     */
    public function isVisible(): bool
    {
        return $this->visible;
    }

    /**
     * @param bool $visible
     *
     * @return FilterData
     */
    public function setVisible(bool $visible): self
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * @return FilterDataOption[]
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param FilterDataOption[] $options
     *
     * @return FilterData
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }
}
