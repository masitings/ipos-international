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

namespace Pimcore\Bundle\PortalEngineBundle\Model\Rest\Api\DataObject;

class VersionPreviewValue implements \JsonSerializable
{
    protected $key;
    protected $label;
    protected $labelAdditions = [];
    protected $value;
    protected $dirty;

    /**
     * VersionPreviewValue constructor.
     *
     * @param string $key
     * @param string|array $label
     * @param $value
     * @param bool $dirty
     */
    public function __construct(string $key, $label, $value, bool $dirty = false)
    {
        $this->key = $key;
        $this->value = $value;
        $this->dirty = $dirty;

        if (is_array($label)) {
            $this->label = array_shift($label);
            $this->labelAdditions = $label;
        } else {
            $this->label = $label;
        }
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return array|string
     */
    public function getLabelAdditions()
    {
        return $this->labelAdditions;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function isDirty(): bool
    {
        return $this->dirty;
    }

    /**
     * @param bool $dirty
     */
    public function setDirty(bool $dirty): void
    {
        $this->dirty = $dirty;
    }

    public function jsonSerialize()
    {
        return [
            'name' => $this->key,
            'title' => $this->label,
            'titleAddition' => implode(' ', $this->labelAdditions),
            'value' => $this->value,
            'dirty' => $this->dirty
        ];
    }
}
