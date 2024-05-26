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

namespace Pimcore\Bundle\PortalEngineBundle\Model\Download\Downloadable;

use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataPoolConfigInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadConfig;

interface DownloadableInterface
{
    /**
     * @return DataPoolConfigInterface
     */
    public function getDataPoolConfig(): DataPoolConfigInterface;

    /**
     * @param DataPoolConfigInterface $dataPoolConfig
     *
     * @return $this
     */
    public function setDataPoolConfig(DataPoolConfigInterface $dataPoolConfig);

    /**
     * @return DownloadConfig
     */
    public function getDownloadConfig(): DownloadConfig;

    /**
     * @param DownloadConfig $downloadConfig
     *
     * @return $this
     */
    public function setDownloadConfig(DownloadConfig $downloadConfig);

    /**
     * @return string
     */
    public function getLabel(): string;

    /**
     * @param string $label
     *
     * @return $this
     */
    public function setLabel(string $label);

    /**
     * @return string|null
     */
    public function getDownloadUniqid(): ?string;

    /**
     * @param string|null $downloadUniqid
     *
     * @return $this
     */
    public function setDownloadUniqid(?string $downloadUniqid): self;

    /**
     * @param bool $deleteAfter
     *
     * @return $this
     */
    public function setDeleteAfter(bool $deleteAfter);

    /**
     * @return bool
     */
    public function shouldDeleteAfter();

    /**
     * @return DownloadableInterface
     */
    public function generate(): self;

    /**
     * @return string
     */
    public function getDownloadFileName(): string;

    /**
     * @return string
     */
    public function getDownloadFilePath(): string;
}
