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

class Document extends AbstractRelation
{
    /**
     * @var string
     */
    public $fieldtype = 'document';

    /**
     *
     * @var bool
     */
    public $documentsAllowed = true;

    /**
     * @param $data
     * @param array $params
     *
     * @return mixed
     */
    public function getDataFromListfolderGrid($data, $params = [])
    {
        $data = \Pimcore\Model\Document::getByPath($data);

        return $data;
    }
}
