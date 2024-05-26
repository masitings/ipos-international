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

use Pimcore\Bundle\PortalEngineBundle\Service\DataObject\RestApiFieldDefinitionService;
use Pimcore\Logger;
use Pimcore\Model\Asset\Image;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\AbstractObject;

class BlockAdapter extends DefaultAdapter
{
    use FlattenNestedData;

    /**
     * @var RestApiFieldDefinitionService
     */
    protected $fieldDefinitionService;

    /**
     * BlockAdapter constructor.
     *
     * @param RestApiFieldDefinitionService $fieldDefinitionService
     */
    public function __construct(RestApiFieldDefinitionService $fieldDefinitionService)
    {
        $this->fieldDefinitionService = $fieldDefinitionService;
    }

    /**
     *
     * @param AbstractObject $object
     * @param Image $data
     * @param array $params
     *
     * @return array|mixed
     */
    public function getDataForDetail(AbstractObject $object, $data, array $params = [])
    {
        return $this->getData($object, $data, $params);
    }

    public function getDataForVersionPreview(AbstractObject $object, $data, array $params = [])
    {
        $data = $this->getData($object, $data, $params, true);

        return $this->flattenData($data);
    }

    protected function extractKeyForFlattening($data)
    {
        return $data ? $data['oIndex'] : null;
    }

    /**
     * @param AbstractObject $object
     * @param $data
     * @param array $params
     * @param bool $versionPreview
     *
     * @return array
     */
    protected function getData(AbstractObject $object, $data, array $params = [], bool $versionPreview = false)
    {
        $params = (array)$params;
        $result = [];
        $idx = -1;

        if (is_array($data)) {
            foreach ($data as $blockElements) {
                $resultElement = [];
                $idx++;

                /** @var DataObject\Data\BlockElement $blockElement */
                foreach ($blockElements as $elementName => $blockElement) {
                    $fd = $this->fieldDefinition->getFieldDefinition($elementName);
                    if (!$fd) {
                        // class definition seems to have changed
                        Logger::warn('class definition seems to have changed, element name: ' . $elementName);
                        continue;
                    }
                    $value = $blockElement->getData();
                    $params['context']['containerType'] = 'block';

                    $fieldDefinitionAdapter = $this->fieldDefinitionService->getFieldDefinitionAdapter($fd);
                    $resultElement[$fd->getName()] = !$versionPreview ? $fieldDefinitionAdapter->getDataForDetail($object, $value, $params) : $fieldDefinitionAdapter->getDataForVersionPreview($object, $value, $params);
                }

                $result[] = [
                    'oIndex' => $idx,
                    'data' => $resultElement
                ];
            }
        }

        return $result;
    }
}
