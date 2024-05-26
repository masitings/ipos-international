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

class ManyToManyRelationAdapter extends DefaultAdapter
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
        if (empty($data)) {
            return null;
        }

        $result = [];

        /**
         * @var Element\ElementInterface $element
         */
        foreach ($data as $element) {
            $item = [
                'id' => $element->getId(),
                'path' => $element->getRealFullPath(),
                'subtype' => $element->getType(),
                'type' => Element\Service::getElementType($element),
                'published' => Element\Service::isPublished($element),
                'name' => $this->nameExtractorService->extractName($element)
            ];

            if ($url = $this->urlExtractorService->extractUrl($element)) {
                $item['url'] = $url;
            }

            $result[] = $item;
        }

        return $result;
    }
}
