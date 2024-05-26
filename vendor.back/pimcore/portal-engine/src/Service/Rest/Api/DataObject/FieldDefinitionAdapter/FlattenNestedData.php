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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\DataObject\FieldDefinitionAdapter;

use Pimcore\Bundle\PortalEngineBundle\Model\Rest\Api\DataObject\VersionPreviewValue;

trait FlattenNestedData
{
    protected function flattenData(?array $data)
    {
        if (empty($data)) {
            return $data;
        }

        $flattened = [];

        foreach ($data as $item) {
            if (empty($item['data'])) {
                continue;
            }

            foreach ($item['data'] as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $v) {
                        $k = "{$this->extractKeyForFlattening($item)}_{$key}_{$v->getKey()}";
                        $flattened[$k] = new VersionPreviewValue($k, $this->extractLabelForFlattening($item, $v), $v->getValue());
                    }
                } else {
                    $k = "{$this->extractKeyForFlattening($item)}_{$value->getKey()}";
                    $flattened[$k] = new VersionPreviewValue($k, $this->extractLabelForFlattening($item, $value), $value->getValue());
                }
            }
        }

        return $flattened;
    }

    abstract protected function extractKeyForFlattening($data);

    protected function extractLabelForFlattening($data, VersionPreviewValue $value)
    {
        return array_merge([$value->getLabel(), "[{$this->extractKeyForFlattening($data)}]"], $value->getLabelAdditions());
    }
}
