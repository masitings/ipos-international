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

use Pimcore\Bundle\PortalEngineBundle\Service\Element\NameExtractorService;
use Pimcore\Bundle\PortalEngineBundle\Service\Element\UrlExtractorService;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Data\ElementMetadata;

class AdvancedManyToManyRelationAdapter extends DefaultAdapter
{
    protected $nameExtractorService;
    protected $urlExtractorService;

    public function __construct(
        NameExtractorService $nameExtractorService,
        UrlExtractorService $urlExtractorService
    ) {
        $this->nameExtractorService = $nameExtractorService;
        $this->urlExtractorService = $urlExtractorService;
    }

    /**
     *
     * @param AbstractObject $object
     * @param ElementMetadata[] $data
     * @param array $params
     *
     * @return array|mixed
     */
    public function getDataForDetail(AbstractObject $object, $data, array $params = [])
    {
        if (empty($data)) {
            return null;
        }

        $result = [
            'meta' => [],
            'data' => []
        ];

        foreach ($data as $item) {
            if (empty($result['meta'])) {
                $result['meta'] = $item->getColumns();
            }

            $element = $item->getElement();

            $dataSet = [
                'name' => $this->nameExtractorService->extractName($element),
                'id' => $element->getId(),
                'path' => $element->getRealFullPath(),
                'type' => $element->getType(),
                'subtype' => $element->getType(),
            ];

            if ($url = $this->urlExtractorService->extractUrl($element)) {
                $dataSet['url'] = $url;
            }

            foreach ($item->getColumns() as $column) {
                $getter = 'get' . ucfirst($column);
                $dataSet[$column] = $item->$getter();
            }

            $result['data'][] = $dataSet;
        }

        return $result;
    }
}
