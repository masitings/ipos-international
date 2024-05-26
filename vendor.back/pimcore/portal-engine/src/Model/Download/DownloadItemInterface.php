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

namespace Pimcore\Bundle\PortalEngineBundle\Model\Download;

use Pimcore\Model\Element\ElementInterface;

interface DownloadItemInterface
{
    /**
     * @return int|null
     */
    public function getDataPoolId(): ?int;

    /**
     * @return int|null
     */
    public function getElementId(): ?int;

    /**
     * @return ElementInterface|null
     */
    public function getElement(): ?ElementInterface;

    /**
     * @return DownloadConfig[]
     */
    public function getConfigs(): array;

    /**
     * @return string
     */
    public function getHash(): string;
}
