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
use Pimcore\Model\Asset;

interface MetadataDefinitionAdapterInterface
{
    /**
     * @param Data $fieldDefinition
     */
    public function setFieldDefinition(Data $fieldDefinition);

    /**
     * @param Asset $asset
     * @param $value
     * @param array $parameters
     *
     * @return mixed
     */
    public function getDataForDetail(Asset $asset, $value, $parameters = []);

    /**
     * @param Asset $asset
     * @param $value
     * @param array $parameters
     *
     * @return mixed
     */
    public function setDataFromDetail(Asset $asset, $value, $parameters = []);

    /**
     * @param Asset $asset
     * @param $value
     * @param array $parameters
     *
     * @return string
     */
    public function getNormalizedData(Asset $asset, $value, $parameters = []);
}
