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

namespace Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Search;

use ONGR\ElasticsearchDSL\Search;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Search\PreConditionServiceHandler\PreConditionServiceHandlerInterface;

/**
 * Class PreConditionService
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\DataObject\Search
 */
class PreConditionService
{
    /** @var DataPoolConfigService */
    protected $dataPoolConfigService;
    /** @var PreConditionServiceHandlerInterface[] */
    protected $preConditionServiceHandlers = [];

    /**
     * PreConditionService constructor.
     *
     * @param DataPoolConfigService $dataPoolConfigService
     */
    public function __construct(DataPoolConfigService $dataPoolConfigService)
    {
        $this->dataPoolConfigService = $dataPoolConfigService;
    }

    /**
     * @param Search $search
     */
    public function applyElasticSearchPreConditions(Search $search)
    {
        /**
         * @var string|null $preConditionServiceId
         */
        $preConditionServiceId = $this->dataPoolConfigService->getCurrentDataPoolConfig()->getPreconditionServiceId();

        if ($preConditionServiceId) {
            /**
             * @var PreConditionServiceHandlerInterface|null $preConditionService
             */
            $preConditionService = $this->getPreConditionServiceHandler($preConditionServiceId);

            if ($preConditionService) {
                $preConditionService->addPreCondition($search);
            }
        }
    }

    /**
     * @param string $serviceId
     * @param PreConditionServiceHandlerInterface $preConditionService
     */
    public function addPreconditionServiceHandler(string $serviceId, PreConditionServiceHandlerInterface $preConditionService)
    {
        $this->preConditionServiceHandlers[$serviceId] = $preConditionService;
    }

    /**
     * @return array
     */
    public function getPreConditionServicesSelectStore(): array
    {
        $result = [];

        //add empty option for select editable
        $result[] = ['none', '-'];

        foreach (array_keys($this->preConditionServiceHandlers) as $serviceId) {
            $result[] = [$serviceId, $serviceId];
        }

        return $result;
    }

    /**
     * @param string $serviceId
     *
     * @return PreConditionServiceHandlerInterface|null
     */
    public function getPreConditionServiceHandler(string $serviceId): ?PreConditionServiceHandlerInterface
    {
        return $this->preConditionServiceHandlers[$serviceId] ?? null;
    }
}
