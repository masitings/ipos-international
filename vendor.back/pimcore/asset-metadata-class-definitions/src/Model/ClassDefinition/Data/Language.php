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

use Pimcore\Tool;

class Language extends Data
{
    /**
     * @var string
     */
    public $fieldtype = 'language';

    public $options = [];

    /**
     * Add dynamic layout options
     */
    public function enrichDefinition()
    {
        $validLanguages = (array)Tool::getValidLanguages();
        $locales = Tool::getSupportedLocales();
        $options = [];

        foreach ($locales as $short => $translation) {
            if (!in_array($short, $validLanguages)) {
                continue;
            }

            $options[] = [
                'key' => $translation,
                'value' => $short
            ];
        }

        $this->setOptions($options);
    }

    public function setOptions($options)
    {
        $this->options = $options;
    }

    public function addListFolderConfig(&$item)
    {
        $this->addGridConfig($item);
    }

    public function addGridConfig(&$item)
    {
        $validLanguages = (array)Tool::getValidLanguages();
        $locales = Tool::getSupportedLocales();
        $options = [];

        foreach ($locales as $short => $translation) {
            if (!in_array($short, $validLanguages)) {
                continue;
            }

            $options[] = $short;
        }

        $config = implode(',', $options);

        $item['config'] = $config;
    }
}
