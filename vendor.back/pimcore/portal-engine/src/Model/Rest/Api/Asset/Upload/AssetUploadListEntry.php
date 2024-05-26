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

namespace Pimcore\Bundle\PortalEngineBundle\Model\Rest\Api\Asset\Upload;

/**
 * Class AssetUploadListEntry
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Model\Rest\Api\Asset\Upload
 */
class AssetUploadListEntry
{
    /** @var string|null */
    protected $name;
    /** @var string|null */
    protected $message;
    /** @var int|null */
    protected $assetId;
    /** @var string|null */
    protected $fullPath;
    /** @var string|null */
    protected $detailLink;

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     *
     * @return AssetUploadListEntry
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @param string|null $message
     *
     * @return AssetUploadListEntry
     */
    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getAssetId(): ?int
    {
        return $this->assetId;
    }

    /**
     * @param int|null $assetId
     *
     * @return AssetUploadListEntry
     */
    public function setAssetId(?int $assetId): self
    {
        $this->assetId = $assetId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFullPath(): ?string
    {
        return $this->fullPath;
    }

    /**
     * @param string|null $fullPath
     *
     * @return AssetUploadListEntry
     */
    public function setFullPath(?string $fullPath): self
    {
        $this->fullPath = $fullPath;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDetailLink(): ?string
    {
        return $this->detailLink;
    }

    /**
     * @param string|null $detailLink
     *
     * @return AssetUploadListEntry
     */
    public function setDetailLink(?string $detailLink): self
    {
        $this->detailLink = $detailLink;

        return $this;
    }
}
