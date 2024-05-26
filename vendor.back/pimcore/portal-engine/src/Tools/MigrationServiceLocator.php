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

namespace Pimcore\Bundle\PortalEngineBundle\Tools;

use Pimcore\Bundle\PortalEngineBundle\EventSubscriber\DocumentConfigSubscriber;
use Pimcore\Bundle\PortalEngineBundle\Service\PortalConfig\FrontendBuildService;

class MigrationServiceLocator
{
    /**
     * @var FrontendBuildService
     */
    protected $frontendBuildService;

    /**
     * @var DocumentConfigSubscriber
     */
    protected $documentConfigSubscriber;

    /**
     * @param FrontendBuildService $frontendBuildService
     * @param DocumentConfigSubscriber $documentConfigSubscriber
     */
    public function __construct(FrontendBuildService $frontendBuildService, DocumentConfigSubscriber $documentConfigSubscriber)
    {
        $this->frontendBuildService = $frontendBuildService;
        $this->documentConfigSubscriber = $documentConfigSubscriber;
    }

    /**
     * @return FrontendBuildService
     */
    public function getFrontendBuildService(): FrontendBuildService
    {
        return $this->frontendBuildService;
    }

    /**
     * @return DocumentConfigSubscriber
     */
    public function getDocumentConfigSubscriber(): DocumentConfigSubscriber
    {
        return $this->documentConfigSubscriber;
    }
}
