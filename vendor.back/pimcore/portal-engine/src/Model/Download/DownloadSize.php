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

class DownloadSize
{
    const FACTOR = 1000;

    private $kb;

    public function __construct(float $kb)
    {
        $this->kb = $kb;
    }

    /**
     * @return float
     */
    public function toB(): float
    {
        return $this->kb * 1000;
    }

    /**
     * @return float
     */
    public function toKb(): float
    {
        return $this->kb;
    }

    /**
     * @return float
     */
    public function toMb(): float
    {
        return $this->toKb() / self::FACTOR;
    }

    /**
     * @return float
     */
    public function toGb(): float
    {
        return $this->toMb() / self::FACTOR;
    }

    /**
     * @return float
     */
    public function toTb(): float
    {
        return $this->toGb() / self::FACTOR;
    }

    /**
     * @param DownloadSize $downloadSize
     *
     * @return $this
     */
    public function add(self $downloadSize): self
    {
        return new self($this->kb + $downloadSize->toKb());
    }

    /**
     * @param float $mul
     *
     * @return $this
     */
    public function mul(float $mul): self
    {
        return new self($this->kb * $mul);
    }

    /**
     * @return static
     */
    public static function zero(): self
    {
        return new static(0);
    }
}
