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

namespace Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Export;

use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Field\ExportableField;
use Pimcore\Model\Element\ElementInterface;

/**
 * Used for generic data pool structured data download formats.
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Export\ExportServiceInterface
 */
interface ExportServiceInterface
{
    /**
     * @param ElementInterface $element
     *
     * @return ExportableField[]
     */
    public function getExportableFields($element): array;
}
