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

namespace Pimcore\Bundle\PortalEngineBundle\EventSubscriber;

use Pimcore\Bundle\PortalEngineBundle\Enum\Document\Editables;
use Pimcore\Bundle\PortalEngineBundle\Model\ElementDataAware;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolService;
use Pimcore\Bundle\PortalEngineBundle\Service\PortalConfig\DefaultValuesService;
use Pimcore\Bundle\PortalEngineBundle\Service\PortalConfig\FrontendBuildService;
use Pimcore\Bundle\PortalEngineBundle\Service\PortalConfig\PortalConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Search\SearchServiceInterface;
use Pimcore\Event\DocumentEvents;
use Pimcore\Event\Model\DocumentEvent;
use Pimcore\Model\Document\Editable;
use Pimcore\Model\Document\Editable\Block\Item;
use Pimcore\Model\Document\Editable\Input;
use Pimcore\Model\Document\Page;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class IndexUpdateListener
 *
 * @package Pimcore\Bundle\PortalEngineBundle\EventListener
 */
class DocumentConfigSubscriber implements EventSubscriberInterface
{
    use ElementDataAware;

    /**
     * @var DataPoolConfigService
     */
    protected $dataPoolConfigService;

    /**
     * @var DataPoolService
     */
    protected $dataPoolService;

    /**
     * @var PortalConfigService
     */
    protected $portalConfigService;

    /**
     * @var DefaultValuesService
     */
    protected $defaultValuesService;

    /**
     * @var FrontendBuildService
     */
    protected $frontendBuildService;

    /**
     * @var bool $updatePortalsJson
     */
    protected $updatePortalsJson = false;

    /**
     * @var bool
     */
    protected $forceUpdatePortalsJson = false;

    /**
     * @var []
     */
    protected $usedParamNames;

    public function __construct(DataPoolConfigService $dataPoolConfigService, DataPoolService $dataPoolService, PortalConfigService $portalConfigService, DefaultValuesService $defaultValuesService, FrontendBuildService $frontendBuildService)
    {
        $this->dataPoolConfigService = $dataPoolConfigService;
        $this->dataPoolService = $dataPoolService;
        $this->portalConfigService = $portalConfigService;
        $this->defaultValuesService = $defaultValuesService;
        $this->frontendBuildService = $frontendBuildService;
    }

    public static function getSubscribedEvents()
    {
        return [
            DocumentEvents::PRE_UPDATE => 'onDocumentSave',
            DocumentEvents::PRE_ADD => 'onDocumentAdd',
            DocumentEvents::POST_ADD => 'triggerUpdatePortalsJson',
            DocumentEvents::POST_DELETE => 'triggerUpdatePortalsJson',
            KernelEvents::TERMINATE => 'onTerminate',
            ConsoleEvents::TERMINATE => [['onTerminate', -1000]]
        ];
    }

    /**
     * @param DocumentEvent $event
     *
     * @throws \Exception
     */
    public function onDocumentSave(DocumentEvent $event)
    {
        $document = $event->getDocument();

        if ($this->dataPoolConfigService->isDataPoolConfigDocument($document)) {
            $this->dataPoolConfigService->setCurrentDataPoolConfigById($document->getId());
            $dataPool = $this->dataPoolService->getDataPoolByConfig($this->dataPoolConfigService->getCurrentDataPoolConfig());

            $this->usedParamNames = [];

            /**
             * @var Page $document ;
             */
            if ($block = $document->getEditable(Editables\DataPool\DataPoolConfig::GRID_CONFIGURATION_FILTERS)) {

                /**
                 * @var Item $blockItem
                 */
                $indices = $block->getData();

                foreach ($block->getElements() as $i => $blockItem) {
                    $index = $indices[$i];
                    $filterType = $this->getBlockItemElementData($blockItem, Editables\DataPool\DataPoolConfig\FilterDefinition::FILTER_TYPE);
                    $filterAttribute = $this->getBlockItemElementData($blockItem, Editables\DataPool\DataPoolConfig\FilterDefinition::FILTER_ATTRIBUTE);

                    if (empty($filterType) || empty($filterAttribute)) {
                        continue;
                    }

                    $uniqueName = $this->getUniqueFilterParamName($dataPool->getSearchService(), $filterAttribute);

                    $parentBlockNames = [Editables\DataPool\DataPoolConfig::GRID_CONFIGURATION_FILTERS];
                    $id = Editable::buildChildEditableName(
                        Editables\DataPool\DataPoolConfig\FilterDefinition::FILTER_PARAM_NAME, 'input', $parentBlockNames, $index
                    );
                    $tag = new Input();
                    $tag->setDataFromEditmode($uniqueName);
                    $tag->setParentBlockNames($parentBlockNames);
                    $tag->setName($id);
                    $document->setEditable($tag);
                }
            }

            $this->usedParamNames = [];

            /**
             * @var Page $document ;
             */
            if ($block = $document->getEditable(Editables\DataPool\DataPoolConfig::GRID_CONFIGURATION_SORT_OPTIONS)) {

                /**
                 * @var Item $blockItem
                 */
                $indices = $block->getData();
                foreach ($block->getElements() as $i => $blockItem) {
                    $index = $indices[$i];
                    $direction = $this->getBlockItemElementData($blockItem, Editables\DataPool\DataPoolConfig\SortOption::DIRECTION);
                    $field = $this->getBlockItemElementData($blockItem, Editables\DataPool\DataPoolConfig\SortOption::FIELD);

                    if (empty($direction) || empty($field)) {
                        continue;
                    }

                    $uniqueName = $this->getUniqueSortParamName($dataPool->getSearchService(), $field, $direction);

                    $parentBlockNames = [Editables\DataPool\DataPoolConfig::GRID_CONFIGURATION_SORT_OPTIONS];
                    $id = Editable::buildChildEditableName(Editables\DataPool\DataPoolConfig\SortOption::PARAM_NAME, 'input', $parentBlockNames, $index);
                    $tag = new Input();
                    $tag->setDataFromEditmode($uniqueName);
                    $tag->setParentBlockNames($parentBlockNames);
                    $tag->setName($id);
                    $document->setEditable($tag);
                }
            }
        } elseif ($document instanceof Page && $this->portalConfigService->isPortalEnginePortal($document)) {
            $this->updatePortalsJson = true;
        }
    }

    /**
     * @param DocumentEvent $event
     *
     * @throws \Exception
     */
    public function onDocumentAdd(DocumentEvent $event)
    {
        $document = $event->getDocument();

        if ($document instanceof Page && $this->portalConfigService->isPortalEnginePortal($document)) {
            $this->defaultValuesService->setPortalPreCreateDefaultConfig($document);
        }
    }

    /**
     * @param DocumentEvent $event
     *
     * @throws \Exception
     */
    public function triggerUpdatePortalsJson(DocumentEvent $event)
    {
        $document = $event->getDocument();
        if ($document instanceof Page && $this->portalConfigService->isPortalEnginePortal($document)) {
            $this->updatePortalsJson = true;
        }
    }

    public function onTerminate(/*Event*/ $terminateEvent)
    {
        if ($this->updatePortalsJson) {
            $this->frontendBuildService->updatePortalsJson(true, $this->forceUpdatePortalsJson);
        }
    }

    /**
     * @param bool $updatePortalsJson
     */
    public function setUpdatePortalsJson(bool $updatePortalsJson, bool $forceUpdatePortalsJson = false)
    {
        $this->updatePortalsJson = $updatePortalsJson;
        $this->forceUpdatePortalsJson = $forceUpdatePortalsJson;
    }

    /**
     * @param string $name
     * @param int $count
     *
     * @return mixed
     *
     * @throws \Exception
     */
    protected function getUniqueFilterParamName(SearchServiceInterface $searchService, $name, $count = 1)
    {
        if ($count === 1) {
            $filterableFields = $searchService->getFilterableFieldsMapping();
            $name = isset($filterableFields[$name]) ? $filterableFields[$name]->getName() : $name;
        }
        $nameWithoutCount = $name;
        if ($count > 1) {
            $name .= $count;
        }

        if (!in_array($name, $this->usedParamNames)) {
            $this->usedParamNames[] = $name;

            return $name;
        }

        return $this->getUniqueFilterParamName($searchService, $nameWithoutCount, $count + 1);
    }

    /**
     * @param string $name
     * @param string $direction
     * @param int $count
     *
     * @return mixed
     *
     * @throws \Exception
     */
    protected function getUniqueSortParamName(SearchServiceInterface $searchService, $name, $direction, $count = 1)
    {
        if ($count === 1) {
            $sortableFields = $searchService->getSortableFieldsMapping();
            $name = isset($sortableFields[$name]) ? $sortableFields[$name]->getName() : $name;
            $name .= '#' . $direction;
        }
        $nameWithoutCount = $name;
        if ($count > 1) {
            $name .= $count;
        }

        if (!in_array($name, $this->usedParamNames)) {
            $this->usedParamNames[] = $name;

            return $name;
        }

        return $this->getUniqueSortParamName($searchService, $nameWithoutCount, $direction, $count + 1);
    }
}
