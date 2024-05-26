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
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data\CalculatedValue;
use Pimcore\Model\DataObject\Objectbrick;
use Pimcore\Model\DataObject\Service;

class ObjectbricksAdapter extends DefaultAdapter
{
    use FlattenNestedData;

    protected $fieldDefinitionService;

    public function __construct(RestApiFieldDefinitionService $fieldDefinitionService)
    {
        $this->fieldDefinitionService = $fieldDefinitionService;
    }

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
        return $data ? $data['type'] : null;
    }

    /**
     * @param AbstractObject $object
     * @param Objectbrick $data
     * @param array $params
     * @param bool $versionPreview
     *
     * @return array
     */
    protected function getData(AbstractObject $object, $data, array $params = [], bool $versionPreview = false)
    {
        $brickData = [];

        if ($data instanceof Objectbrick) {
            foreach ($data->getAllowedBrickTypes() as $brickType) {
                $getter = 'get' . ucfirst($brickType);
                $p = [
                    'objectFromVersion' => $params['objectFromVersion'] ?? false,
                    'context' => [
                        'containerType' => 'objectbrick',
                        'containerKey' => $brickType,
                    ],
                    'fieldname' => $this->fieldDefinition->getName(),
                ];

                $brickData[] = $this->getDataForBrick($getter, $data, $p, $versionPreview);
            }
        }

        return array_values(array_filter($brickData));
    }

    /**
     * @param string $getter
     * @param $data
     * @param array $params
     * @param bool $versionPreview
     *
     * @return array|null
     */
    protected function getDataForBrick(string $getter, $data, array $params, bool $versionPreview = false)
    {
        $object = $data->getObject();
        if ($object) {
            $parent = Service::hasInheritableParentObject($object);
        }

        $item = $data->$getter();

        if (!$item && !empty($parent)) {
            $data = $parent->{'get' . ucfirst($this->fieldDefinition->getName())}();

            return $this->getDataForBrick($getter, $data, $params);
        }

        if (!$item instanceof Objectbrick\Data\AbstractData) {
            return null;
        }

        if (!$collectionDef = Objectbrick\Definition::getByKey($item->getType())) {
            return null;
        }

        $brickData = [];
        foreach ($collectionDef->getFieldDefinitions() as $fd) {
            if (!$fd instanceof CalculatedValue) {
                $value = $item->{'get' . ucfirst($fd->getName())}();
                $adapter = $this->fieldDefinitionService->getFieldDefinitionAdapter($fd);

                if ($fd instanceof ClassDefinition\Data\Localizedfields) {
                    $params[LocalizedFieldsAdapter::PARAM_FIELD_COLLECTION] = $item;
                }

                $brickData[$fd->getName()] = !$versionPreview ? $adapter->getDataForDetail($object, $value, $params) : $adapter->getDataForVersionPreview($object, $value, $params);
            }
        }

        $calculatedChilds = [];
        ClassDefinition\Data\Objectbricks::collectCalculatedValueItems($collectionDef->getFieldDefinitions(), $calculatedChilds);

        foreach ($calculatedChilds as $fd) {
            $fieldData = new \Pimcore\Model\DataObject\Data\CalculatedValue($fd->getName());
            $adapter = $this->fieldDefinitionService->getFieldDefinitionAdapter($fd);

            $fieldData->setContextualData('objectbrick', $this->fieldDefinition->getName(), $item->getType(), $fd->getName(), null, null, $fd);
            $fieldData = !$versionPreview ? $adapter->getDataForDetail($object, $fieldData, $params) : $adapter->getDataForVersionPreview($object, $fieldData, $params);
            $brickData[$fd->getName()] = $fieldData;
        }

        return [
            'data' => $brickData,
            'type' => $item->getType(),
            'title' => $collectionDef->getTitle()
        ];
    }
}
