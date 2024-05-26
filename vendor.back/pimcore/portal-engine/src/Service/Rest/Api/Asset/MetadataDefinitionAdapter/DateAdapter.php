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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\Asset\MetadataDefinitionAdapter;

use Carbon\Carbon;
use Pimcore\Bundle\PortalEngineBundle\Service\Asset\AttributeService;
use Pimcore\Bundle\PortalEngineBundle\Service\Asset\MetadataService;
use Pimcore\Bundle\PortalEngineBundle\Service\Element\NameExtractorService;
use Pimcore\Bundle\PortalEngineBundle\Service\Element\UrlExtractorService;
use Pimcore\Localization\IntlFormatter;
use Pimcore\Model\Asset;

class DateAdapter extends AbstractMetadataDefinitionAdapter
{
    protected $formatter;

    public function __construct(
        MetadataService $metadataService,
        AttributeService $attributeService,
        NameExtractorService $nameExtractorService,
        UrlExtractorService $urlExtractorService,
        IntlFormatter $formatter
    ) {
        parent::__construct($metadataService, $attributeService, $nameExtractorService, $urlExtractorService);

        $this->formatter = $formatter;
    }

    public function getDataForDetail(Asset $asset, $value, $parameters = [])
    {
        // convert date to timestamp
        if ($value instanceof Carbon) {
            return $value->getTimestamp();
        }

        // already timestamp
        if (is_numeric($value)) {
            return $value;
        }

        return null;
    }

    public function setDataFromDetail(Asset $asset, $value, $parameters = [])
    {
        if (!is_numeric($value)) {
            return null;
        }

        return $value;
    }

    public function getNormalizedData(Asset $asset, $value, $parameters = [])
    {
        $timestamp = $this->getDataForDetail($asset, $value, $parameters);

        if (!$timestamp) {
            return null;
        }

        try {
            $carbon = Carbon::createFromTimestamp($timestamp);

            return $this->formatter->formatDateTime($carbon);
        } catch (\Exception $e) {
            return null;
        }
    }
}
