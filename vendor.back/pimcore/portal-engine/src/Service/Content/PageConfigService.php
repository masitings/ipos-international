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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Content;

use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\Content\PageConfig;
use Pimcore\Model\Document\PageSnippet;

class PageConfigService
{
    /**
     * @param PageSnippet $document
     *
     * @return PageConfig
     */
    public function createPageConfig(PageSnippet $document): PageConfig
    {
        return new PageConfig($document);
    }
}
