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

class Datetime extends Data
{
    /**
     * @var string
     */
    public $fieldtype = 'datetime';

    /** @inheritDoc */
    public function getDataForEditMode($data, $params = [])
    {
        if ($data) {
            $data = $data * 1000;
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
        if ($data) {
            $data = $data / 1000;
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
        return $this->getDataForEditMode($data, $params);
    }

    /**
     * @param $data
     * @param array $params
     *
     * @return mixed
     */
    public function getDataFromListfolderGrid($data, $params = [])
    {
        return $this->getDataFromEditMode($data, $params);
    }

    /**
     * @param mixed $value
     * @param array $params
     */
    public function getVersionPreview($value, $params = [])
    {
        return date('m/d/Y H:i', $value);
    }
}
