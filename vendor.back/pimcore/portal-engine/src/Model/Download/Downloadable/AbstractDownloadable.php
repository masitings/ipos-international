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
use Pimcore\Bundle\PortalEngineBundle\Service\Download\Generator\DownloadGeneratorInterface;

abstract class AbstractDownloadable implements DownloadableInterface
{
    /**
     * @var DataPoolConfigInterface
     */
    protected $dataPoolConfig;

    /**
     * @var DownloadConfig
     */
    protected $downloadConfig;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var string|null
     */
    protected $downloadUniqid;

    /**
     * @var DownloadGeneratorInterface
     */
    protected $generator;

    /**
     * @var GenerationResult
     */
    protected $generationResult;

    /**
     * @var bool
     */
    protected $deleteAfter = false;

    /**
     * @return DataPoolConfigInterface
     */
    public function getDataPoolConfig(): DataPoolConfigInterface
    {
        return $this->dataPoolConfig;
    }

    /**
     * @param DataPoolConfigInterface $dataPoolConfig
     *
     * @return $this
     */
    public function setDataPoolConfig(DataPoolConfigInterface $dataPoolConfig)
    {
        $this->dataPoolConfig = $dataPoolConfig;

        return $this;
    }

    /**
     * @return DownloadConfig
     */
    public function getDownloadConfig(): DownloadConfig
    {
        return $this->downloadConfig;
    }

    /**
     * @param DownloadConfig $downloadConfig
     *
     * @return $this
     */
    public function setDownloadConfig(DownloadConfig $downloadConfig)
    {
        $this->downloadConfig = $downloadConfig;

        return $this;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     *
     * @return $this
     */
    public function setLabel(string $label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDownloadUniqid(): ?string
    {
        return $this->downloadUniqid;
    }

    /**
     * @param string|null $downloadUniqid
     *
     * @return AbstractDownloadable
     */
    public function setDownloadUniqid(?string $downloadUniqid): DownloadableInterface
    {
        $this->downloadUniqid = $downloadUniqid;

        return $this;
    }

    /**
     * @param bool $deleteAfter
     *
     * @return $this
     */
    public function setDeleteAfter(bool $deleteAfter)
    {
        $this->deleteAfter = $deleteAfter;

        return $this;
    }

    /**
     * @return bool
     */
    public function shouldDeleteAfter()
    {
        return $this->deleteAfter;
    }

    /**
     * @param DownloadGeneratorInterface $generator
     *
     * @return $this
     */
    public function setGenerator(DownloadGeneratorInterface $generator)
    {
        $this->generator = $generator;

        return $this;
    }

    /**
     * @return DownloadableInterface
     */
    public function generate(): DownloadableInterface
    {
        if (!$this->generationResult) {
            $this->generationResult = $this->generator->generate($this);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getDownloadFileName(): string
    {
        if (!$this->generationResult) {
            $this->generator->generate($this);
        }

        return $this->generationResult->getFileName();
    }

    /**
     * @return string
     */
    public function getDownloadFilePath(): string
    {
        if (!$this->generationResult) {
            $this->generator->generate($this);
        }

        return $this->generationResult->getFilePath();
    }
}
