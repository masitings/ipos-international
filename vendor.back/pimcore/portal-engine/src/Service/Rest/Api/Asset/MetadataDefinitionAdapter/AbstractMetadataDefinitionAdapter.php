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

use Pimcore\AssetMetadataClassDefinitionsBundle\Model\ClassDefinition\Data\Data;
use Pimcore\Bundle\PortalEngineBundle\Service\Asset\AttributeService;
use Pimcore\Bundle\PortalEngineBundle\Service\Asset\MetadataService;
use Pimcore\Bundle\PortalEngineBundle\Service\Element\NameExtractorService;
use Pimcore\Bundle\PortalEngineBundle\Service\Element\UrlExtractorService;
use Pimcore\Model\Asset;

abstract class AbstractMetadataDefinitionAdapter implements MetadataDefinitionAdapterInterface
{
    protected $metadataService;
    protected $attributeService;
    protected $nameExtractorService;
    protected $urlExtractorService;

    /**
     * @var Data
     */
    protected $fieldDefinition;

    public function __construct(
        MetadataService $metadataService,
        AttributeService $attributeService,
        NameExtractorService $nameExtractorService,
        UrlExtractorService $urlExtractorService
    ) {
        $this->metadataService = $metadataService;
        $this->attributeService = $attributeService;
        $this->nameExtractorService = $nameExtractorService;
        $this->urlExtractorService = $urlExtractorService;
    }

    /**
     * @param Data $fieldDefinition
     */
    public function setFieldDefinition(Data $fieldDefinition)
    {
        $this->fieldDefinition = $fieldDefinition;
    }

    /**
     * @return Data
     */
    public function getFieldDefinition(): Data
    {
        return $this->fieldDefinition;
    }

    public function setDataFromDetail(Asset $asset, $value, $parameters = [])
    {
        return $value;
    }
}
