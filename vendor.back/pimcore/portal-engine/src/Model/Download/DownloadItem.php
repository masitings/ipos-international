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
use Pimcore\Model\Element\Service;

class DownloadItem implements DownloadItemInterface
{
    use DownloadItemTrait;

    /**
     * @var int|null
     */
    private $dataPoolId;

    /**
     * @var int|null
     */
    private $elementId;

    /**
     * @var string|null
     */
    private $elementType;

    /**
     * @var DownloadConfig[]
     */
    private $configs = [];

    /**
     * @return int|null
     */
    public function getDataPoolId(): ?int
    {
        return $this->dataPoolId;
    }

    /**
     * @param int|null $dataPoolId
     *
     * @return $this
     */
    public function setDataPoolId(?int $dataPoolId)
    {
        $this->dataPoolId = $dataPoolId;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getElementId(): ?int
    {
        return $this->elementId;
    }

    /**
     * @param int|null $elementId
     *
     * @return $this
     */
    public function setElementId(?int $elementId)
    {
        $this->elementId = $elementId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getElementType(): ?string
    {
        return $this->elementType;
    }

    /**
     * @param string|null $elementType
     *
     * @return $this
     */
    public function setElementType(?string $elementType)
    {
        $this->elementType = $elementType;

        return $this;
    }

    /**
     * @return ElementInterface|null
     */
    public function getElement(): ?ElementInterface
    {
        return Service::getElementById($this->elementType, $this->elementId);
    }

    /**
     * @return DownloadConfig[]
     */
    public function getConfigs(): array
    {
        return $this->configs;
    }

    /**
     * @param DownloadConfig[] $configs
     *
     * @return $this
     */
    public function setConfigs(array $configs)
    {
        $this->configs = $configs;

        return $this;
    }
}
