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

use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataPoolConfigInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\Rest\Api\DataObject\VersionPreviewValue;
use Pimcore\Bundle\PortalEngineBundle\Service\Download\Provider\DataObjectFieldsProvider;
use Pimcore\Localization\LocaleServiceInterface;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\ClassDefinition\CustomLayout;
use Pimcore\Model\DataObject\ClassDefinition\Data\ReverseObjectRelation;

class CustomLayoutService
{
    protected $localeService;
    protected $fieldDefinitionService;
    protected $dataObjectFieldsProvider;

    public function __construct(
        LocaleServiceInterface $localeService,
        RestApiFieldDefinitionService $fieldDefinitionService,
        DataObjectFieldsProvider $dataObjectFieldsProvider
    ) {
        $this->localeService = $localeService;
        $this->fieldDefinitionService = $fieldDefinitionService;
        $this->dataObjectFieldsProvider = $dataObjectFieldsProvider;
    }

    /**
     * @param string $customLayoutId
     *
     * @return CustomLayout|null
     */
    public function getCustomLayout(string $customLayoutId): ?CustomLayout
    {
        return CustomLayout::getById($customLayoutId);
    }

    /**
     * @param CustomLayout|null $customLayout
     * @param DataPoolConfigInterface $config
     * @param AbstractObject|null $dataObject
     *
     * @return array
     */
    public function getCustomLayoutLayoutDefinitions(CustomLayout $customLayout = null, DataPoolConfigInterface $config, ?AbstractObject $dataObject = null): array
    {
        if (!$customLayout) {
            return [];
        }

        $layout = $customLayout->getLayoutDefinitions();

        return $this->enrichLayoutDefinition($layout, $config, $dataObject, function ($layout, $config, $object, &$item) {
            if ($layout->getDatatype() === 'data') {
                $downloadTypes = [];
                $this->dataObjectFieldsProvider->provideByDefinitions($config, $layout, $downloadTypes);
                $item['portalDownloadTypes'] = $downloadTypes;
            }
        });
    }

    /**
     * @param DataObject\ClassDefinition\Data|DataObject\ClassDefinition\Layout $layout
     * @param DataPoolConfigInterface $config
     * @param AbstractObject $object
     * @param callable|null $customEnrich
     *
     * @return array
     */
    public function enrichLayoutDefinition($layout, DataPoolConfigInterface $config, $object, ?callable $customEnrich = null)
    {
        if ($object) {
            $context = ['object' => $object];

            if (method_exists($layout, 'enrichLayoutDefinition')) {
                $layout->enrichLayoutDefinition($object, $context);
            }
        }

        $item = (array)$layout;

        if (method_exists($layout, 'getChildren')) {
            $children = [];
            if (is_array($layout->getChildren())) {
                foreach ($layout->getChildren() as $child) {
                    $children[] = $this->enrichLayoutDefinition($child, $config, $object, $customEnrich);
                }
            }

            $item['childs'] = $children;
        }

        if ($customEnrich) {
            $customEnrich($layout, $config, $object, $item);
        }

        return $item;
    }

    public function getCustomLayoutData(AbstractObject $object, CustomLayout $customLayout = null): array
    {
        if (empty($customLayout)) {
            return [];
        }

        return $this->getFieldDefinitionData($object, $customLayout, $customLayout->getLayoutDefinitions());
    }

    public function getCustomLayouts(string $classId): array
    {
        $list = new DataObject\ClassDefinition\CustomLayout\Listing();

        $list->setCondition('classId = ' . $list->quote($classId));
        $list = $list->load();
        $result = [];
        /** @var DataObject\ClassDefinition\CustomLayout $item */
        foreach ($list as $item) {
            $result[] = [
                'id' => $item->getId(),
                'name' => $item->getName() . ' (ID: ' . $item->getId() . ')',
            ];
        }

        return $result;
    }

    public function getCustomLayoutsSelectStore(string $classId): array
    {
        $result = [];
        foreach ($this->getCustomLayouts($classId) as $row) {
            $result[] = [$row['id'], $row['name']];
        }

        return $result;
    }

    public function getCustomLayoutVersionData(array $objects, CustomLayout $customLayout = null): array
    {
        if (empty($customLayout)) {
            return [];
        }

        $data = $this->getFieldDefinitionVersionData($objects, $customLayout, $customLayout->getLayoutDefinitions());

        foreach ($data as $name => $versions) {
            $this->compareVersions($versions);
        }

        return $data;
    }

    /**
     * @param AbstractObject $object
     * @param CustomLayout $customLayout
     * @param DataObject\ClassDefinition\Layout|DataObject\ClassDefinition\Data $layoutItem
     * @param array $data
     *
     * @return array
     */
    protected function getFieldDefinitionData(AbstractObject $object, CustomLayout $customLayout, $layoutItem, &$data = [])
    {
        if ($layoutItem->getDatatype() == 'data') {
            $value = $this->getRawFieldDefinitionData($object, $layoutItem);

            $fieldDefinitionAdapter = $this->fieldDefinitionService->getFieldDefinitionAdapter($layoutItem);

            $currentData = $data[$layoutItem->getName()] ?? null;
            $value = $fieldDefinitionAdapter->getDataForDetail($object, $value);

            if (!empty($currentData) && is_array($currentData) && is_array($value)) {
                $currentData = array_merge($currentData, $value);
            } else {
                $currentData = $value;
            }

            $data[$layoutItem->getName()] = $currentData;
        } else {
            foreach ($layoutItem->getChildren() as $child) {
                $this->getFieldDefinitionData($object, $customLayout, $child, $data);
            }
        }

        return $data;
    }

    /**
     * @param AbstractObject[] $object
     * @param CustomLayout $customLayout
     * @param DataObject\ClassDefinition\Layout|DataObject\ClassDefinition\Data $layoutItem
     * @param array $data
     *
     * @return array
     */
    protected function getFieldDefinitionVersionData(array $objects, CustomLayout $customLayout, $layoutItem, &$data = [])
    {
        if ($layoutItem->getDatatype() == 'data') {
            $fieldDefinitionAdapter = $this->fieldDefinitionService->getFieldDefinitionAdapter($layoutItem);

            $values = [];

            foreach ($objects as $object) {
                $value = $this->getRawFieldDefinitionData($object, $layoutItem);
                $value = $fieldDefinitionAdapter->getDataForVersionPreview($object, $value);

                if (is_array($value)) {
                    foreach ($value as $k => $v) {
                        $values[$k][] = $v;
                    }
                } else {
                    $values[] = $value;
                }
            }

            $data[$layoutItem->getName()] = array_merge($data[$layoutItem->getName()] ?: [], $values);
        } else {
            foreach ($layoutItem->getChildren() as $child) {
                $this->getFieldDefinitionVersionData($objects, $customLayout, $child, $data);
            }
        }

        return $data;
    }

    /**
     * @param VersionPreviewValue[]|VersionPreviewValue[][] $versions
     */
    protected function compareVersions(array $versions)
    {
        // string as keys --> associative --> nested key => versions (field collections, localized fields, ...)
        if (count(array_filter(array_keys($versions), 'is_string'))) {
            foreach ($versions as $attribute => $nestedVersions) {
                $this->compareVersions($nestedVersions);
            }

            return;
        }

        if (count($versions) <= 1) {
            return;
        }

        for ($i = 1; $i < count($versions); $i++) {
            $this->compareVersionData($versions[0], $versions[$i]);
        }
    }

    /**
     * @param VersionPreviewValue|VersionPreviewValue[]|VersionPreviewValue[][] $first
     * @param VersionPreviewValue|VersionPreviewValue[]| VersionPreviewValue[][] $current
     */
    protected function compareVersionData($first, $current)
    {
        if (is_array($first)) {
            foreach ($first as $key => $value) {
                if (array_key_exists($key, $current)) {
                    $this->compareVersionData($value, $current[$key]);
                }
            }
        } elseif ($first instanceof VersionPreviewValue && $current instanceof VersionPreviewValue && $first->getValue() !== $current->getValue()) {
            $current->setDirty(true);
        }
    }

    /**
     * @param AbstractObject $object
     * @param CustomLayout $customLayout
     * @param array $layoutItem
     *
     * @return mixed
     */
    protected function getRawFieldDefinitionData(AbstractObject $object, DataObject\ClassDefinition\Data $fieldDefinition)
    {
        if ($fieldDefinition instanceof ReverseObjectRelation) {
            $refKey = $fieldDefinition->getOwnerFieldName();

            $refClass = DataObject\ClassDefinition::getByName($fieldDefinition->getOwnerClassName());
            if ($refClass) {
                $refId = $refClass->getId();
            }

            return $object->getRelationData($refKey, false, $refId);
        } else {
            return $object->get($fieldDefinition->getName());
        }
    }
}
