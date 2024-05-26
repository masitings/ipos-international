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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api;

use Pimcore\Bundle\PortalEngineBundle\Enum\Permission;
use Pimcore\Bundle\PortalEngineBundle\Model\Rest\Api\SearchGroup;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolService;
use Pimcore\Translation\Translator;
use Symfony\Component\Security\Core\Security;

/**
 * Class SearchHandler
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api
 */
class SearchHandler
{
    /** @var ErrorHandler */
    protected $errorHandler;
    /** @var Translator */
    protected $translator;
    /** @var DataPoolService */
    protected $dataPoolService;
    /** @var DataPoolConfigService */
    protected $dataPoolConfigService;
    /** @var Security */
    protected $security;

    /**
     * SearchHandler constructor.
     *
     * @param ErrorHandler $errorHandler
     * @param Translator $translator
     * @param DataPoolService $dataPoolService
     * @param DataPoolConfigService $dataPoolConfigService
     * @param Security $security
     */
    public function __construct(ErrorHandler $errorHandler, Translator $translator, DataPoolService $dataPoolService, DataPoolConfigService $dataPoolConfigService, Security $security)
    {
        $this->errorHandler = $errorHandler;
        $this->translator = $translator;
        $this->dataPoolService = $dataPoolService;
        $this->dataPoolConfigService = $dataPoolConfigService;
        $this->security = $security;
    }

    /**
     * @param array $searchTerms
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getSmartSuggestData(array $searchTerms)
    {
        /** @var array $smartSuggestData */
        $smartSuggestData = [];

        if (empty($searchTerms)) {
            $this->errorHandler->setErrorMessage($this->translator->trans('portal-engine.search.error.empty-search-term', [], 'messages'));
        } else {
            foreach ($this->getSearchGroupsBySearchTerms($searchTerms) as $searchGroup) {
                $smartSuggestData['groups'][] = [
                    'id' => $searchGroup->getDataPoolConfigId(),
                    'name' => $searchGroup->getName(),
                    'icon' => $searchGroup->getIcon(),
                    'url' => $searchGroup->getUrl(),
                ];

                foreach ($searchGroup->getItems() as $searchItem) {
                    $smartSuggestData['items'][] = [
                        'label' => $searchItem->getLabel(),
                        'id' => $searchItem->getId(),
                        'url' => $searchItem->getDetailLink(),
                        'img' => $searchItem->getThumbnail(),
                        'groupId' => $searchGroup->getDataPoolConfigId()
                    ];
                }
            }
        }

        return $smartSuggestData;
    }

    /**
     * @param array $searchTerms
     *
     * @return SearchGroup[]
     *
     * @throws \Exception
     */
    public function getFullTextData(array $searchTerms)
    {
        /** @var array $searchGroups */
        $searchGroups = [];

        if (!empty($searchTerms)) {
            foreach ($this->getSearchGroupsBySearchTerms($searchTerms, 1) as $searchGroup) {
                $searchGroups[$searchGroup->getDataPoolConfigId()] = $searchGroup;
            }
        }

        return $searchGroups;
    }

    /**
     * @param array $searchTerms
     * @param int $searchItemSize
     *
     * @return array|SearchGroup[]
     *
     * @throws \Exception
     */
    protected function getSearchGroupsBySearchTerms(array $searchTerms, int $searchItemSize = 5)
    {
        /** @var SearchGroup[] $searchGroups */
        $searchGroups = [];

        foreach ($this->dataPoolConfigService->getDataPoolConfigsFromSite(null, true) as $dataPoolConfig) {

            //set current loop $dataPoolConfig reference in DataPoolConfigService
            $this
                ->dataPoolConfigService
                ->setCurrentDataPoolConfig($dataPoolConfig);

            //deny access if not granted for current dataPool
            if ($this->security->isGranted(Permission::DATA_POOL_ACCESS)) {

                /** @var SearchGroup $searchGroup */
                $searchGroup = $dataPool = $this
                    ->dataPoolService
                    ->getDataPoolByConfig($dataPoolConfig)
                    ->getSearchService()
                    ->getSearchGroupForTerms($searchTerms, $searchItemSize);

                if ($searchGroup->hasItems()) {
                    $searchGroup->setType($dataPoolConfig->getElementType());

                    $searchGroups[] = $searchGroup;
                }
            }
        }

        return $searchGroups;
    }
}
