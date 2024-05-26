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

namespace Pimcore\Bundle\PortalEngineBundle\Model\Configuration\Content;

use Pimcore\Bundle\PortalEngineBundle\Model\ElementDataAware;
use Pimcore\Model\Document\PageSnippet;

class PageConfig
{
    use ElementDataAware;

    public function __construct(PageSnippet $document)
    {
        $this->document = $document;
    }
}
