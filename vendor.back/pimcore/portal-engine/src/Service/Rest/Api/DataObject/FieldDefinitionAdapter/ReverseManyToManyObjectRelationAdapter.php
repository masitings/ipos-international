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
use Pimcore\Model\Asset\Image;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\Element;

class ReverseManyToManyObjectRelationAdapter extends DefaultAdapter
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
     * @param Image $data
     * @param array $params
     *
     * @return array|mixed
     */
    public function getDataForDetail(AbstractObject $object, $data, array $params = [])
    {
        $result = [];

        foreach ($data as $row) {
            if ($element = Element\Service::getElementById($row['type'], $row['id'])) {
                $item = [
                    'id' => intval($row['id']),
                    'type' => $row['type'],
                    'subtype' => $row['subtype'],
                    'path' => $row['path'],
                    'published' => (bool) $row['published'],
                    'name' => $this->nameExtractorService->extractName($element)
                ];

                if ($url = $this->urlExtractorService->extractUrl($element)) {
                    $item['url'] = $this->urlExtractorService->extractUrl($element);
                }

                $result[] = $item;
            }
        }

        return $result;
    }
}
