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

use Pimcore\Model\DataObject\AbstractObject;

class DataObject extends AbstractRelation
{
    /**
     *
     * @var bool
     */
    public $objectsAllowed = true;

    /**
     * @var string
     */
    public $fieldtype = 'object';

    /**
     * @param $data
     * @param array $params
     *
     * @return mixed
     */
    public function getDataFromListfolderGrid($data, $params = [])
    {
        $data = AbstractObject::getByPath($data);

        return $data;
    }
}
