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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Download\Provider\DataObjectFieldExtractor;

use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataObjectConfig;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadType;
use Pimcore\Model\DataObject\ClassDefinition\Data;

interface DataObjectFieldExtractorInterface
{
    /**
     * @param Data $fieldDefinition
     *
     * @return bool
     */
    public function supports(Data $fieldDefinition): bool;

    /**
     * @param DataObjectConfig $config
     * @param Data $fieldDefinition
     * @param array $context
     *
     * @return DownloadType|DownloadType[]
     */
    public function extract(DataObjectConfig $config, Data $fieldDefinition, array $context = []);

    /**
     * @param mixed $data
     *
     * @return bool
     */
    public function canTransform($data): bool;

    /**
     * @param mixed $data
     *
     * @return mixed
     */
    public function transform($data);
}
