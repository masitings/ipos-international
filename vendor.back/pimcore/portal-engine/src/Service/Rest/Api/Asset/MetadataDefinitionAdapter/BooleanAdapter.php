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

use Pimcore\Bundle\PortalEngineBundle\Service\Asset\AttributeService;
use Pimcore\Bundle\PortalEngineBundle\Service\Asset\MetadataService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\TranslatorService;
use Pimcore\Bundle\PortalEngineBundle\Service\Element\NameExtractorService;
use Pimcore\Bundle\PortalEngineBundle\Service\Element\UrlExtractorService;
use Pimcore\Model\Asset;

class BooleanAdapter extends AbstractMetadataDefinitionAdapter
{
    protected $translatorService;

    public function __construct(
        MetadataService $metadataService,
        AttributeService $attributeService,
        NameExtractorService $nameExtractorService,
        UrlExtractorService $urlExtractorService,
        TranslatorService $translatorService
    ) {
        parent::__construct($metadataService, $attributeService, $nameExtractorService, $urlExtractorService);

        $this->translatorService = $translatorService;
    }

    public function getDataForDetail(Asset $asset, $value, $parameters = [])
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
    }

    public function setDataFromDetail(Asset $asset, $value, $parameters = [])
    {
        return $value === null ? null : ($value ? 1 : 0);
    }

    public function getNormalizedData(Asset $asset, $value, $parameters = [])
    {
        return $this->getDataForDetail($asset, $value, $parameters) ?
            $this->translatorService->translate('yes') :
            $this->translatorService->translate('no');
    }
}
