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

class Select extends Data
{
    /**
     * @var string
     */
    public $fieldtype = 'select';

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
}
