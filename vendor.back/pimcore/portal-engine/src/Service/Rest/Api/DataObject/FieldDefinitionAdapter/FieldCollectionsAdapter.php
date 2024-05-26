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
use Pimcore\Model\Asset\Image;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\ClassDefinition\Data\CalculatedValue;

class FieldCollectionsAdapter extends DefaultAdapter
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
     *
     * @return array
     */
    protected function getData(AbstractObject $object, $data, array $params = [], bool $versionPreview = false)
    {
        $editmodeData = [];
        $idx = -1;

        if ($data instanceof DataObject\Fieldcollection) {
            foreach ($data as $item) {
                $idx++;

                if (!$item instanceof DataObject\Fieldcollection\Data\AbstractData) {
                    continue;
                }

                if ($collectionDef = DataObject\Fieldcollection\Definition::getByKey($item->getType())) {
                    $collectionData = [];

                    foreach ($collectionDef->getFieldDefinitions() as $fd) {
                        if (!$fd instanceof CalculatedValue) {
                            $value = $item->{'get' . $fd->getName()}();
                            $fieldDefinitionAdapter = $this->fieldDefinitionService->getFieldDefinitionAdapter($fd);
                            if ($fd instanceof DataObject\ClassDefinition\Data\Localizedfields) {
                                $params[LocalizedFieldsAdapter::PARAM_FIELD_COLLECTION] = $item;
                            }

                            $collectionData[$fd->getName()] = !$versionPreview ? $fieldDefinitionAdapter->getDataForDetail($object, $value, $params) : $fieldDefinitionAdapter->getDataForVersionPreview($object, $value, $params);
                        }
                    }

                    $calculatedChilds = [];
                    DataObject\ClassDefinition\Data\Fieldcollections::collectCalculatedValueItems($collectionDef->getFieldDefinitions(), $calculatedChilds);

                    if ($calculatedChilds) {
                        foreach ($calculatedChilds as $fd) {
                            $calculatedData = new DataObject\Data\CalculatedValue($fd->getName());
                            $fieldDefinitionAdapter = $this->fieldDefinitionService->getFieldDefinitionAdapter($fd);
                            $calculatedData->setContextualData('fieldcollection', $this->fieldDefinition->getName(), $idx, null, null, null, $fd);
                            $calculatedData = !$versionPreview ? $fieldDefinitionAdapter->getDataForDetail($object, $calculatedData, $params) : $fieldDefinitionAdapter->getDataForVersionPreview($object, $calculatedData, $params);
                            $collectionData[$fd->getName()] = $calculatedData;
                        }
                    }

                    $editmodeData[] = [
                        'data' => $collectionData,
                        'type' => $item->getType(),
                        'oIndex' => $idx,
                        'title' => $collectionDef->getTitle()
                    ];
                }
            }
        }

        return array_values(array_filter($editmodeData));
    }
}
