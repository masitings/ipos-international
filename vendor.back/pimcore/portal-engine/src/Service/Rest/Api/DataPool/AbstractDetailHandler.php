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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\DataPool;

use Pimcore\Bundle\PortalEngineBundle\Model\Rest\Api\DataPool\ListDataEntry;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Search\SearchServiceInterface;

abstract class AbstractDetailHandler
{
    /**
     * @var SearchServiceInterface
     */
    protected $searchService;

    /**
     * @param SearchServiceInterface $searchService
     * @required
     */
    public function setSearchService(SearchServiceInterface $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * @param $id
     * @param array $params
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getResultListData($id, array $params = []): array
    {
        $items = $this->searchService->getItemsBeforeAndAfterByParams($id, $params);

        $items = array_map(
            function (ListDataEntry $item) use ($params) {
                return $this->getResultListItemData($item, $params);
            },
            $items
        );

        return [
            'activeItem' => intval($id),
            'items' => $items
        ];
    }

    public function getResultListItemData(ListDataEntry $item, array $params): array
    {
        return [
            'id' => $item->getId(),
            'name' => $item->getName(),
            'thumbnail' => $item->getThumbnail(),
            'detailLink' => $item->getDetailLink($params)
        ];
    }
}
