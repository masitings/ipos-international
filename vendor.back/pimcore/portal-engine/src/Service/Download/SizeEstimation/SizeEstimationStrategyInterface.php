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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Download\SizeEstimation;

use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadItemInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadSize;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\ClassDefinition;

interface SizeEstimationStrategyInterface
{
    /**
     * @param ClassDefinition $classDefinition
     *
     * @return array
     */
    public function getCustomDataObjectMappingForIndex(ClassDefinition $classDefinition): array;

    /**
     * @param AbstractObject $dataObject
     *
     * @return array
     */
    public function getCustomDataObjectDataForIndex(AbstractObject $dataObject): array;

    /**
     * @return array
     */
    public function getCustomAssetMappingForIndex(): array;

    /**
     * @return array
     */
    public function getCustomAssetDataForIndex(Asset $asset): array;

    /**
     * @param DownloadItemInterface[] $downloadItems
     *
     * @return DownloadSize
     */
    public function estimate(array $downloadItems): DownloadSize;
}
