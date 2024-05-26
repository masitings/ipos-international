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

namespace Pimcore\AssetMetadataClassDefinitionsBundle\Model\ClassDefinition\Data;

use Pimcore\AssetMetadataClassDefinitionsBundle\Helper;
use Pimcore\Model\Element\ValidationException;

class Numeric extends Data
{
    /**
     * @var string
     */
    public $fieldtype = 'numeric';

    /**
     * @var bool
     */
    public $integer = false;

    /**
     * @var bool
     */
    public $unsigned = false;

    /**
     * @var float
     */
    public $minValue;

    /**
     * @var float
     */
    public $maxValue;

    /**
     * @param bool $integer
     */
    public function setInteger($integer)
    {
        $this->integer = $integer;
    }

    /**
     * @return bool
     */
    public function getInteger()
    {
        return $this->integer;
    }

    /**
     * @param float $maxValue
     */
    public function setMaxValue($maxValue)
    {
        $this->maxValue = $maxValue;
    }

    /**
     * @return float
     */
    public function getMaxValue()
    {
        return $this->maxValue;
    }

    /**
     * @param float $minValue
     */
    public function setMinValue($minValue)
    {
        $this->minValue = $minValue;
    }

    /**
     * @return float
     */
    public function getMinValue()
    {
        return $this->minValue;
    }

    /**
     * @param bool $unsigned
     */
    public function setUnsigned($unsigned)
    {
        $this->unsigned = $unsigned;
    }

    /**
     * @return bool
     */
    public function getUnsigned()
    {
        return $this->unsigned;
    }

    public function addGridConfig(&$item)
    {
        $fieldDefinition = Helper::getFieldDefinition($item['name']);
        if ($fieldDefinition) {
            $config = json_encode($fieldDefinition);
            $item['config'] = $config;
        }
    }

    public function addListFolderConfig(&$item)
    {
        $this->addGridConfig($item);
    }

    /**
     * @param mixed $data
     * @param array $params
     */
    public function checkValidity($data, $params = [])
    {
        if (strlen($data) < 1) {
            return;
        }
        if ($this->getInteger() && strpos((string) $data, '.') !== false) {
            throw new ValidationException('Value in field [ '.$this->getName().' ] is not an integer');
        }

        if (strlen($this->getMinValue()) && $this->getMinValue() > $data) {
            throw new ValidationException('Value in field [ '.$this->getName().' ] is not at least ' . $this->getMinValue());
        }

        if (strlen($this->getMaxValue()) && $data > $this->getMaxValue()) {
            throw new ValidationException('Value in field [ '.$this->getName().' ] is bigger than ' . $this->getMaxValue());
        }

        if ($this->getUnsigned() && $data < 0) {
            throw new ValidationException('Value in field [ '.$this->getName().' ] is not unsigned (bigger than 0)');
        }
    }
}
