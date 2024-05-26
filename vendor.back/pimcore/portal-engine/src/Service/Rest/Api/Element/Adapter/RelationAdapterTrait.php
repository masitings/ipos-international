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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\Element\Adapter;

use Pimcore\Bundle\PortalEngineBundle\Service\Element\NameExtractorService;
use Pimcore\Bundle\PortalEngineBundle\Service\Element\UrlExtractorService;
use Pimcore\Model\Element\ElementInterface;
use Pimcore\Model\Element\Service;

trait RelationAdapterTrait
{
    protected function getElementValues(NameExtractorService $nameExtractorService, UrlExtractorService $urlExtractorService, ?ElementInterface $element)
    {
        $data = [
            'id' => $element->getId(),
            'type' => Service::getElementType($element),
            'path' => $element->getPath(),
            'fullpath' => $element->getFullPath(),
            'name' => $nameExtractorService->extractName($element)
        ];

        if ($url = $urlExtractorService->extractUrl($element)) {
            $data['url'] = $url;
        }

        return $data;
    }

    protected function getElementById($value)
    {
        if (!is_array($value) || empty($value['id']) || empty($value['type'])) {
            return null;
        }

        return Service::getElementById($value['type'], $value['id']);
    }
}
