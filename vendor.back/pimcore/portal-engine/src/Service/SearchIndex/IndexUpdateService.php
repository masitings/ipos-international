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

namespace Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex;

use Pimcore\Bundle\PortalEngineBundle\Enum\Index\Statistics\ElasticSearchAlias;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Asset\IndexService as AssetIndexService;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\DataObject\IndexService as DataObjectIndexService;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Listing;

/**
 * Class IndexUpdateService
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex
 */
class IndexUpdateService
{
    /** @var IndexQueueService */
    protected $indexQueueService;
    /** @var DataObjectIndexService */
    protected $dataObjectIndexService;
    /** @var AssetIndexService */
    protected $assetIndexService;

    /** @var bool */
    protected $reCreateIndex = false;

    /**
     * IndexUpdateService constructor.
     *
     * @param IndexQueueService $indexQueueService
     * @param DataObjectIndexService $dataObjectIndexService
     * @param AssetIndexService $assetIndexService
     */
    public function __construct(IndexQueueService $indexQueueService, DataObjectIndexService $dataObjectIndexService, AssetIndexService $assetIndexService)
    {
        $this->indexQueueService = $indexQueueService;
        $this->dataObjectIndexService = $dataObjectIndexService;
        $this->assetIndexService = $assetIndexService;
    }

    /**
     * @return $this
     */
    public function updateAll()
    {
        $this
            ->updateClassDefinitions()
            ->updateAssets();

        return $this;
    }

    /**
     * @return $this
     */
    public function updateClassDefinitions()
    {
        foreach ((new Listing())->load() as $classDefinition) {
            $this->updateClassDefinition($classDefinition);
        }

        return $this;
    }

    /**
     * @param ClassDefinition $classDefinition
     *
     * @return $this
     */
    public function updateClassDefinition($classDefinition)
    {
        if ($this->reCreateIndex) {
            $this
                ->dataObjectIndexService
                ->deleteIndex($classDefinition);
        }

        $this
            ->dataObjectIndexService
            ->updateMapping($classDefinition, $this->reCreateIndex);

        //add dataObjects to update queue
        $this
            ->indexQueueService
            ->updateDataObjects($classDefinition);

        $this
            ->dataObjectIndexService
            ->addClassDefinitionToAlias($classDefinition, ElasticSearchAlias::CLASS_DEFINITIONS);

        return $this;
    }

    /**
     * @return $this
     */
    public function updateAssets()
    {
        if ($this->reCreateIndex) {
            $this
                ->assetIndexService
                ->deleteIndex();
        }

        $this
            ->assetIndexService
            ->updateMapping($this->reCreateIndex);

        //add assets to update queue
        $this
            ->indexQueueService
            ->updateAssets();

        return $this;
    }

    /**
     * @param bool $reCreateIndex
     *
     * @return IndexUpdateService
     */
    public function setReCreateIndex(bool $reCreateIndex): self
    {
        $this->reCreateIndex = $reCreateIndex;

        return $this;
    }
}
