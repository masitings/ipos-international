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

class Asset extends AbstractRelation
{
    /**
     *
     * @var bool
     */
    public $assetsAllowed = true;

    /**
     * @var string
     */
    public $fieldtype = 'asset';

    /**
     * @param $data
     * @param array $params
     *
     * @return mixed
     */
    public function getDataFromListfolderGrid($data, $params = [])
    {
        $data = \Pimcore\Model\Asset::getByPath($data);

        return $data;
    }
}
