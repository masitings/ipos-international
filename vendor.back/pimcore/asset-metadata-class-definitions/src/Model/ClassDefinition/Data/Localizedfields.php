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

class Localizedfields extends Data
{
    /**
     * @var string
     */
    public $fieldtype = 'localizedfields';

    /**
     * @var array
     */
    public $childs = [];

    public function getChildren()
    {
        return $this->getChilds();
    }

    /**
     * @return array
     */
    public function getChilds()
    {
        return $this->childs;
    }

    /**
     * @param array $children
     *
     * @return $this
     */
    public function setChilds($children)
    {
        $this->childs = $children;

        return $this;
    }

    public function hasChildren()
    {
        return count($this->childs) > 0;
    }

    public function addChild($child)
    {
        $this->childs[] = $child;
    }
}
