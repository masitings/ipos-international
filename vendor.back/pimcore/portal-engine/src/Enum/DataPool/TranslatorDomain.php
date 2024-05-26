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

namespace Pimcore\Bundle\PortalEngineBundle\Enum\DataPool;

use MyCLabs\Enum\Enum;

/**
 * Class TranslatorDomain
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Enum\DataPool
 */
class TranslatorDomain extends Enum
{
    const TRANSLATION_DOMAIN_PREFIX = 'portal-engine';

    const DOMAIN_FILTER_LABEL = 'filter-label';
    const DOMAIN_FILTER_OPTION_LABEL = 'filter-option';
    const DOMAIN_SORT_LABEL = 'sort-label';
    const DOMAIN_LIST_LABEL = 'list-label';
    const DOMAIN_SEARCH_TAB_LABEL = 'search-tab-label';
    const DOMAIN_COLLECTION_TAB_LABEL = 'collection-tab-label';
    const DOMAIN_PUBLIC_SHARE_TAB_LABEL = 'public-share-tab-label';
    const DOMAIN_ASSET = 'asset';
    const DOMAIN_WORKFLOW = 'workflow';
    const DOMAIN_WORKFLOW_TRANSITION = 'workflow-transition';
    const DOMAIN_STATISTIC_NAME = 'statistic-name';
}
