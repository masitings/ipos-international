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
use Pimcore\Bundle\PortalEngineBundle\Service\DataObject\RestApiFieldDefinitionService;
use Pimcore\Model\Asset\Image;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\ClassDefinition\Data\Localizedfields;
use Pimcore\Tool;

class LocalizedFieldsAdapter extends DefaultAdapter
{
    const PARAM_FIELD_COLLECTION = 'localizedFieldCollection';

    /**
     * @var RestApiFieldDefinitionService
     */
    protected $fieldDefinitionService;

    /**
     * LocalizedFieldsAdapter constructor.
     *
     * @param RestApiFieldDefinitionService $fieldDefinitionService
     */
    public function __construct(RestApiFieldDefinitionService $fieldDefinitionService)
    {
        $this->fieldDefinitionService = $fieldDefinitionService;
    }

    /**
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
        $previewData = [];

        foreach ($data as $attribute => $languageValues) {
            foreach ($languageValues as $language => $value) {
                $key = "{$attribute}_{$language}";

                $previewData[$key] = new VersionPreviewValue($key, [$value->getLabel(), "[$language]"], $value->getValue());
            }
        }

        return $previewData;
    }

    protected function getData(AbstractObject $object, $data, array $params = [], bool $versionPreview = false)
    {
        if (empty($data)) {
            return [];
        }

        /**
         * @var Localizedfields $fieldDefinition
         */
        $fieldDefinition = $this->fieldDefinition;

        $result = [];
        foreach ($fieldDefinition->getFieldDefinitions() as $fd) {
            $values = [];
            $objectContainer = $params[self::PARAM_FIELD_COLLECTION] ?? $object;

            foreach (Tool::getValidLanguages() as $language) {
                $value = $objectContainer->{'get' . ucfirst($fd->getName())}($language);
                $fieldDefinitionAdapter = $this->fieldDefinitionService->getFieldDefinitionAdapter($fd);
                $values[$language] = !$versionPreview ? $fieldDefinitionAdapter->getDataForDetail($object, $value) : $fieldDefinitionAdapter->getDataForVersionPreview($object, $value);
            }

            $result[$fd->getName()] = $values;
        }

        return $result;
    }
}
