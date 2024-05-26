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

namespace Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Search\PreConditionServiceHandler;

use ONGR\ElasticsearchDSL\Search;

/**
 * Interface PreConditionServiceInterface
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataObject
 */
interface PreConditionServiceHandlerInterface
{
    /**
     * @param Search $search
     */
    public function addPreCondition(Search $search);
}
