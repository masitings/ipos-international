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

class Country extends Data
{
    /**
     * @var string
     */
    public $fieldtype = 'country';

    public $options = [];

    public function addGridConfig(&$item)
    {
        $name = $item['name'];
        $fieldDefinition = Helper::getFieldDefinition($name);

        $config = '';
        $list = [];
        if ($fieldDefinition) {
            $countries = \Pimcore::getContainer()->get('pimcore.locale')->getDisplayRegions();
            asort($countries);
            $options = [];

            foreach ($countries as $short => $translation) {
                if (strlen($short) == 2) {
                    $list[] = $short;
                }
            }
            $config = implode(',', $list);
        }
        $item['config'] = $config;
    }

    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * Add dynamic layout options
     */
    public function enrichDefinition()
    {
        $countries = \Pimcore::getContainer()->get('pimcore.locale')->getDisplayRegions();
        asort($countries);
        $options = [];

        foreach ($countries as $short => $translation) {
            if (strlen($short) == 2) {
                $options[] = [
                    'key' => $translation,
                    'value' => $short
                ];
            }
        }

        $this->setOptions($options);
    }

    public function addListFolderConfig(&$item)
    {
        $this->addGridConfig($item);
    }
}
