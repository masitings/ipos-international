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

namespace Pimcore\Bundle\PortalEngineBundle\Service\DataObject;

/**
 * Class SelectOptionsExtractor
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\DataObject
 */
class SelectOptionsExtractor
{
    /**
     * @param string $value
     * @param array $options
     *
     * @return string
     */
    public function getKeyByValue($value, $options)
    {
        /** @var string $key */
        $key = $value;

        foreach ($options as $option) {
            if ($option['value'] === $value) {
                $key = $option['key'];
                break;
            }
        }

        return $key;
    }
}
