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

class Multiselect extends Data
{
    /**
     * @var string
     */
    public $fieldtype = 'multiselect';

    /**
     * Available options to select
     *
     * @var array|null
     */
    public $options;

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     *
     * @return $this
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    public function addGridConfig(&$item)
    {
        $name = $item['name'];
        $fieldDefinition = Helper::getFieldDefinition($name);

        $config = '';
        $list = [];
        if ($fieldDefinition) {
            $options = $fieldDefinition->getOptions() ?? [];
            foreach ($options as $option) {
                $list[] = $option['value'];
            }
            $config = implode(',', $list);
        }
        $item['config'] = $config;
    }

    public function addListFolderConfig(&$item)
    {
        $this->addGridConfig($item);
    }

    /**
     * @param mixed $data
     * @param array $params
     *
     * @return mixed
     */
    public function transformGetterData($data, $params = [])
    {
        if (is_string($data)) {
            return explode(',', $data);
        }

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
        if (is_array($data)) {
            return implode(',', $data);
        }

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
        if (is_array($data)) {
            return implode(',', $data);
        }

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
        if (is_string($data)) {
            return explode(',', $data);
        }

        return $data;
    }

    /** @inheritDoc */
    public function getDataForEditMode($data, $params = [])
    {
        if (is_array($data)) {
            return implode(',', $data);
        }

        return $data;
    }

    /**
     * @param mixed $data
     * @param array $params
     *
     * @return mixed
     */
    public function getDataForListfolderGrid($data, $params = [])
    {
        if (is_array($data)) {
            return implode(',', $data);
        }

        return $data;
    }

    /** @inheritDoc */
    public function getVersionPreview($data, $params = [])
    {
        if (is_array($data)) {
            return implode(',', $data);
        }

        return $data;
    }
}
