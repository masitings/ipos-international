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
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Document;
use Pimcore\Model\Element\ElementInterface;
use Pimcore\Model\Element\Service;

class ManyToManyRelation extends Data
{
    /**
     * @var string
     */
    public $fieldtype = 'manyToManyRelation';

    /**
     * @var int
     */
    public $maxItems;

    /**
     * @var string
     */
    public $assetUploadPath;

    /**
     * @var bool
     */
    public $relationType = true;

    /**
     *
     * @var bool
     */
    public $objectsAllowed = false;

    /**
     *
     * @var bool
     */
    public $assetsAllowed = false;

    /**
     * Allowed asset types
     *
     * @var array
     */
    public $assetTypes = [];

    /**
     *
     * @var bool
     */
    public $documentsAllowed = false;

    /**
     * Allowed document types
     *
     * @var array
     */
    public $documentTypes = [];

    /**
     * Set of allowed classes
     *
     * @var array
     */
    public $classes = [];

    /**
     * @return int
     */
    public function getMaxItems()
    {
        return $this->maxItems;
    }

    /**
     * @param string|int|null $maxItems
     *
     * @return $this
     */
    public function setMaxItems($maxItems)
    {
        $this->maxItems = $this->getAsIntegerCast($maxItems);

        return $this;
    }

    /**
     * @return string
     */
    public function getAssetUploadPath()
    {
        return $this->assetUploadPath;
    }

    /**
     * @param string $assetUploadPath
     *
     * @return $this
     */
    public function setAssetUploadPath($assetUploadPath)
    {
        $this->assetUploadPath = $assetUploadPath;

        return $this;
    }

    /**
     * @return bool
     */
    public function getObjectsAllowed()
    {
        return $this->objectsAllowed;
    }

    /**
     * @param bool $objectsAllowed
     *
     * @return $this
     */
    public function setObjectsAllowed($objectsAllowed)
    {
        $this->objectsAllowed = $objectsAllowed;

        return $this;
    }

    /**
     * @return bool
     */
    public function getDocumentsAllowed()
    {
        return $this->documentsAllowed;
    }

    /**
     * @param bool $documentsAllowed
     *
     * @return $this
     */
    public function setDocumentsAllowed($documentsAllowed)
    {
        $this->documentsAllowed = $documentsAllowed;

        return $this;
    }

    /**
     * @return array
     */
    public function getDocumentTypes()
    {
        return $this->documentTypes ?: [];
    }

    /**
     * @param array $documentTypes
     *
     * @return $this
     */
    public function setDocumentTypes($documentTypes)
    {
        $this->documentTypes = Service::fixAllowedTypes($documentTypes, 'documentTypes');

        return $this;
    }

    /**
     *
     * @return bool
     */
    public function getAssetsAllowed()
    {
        return $this->assetsAllowed;
    }

    /**
     *
     * @param bool $assetsAllowed
     *
     * @return $this
     */
    public function setAssetsAllowed($assetsAllowed)
    {
        $this->assetsAllowed = $assetsAllowed;

        return $this;
    }

    /**
     * @return array
     */
    public function getAssetTypes()
    {
        return $this->assetTypes;
    }

    /**
     * @param array $assetTypes
     *
     * @return $this
     */
    public function setAssetTypes($assetTypes)
    {
        $this->assetTypes = Service::fixAllowedTypes($assetTypes, 'assetTypes');

        return $this;
    }

    /**
     * @return array[
     *  'classes' => string,
     * ]
     */
    public function getClasses()
    {
        return $this->classes ?: [];
    }

    /**
     * @param array $classes
     *
     * @return $this
     */
    public function setClasses($classes)
    {
        $this->classes = Service::fixAllowedTypes($classes, 'classes');

        return $this;
    }

    public function addListFolderConfig(&$item)
    {
        $this->addGridConfig($item);
    }

    public function addGridConfig(&$item)
    {
        $fieldDefinition = Helper::getFieldDefinition($item['name']);
        if ($fieldDefinition) {
            $config = json_encode($fieldDefinition);
            $item['config'] = $config;
        }
    }

    /**
     * @param mixed $data
     * @param array $params
     *
     * @return mixed
     */
    public function transformGetterData($data, $params = [])
    {
        if (is_array($data)) {
            $result = [];
            foreach ($data as $item) {
                if (isset($item[0]) && isset($item[1])) {
                    $type = $item[0];
                    $id = $item[1];
                    $e = Service::getElementById($type, $id);
                    if ($e) {
                        $result[] = $e;
                    }
                }
            }

            return $result;
        }

        return $data;
    }

    /**
     * @param $data
     * @param array $params
     *
     * @return mixed
     */
    public function getDataFromEditMode($data, $params = [])
    {
        if ($data) {
            $result = [];
            foreach ($data as $item) {
                $result[] = [
                    $item['type'], $item['id']
                ];
            }

            return $result;
        }

        return $data;
    }

    /**
     * @param $data
     * @param array $params
     *
     * @return mixed
     */
    public function getDataForResource($data, $params = [])
    {
        return json_encode($data);
    }

    /**
     * @param mixed $data
     * @param array $params
     *
     * @return mixed
     */
    public function transformSetterData($data, $params = [])
    {
        if (is_array($data)) {
            $result = [];
            foreach ($data as $item) {
                if ($item instanceof ElementInterface) {
                    $result[] = [Service::getElementType($item), $item->getId()];
                }
            }

            return $result;
        }

        return $data;
    }

    /**
     * @param $data
     * @param array $params
     *
     * @return mixed
     */
    public function getDataFromResource($data, $params = [])
    {
        return json_decode($data, true);
    }

    /**
     * @param mixed $data
     * @param array $params
     *
     * @return mixed
     */
    public function getDataForListfolderGrid($data, $params = [])
    {
        return $this->getDataForEditMode($data, $params);
    }

    /** @inheritDoc */
    public function getDataForEditMode($data, $params = [])
    {
        $return = [];

        if (is_array($data) && count($data) > 0) {
            foreach ($data as $elementData) {
                if (is_array($elementData)) {
                    $element = Document\Service::getElementById($elementData[0], $elementData[1]);
                } else {
                    $element = $elementData;
                }

                if ($element instanceof Concrete) {
                    $return[] = [
                        'id' => $element->getId(), 'fullpath' => $element->getRealFullPath(), 'type' => 'object',
                        'classname' => $element->getClassName(), 'published' => $element->getPublished()];
                } elseif ($element instanceof AbstractObject) {
                    $return[] = [
                        'id' => $element->getId(), 'fullpath' => $element->getRealFullPath(), 'type' => 'object', 'subtype' => 'folder'];
                } elseif ($element instanceof Asset) {
                    $return[] = ['id' => $element->getId(), 'fullpath' => $element->getRealFullPath(), 'type' => 'asset', 'subtype' => $element->getType()];
                } elseif ($element instanceof Document) {
                    $return[] = ['id' => $element->getId(), 'fullpath' => $element->getRealFullPath(), 'type' => 'document', 'subtype' => $element->getType(), $element->getPublished()];
                }
            }
            if (empty($return)) {
                $return = null;
            }

            return $return;
        }

        return null;
    }

    /**
     * @param $data
     * @param array $params
     *
     * @return mixed
     */
    public function getDataFromListfolderGrid($data, $params = [])
    {
        if (is_array($data)) {
            $result = [];
            foreach ($data as $item) {
                $result[] = [$item['type'], $item['id']];
            }

            return $result;
        }

        return $data;
    }

    /**
     * @param $data
     * @param array $params
     *
     * @return array
     */
    public function resolveDependencies($data, $params = [])
    {
        $result = [];
        if ($data) {
            foreach ($data as $item) {
                if ($item instanceof ElementInterface) {
                    $elementType = $item->getType();
                    $elementId = $item->getId();
                } else {
                    $elementType = $item[0];
                    $elementId = $item[1];
                }

                $key = $elementType . '_' . $elementId;
                $result[$key] = [
                    'id' => $elementId,
                    'type' => $elementType
                ];
            }

            return $result;
        }
    }

    /** @inheritDoc */
    public function getVersionPreview($data, $params = [])
    {
        if (is_array($data) && count($data) > 0) {
            $pathes = [];

            foreach ($data as $e) {
                $e = Service::getElementById($e[0], $e[1]);
                if ($e instanceof ElementInterface) {
                    $pathes[] = get_class($e) . $e->getRealFullPath();
                }
            }

            return implode('<br />', $pathes);
        }

        return null;
    }
}
