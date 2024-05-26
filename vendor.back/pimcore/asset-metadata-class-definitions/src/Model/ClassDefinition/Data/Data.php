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

use Pimcore\Model\Asset\MetaData\ClassDefinition\Data\DataDefinitionInterface;
use Pimcore\Model\DataObject\ClassDefinition\Helper\VarExport;

class Data implements DataDefinitionInterface
{
    use VarExport;

    /**
     * @var string
     */
    public $datatype = 'data';

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $fieldtype;

    /**
     * @var string
     */
    public $style;

    /**
     * @var int
     */
    public $width;

    /**
     * @var bool
     */
    public $mandatory;

    /**
     * @param array $data
     * @param array $blockedKeys
     *
     * @return $this
     */
    public function setValues($data = [], $blockedKeys = [])
    {
        foreach ($data as $key => $value) {
            if (!in_array($key, $blockedKeys)) {
                $method = 'set' . $key;
                if (method_exists($this, $method)) {
                    $this->$method($value);
                }
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getFieldtype(): string
    {
        return $this->fieldtype;
    }

    /**
     * @param string $fieldtype
     */
    public function setFieldtype(string $fieldtype): void
    {
        $this->fieldtype = $fieldtype;
    }

    /**
     *
     * @return string
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * @param string|null $style
     *
     * @return $this
     */
    public function setStyle($style)
    {
        $this->style = (string)$style;

        return $this;
    }

    /**
     * @return bool
     */
    public function isMandatory()
    {
        return $this->mandatory;
    }

    /**
     * @param bool $mandatory
     */
    public function setMandatory($mandatory)
    {
        $this->mandatory = (bool) $mandatory;
    }

    /**
     * @param int $width
     *
     * @return $this
     */
    public function setWidth($width)
    {
        $this->width = $this->getAsIntegerCast($width);

        return $this;
    }

    /**
     * @param int|string|null $number
     *
     * @return int|null
     */
    public function getAsIntegerCast($number)
    {
        return strlen($number) === 0 ? '' : (int)$number;
    }

    /** Add type specific configurations
     *
     * @param $item
     */
    public function addListFolderConfig(&$item)
    {
    }

    /** Add type specific configurations
     *
     * @param $item
     */
    public function addGridConfig(&$item)
    {
    }

    /**
     * Add dynamic layout options
     */
    public function enrichDefinition()
    {
    }

    /**
     * @param mixed $data
     * @param array $params
     *
     * @return mixed
     */
    public function transformGetterData($data, $params = [])
    {
        return $data;
    }

    /**
     * @param mixed $data
     * @param array $params
     *
     * @return mixed
     */
    public function transformSetterData($data, $params = [])
    {
        return $data;
    }

    /**
     * @param $data
     * @param array $params
     *
     * @return mixed
     */
    public function getDataFromEditMode($data, $params = [])
    {
        return $data;
    }

    /**
     * @param $data
     * @param array $params
     *
     * @return mixed
     */
    public function getDataForResource($data, $params = [])
    {
        return $data;
    }

    /** @inheritDoc */
    public function getDataForEditMode($data, $params = [])
    {
        return $data;
    }

    public function isEmpty($data, $params = [])
    {
        return empty($data);
    }

    /**
     * @param mixed $data
     * @param array $params
     */
    public function checkValidity($data, $params = [])
    {
    }

    /**
     * @param mixed $data
     * @param array $params
     *
     * @return mixed
     */
    public function getDataForListfolderGrid($data, $params = [])
    {
        return $data;
    }

    /**
     * @param $data
     * @param array $params
     *
     * @return mixed
     */
    public function getDataFromListfolderGrid($data, $params = [])
    {
        return $data;
    }

    /**
     * @param $data
     * @param array $params
     *
     * @return array
     */
    public function resolveDependencies($data, $params = [])
    {
        return [];
    }

    /**
     * @param $data
     * @param array $params
     *
     * @return mixed
     */
    public function getDataFromResource($data, $params = [])
    {
        return $data;
    }

    /**
     * @param mixed $value
     * @param array $params
     */
    public function getVersionPreview($value, $params = [])
    {
        return $value;
    }

    /**
     * @param mixed $data
     * @param array $params
     *
     * @return mixed
     */
    public function getDataForSearchIndex($data, $params = [])
    {
        if (is_scalar($data)) {
            return $params['name'] . ':' . $data;
        }
    }
}
