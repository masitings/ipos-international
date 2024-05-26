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

namespace Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Asset\FieldDefinitionAdapter;

use Pimcore\Bundle\PortalEngineBundle\Enum\ElasticSearchFields;
use Pimcore\Bundle\PortalEngineBundle\Service\Element\NameExtractorService;
use Pimcore\Model\Element\ElementInterface;
use Pimcore\Model\Element\Service;

/**
 * Class ManyToOneRelationAdapter
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Asset\FieldDefinitionAdapter
 */
class ManyToOneRelationAdapter extends DefaultAdapter implements FieldDefinitionAdapterInterface
{
    /** @var NameExtractorService */
    protected $nameExtractorService;

    /**
     * @param NameExtractorService $nameExtractorService
     * @required
     */
    public function setNameExtractorService(NameExtractorService $nameExtractorService): void
    {
        $this->nameExtractorService = $nameExtractorService;
    }

    /**
     * @return array
     */
    public function getESMapping()
    {
        $nameFields = [
            'raw' => [
                'type' => ElasticSearchFields::TYPE_KEYWORD
            ]
        ];

        $searchAttributes = $this->elasticSearchConfigService->getSearchSettings()['search_analyzer_attributes'][ElasticSearchFields::TYPE_KEYWORD]['fields'] ?? [];
        if (!empty($searchAttributes)) {
            $nameFields = array_merge($searchAttributes, $nameFields);
        }

        return [
            $this->fieldDefinition->getName(),
            [
                'properties' => [
                    'id' => [
                        'type' => ElasticSearchFields::TYPE_LONG
                    ],
                    'type' => [
                        'type' => ElasticSearchFields::TYPE_TEXT
                    ],
                    'name' => [
                        'type' => ElasticSearchFields::TYPE_TEXT,
                        'fields' => $nameFields
                    ],
                ]
            ]
        ];
    }

    /**
     * @return bool
     */
    public function isFilterable()
    {
        return true;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return 'name.raw';
    }

    /**
     * @param ElementInterface $element
     *
     * @return array
     */
    protected function getArrayValuesByElement($element)
    {
        return [
            'id' => $element->getId(),
            'name' => $this->nameExtractorService->extractName($element),
            'type' => Service::getElementType($element)
        ];
    }

    public function exportDataToString($exportData): string
    {
        if (!is_array($exportData)) {
            return '';
        }

        return (string) $exportData['name'];
    }
}
