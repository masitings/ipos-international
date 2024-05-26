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

use Pimcore\Bundle\PortalEngineBundle\Controller\DataPool\DataObjectController;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\DataObject\Export\ExportService;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\DataObject\IndexService;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\DataObject\SearchService;

class DataObject extends AbstractDataPool
{
    /**
     * @var SearchService
     */
    protected $searchService;

    /**
     * @var IndexService
     */
    protected $indexService;

    /**
     * @var ExportService
     */
    protected $exportService;

    /**
     * DataObject constructor.
     *
     * @param SearchService $searchService
     */
    public function __construct(SearchService $searchService, IndexService $indexService, ExportService $exportService)
    {
        $this->searchService = $searchService;
        $this->indexService = $indexService;
        $this->exportService = $exportService;
    }

    public function getFrontendControllerClass(): string
    {
        return DataObjectController::class;
    }
}
