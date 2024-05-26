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

use Pimcore\Model\Element\ElementInterface;

interface IndexServiceInterface
{
    /**
     * @param string $indexName
     *
     * @return array
     */
    public function refreshIndex(string $indexName): array;

    public function getCurrentIndexFullPath(ElementInterface $element, string $indexName): ?string;

    public function rewriteChildrenIndexPaths(ElementInterface $element, string $indexName, string $oldFullPath);
}
