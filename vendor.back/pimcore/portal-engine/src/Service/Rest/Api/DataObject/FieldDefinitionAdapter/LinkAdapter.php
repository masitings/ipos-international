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
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Data\Link;

class LinkAdapter extends DefaultAdapter
{
    /**
     * @param AbstractObject $object
     * @param Link $data
     * @param array $params
     *
     * @return array|mixed
     */
    public function getDataForDetail(AbstractObject $object, $data, array $params = [])
    {
        if (empty($data)) {
            return null;
        }

        return [
            'href' => $data->getHref(),
            'title' => $data->getTitle(),
            'text' => $data->getText(),
            'target' => $data->getTarget(),
            'class' => $data->getClass()
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getDataForVersionPreview(AbstractObject $object, $data, array $params = [])
    {
        return new VersionPreviewValue($this->fieldDefinition->getName(), $this->fieldDefinition->getTitle(), (string) $data);
    }
}
