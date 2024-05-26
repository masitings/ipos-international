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

use Elasticsearch\Client;
use ONGR\ElasticsearchDSL\Search;
use Pimcore\Bundle\PortalEngineBundle\Model\Rest\Api\DataPool\ListData;
use Pimcore\Model\Element\ElementInterface;

interface SearchServiceInterface
{
    /**
     * @param ElementInterface|mixed $item
     *
     * @return bool
     */
    public function isItemInDataPool($item): bool;

    public function getWorkspaceService(): WorkspaceServiceInterface;

    /**
     * @param array $params
     *
     * @return ListData
     *
     * @throws \Exception
     */
    public function getListDataByParams(array $params = []);

    /**
     * @param array $params
     *
     * @return Search
     *
     * @throws \Exception
     */
    public function getSearchByParams(array $params = []);

    /**
     * @return Client
     */
    public function getEsClient(): Client;

    /**
     * @param $tieBreakerId
     * @param array $params
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getItemsBeforeAndAfterByParams($tieBreakerId, array $params = []);

    /**
     * @return string
     */
    public function getESIndexName();

    /**
     * @param string $permission
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function hasItemsWithPermission(string $permission, array $params = []): bool;

    /**
     * @return bool
     *
     * @throws \Exception
     */
    public function hasTags(): bool;
}
