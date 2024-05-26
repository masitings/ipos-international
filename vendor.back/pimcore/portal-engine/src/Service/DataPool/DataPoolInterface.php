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

namespace Pimcore\Bundle\PortalEngineBundle\Service\DataPool;

use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Export\ExportServiceInterface;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\IndexServiceInterface;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Search\SearchServiceInterface;

interface DataPoolInterface
{
    public function getSearchService(): SearchServiceInterface;

    public function getIndexService(): IndexServiceInterface;

    public function getExportService(): ExportServiceInterface;
}
