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

class Date extends Data
{
    /**
     * @var string
     */
    public $fieldtype = 'date';

    /**
     * @param mixed $value
     * @param array $params
     */
    public function getVersionPreview($value, $params = [])
    {
        return date('m/d/Y H:i:s', $value);
    }
}
