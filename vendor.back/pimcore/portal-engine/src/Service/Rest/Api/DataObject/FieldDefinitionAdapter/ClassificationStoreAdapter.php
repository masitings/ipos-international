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
use Pimcore\Bundle\PortalEngineBundle\Service\DataObject\ClassificationStoreLayoutService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataObject\RestApiFieldDefinitionService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\LanguagesService;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Classificationstore;
use Pimcore\Model\DataObject\Classificationstore\DefinitionCache;
use Pimcore\Model\DataObject\Classificationstore\GroupConfig;

class ClassificationStoreAdapter extends DefaultAdapter
{
    protected $classificationStoreLayoutService;
    protected $fieldDefinitionService;
    protected $languagesService;

    public function __construct(ClassificationStoreLayoutService $classificationStoreLayoutService, RestApiFieldDefinitionService $fieldDefinitionService, LanguagesService $languagesService)
    {
        $this->classificationStoreLayoutService = $classificationStoreLayoutService;
        $this->fieldDefinitionService = $fieldDefinitionService;
        $this->languagesService = $languagesService;
    }

    /**
     * @param AbstractObject $object
     * @param Classificationstore $data
     * @param array $params
     *
     * @return array|mixed
     */
    public function getDataForDetail(AbstractObject $object, $data, array $params = [])
    {
        return $this->getData($object, $data, $params);
    }

    /**
     * {@inheritDoc}
     */
    public function getDataForVersionPreview(AbstractObject $object, $data, array $params = [])
    {
        $data = $this->getData($object, $data, $params, true);
        $values = [];

        if (!empty($data)) {
            foreach ($data as $languageData) {
                if (empty($languageData['groups'])) {
                    continue;
                }

                foreach ($languageData['groups'] as $groupData) {
                    if (empty($groupData['keys'])) {
                        continue;
                    }

                    foreach ($groupData['keys'] as $keyData) {
                        $key = "{$this->fieldDefinition->getName()}_{$languageData['language']}_{$groupData['id']}_{$keyData['id']}";

                        $values[$key] = new VersionPreviewValue(
                            $key,
                            [$keyData['value']->getLabel(), "[{$languageData['language']}]"],
                            $keyData['value']->getValue()
                        );
                    }
                }
            }
        }

        return $values;
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
        $store = $data;
        $data = parent::getDataForDetail($object, $data, $params);

        $result = [];

        foreach ($data['data'] as $language => $groups) {
            $groupsData = [];

            foreach ($groups as $groupId => $keys) {
                $group = GroupConfig::getById($groupId);

                if (empty($group)) {
                    continue;
                }

                $groupData = [
                    'id' => $groupId,
                    'name' => $group->getName(),
                    'keys' => [],
                ];

                foreach ($keys as $keyId => $value) {
                    try {
                        $keyConfig = DefinitionCache::get($keyId);
                        $this->classificationStoreLayoutService->addUsedKeyConfig($object->getClass()->getName(), $keyConfig);
                        $storeValue = $store->getLocalizedKeyValue($groupId, $keyId, $language, true, true);
                        $adapter = $this->fieldDefinitionService->getFieldDefinitionAdapter(Classificationstore\Service::getFieldDefinitionFromKeyConfig($keyConfig));

                        $groupData['keys'][] = [
                            'id' => $keyId,
                            'value' => !$versionPreview ? $adapter->getDataForDetail($object, $storeValue, $params) : $adapter->getDataForVersionPreview($object, $storeValue, $params),
                        ];
                    } catch (\Exception $e) {
                    }
                }

                $groupsData[] = $groupData;
            }

            $result[] = [
                'language' => $language,
                'groups' => $groupsData,
            ];

            uasort($result, function ($a, $b) {
                return $this->languagesService->compareLanguages($a['language'], $b['language']);
            });

            // keep it an array (otherwise its an object after json encoding)
            $result = array_values($result);
        }

        return $result;
    }
}
