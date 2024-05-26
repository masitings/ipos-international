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

use Pimcore\Model\Asset\Folder;

/**
 * Class AssetUploadList
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Model\Rest\Api\Asset\Upload
 */
class AssetUploadList
{
    /** @var string[] */
    protected $messages = [];
    /** @var AssetUploadListEntry[] */
    protected $entries = [];
    /** @var int[] */
    protected $assetFolderIds = [];
    /** @var bool */
    protected $addEntryAllowed = true;

    /**
     * @param string $message
     *
     * @return $this
     */
    public function addMessage(string $message)
    {
        $this->messages[] = $message;

        return $this;
    }

    /**
     * @param AssetUploadListEntry $assetUploadListEntry
     *
     * @return $this
     */
    public function addEntry(AssetUploadListEntry $assetUploadListEntry)
    {
        $this->entries[] = $assetUploadListEntry;

        return $this;
    }

    /**
     * @param Folder $folder
     *
     * @return $this
     */
    public function addAssetFolder(Folder $folder)
    {
        $this->assetFolderIds[] = $folder->getId();

        return $this;
    }

    /**
     * @return string[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @param string[] $messages
     *
     * @return AssetUploadList
     */
    public function setMessages(array $messages): self
    {
        $this->messages = $messages;

        return $this;
    }

    /**
     * @return AssetUploadListEntry[]
     */
    public function getEntries(): array
    {
        return $this->entries;
    }

    /**
     * @param AssetUploadListEntry[] $entries
     *
     * @return AssetUploadList
     */
    public function setEntries(array $entries): self
    {
        $this->entries = $entries;

        return $this;
    }

    /**
     * @return int[]
     */
    public function getAssetFolderIds(): array
    {
        return $this->assetFolderIds;
    }

    /**
     * @param int[] $assetFolderIds
     *
     * @return AssetUploadList
     */
    public function setAssetFolderIds(array $assetFolderIds): self
    {
        $this->assetFolderIds = $assetFolderIds;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAddEntryAllowed(): bool
    {
        return $this->addEntryAllowed;
    }

    /**
     * @param bool $addEntryAllowed
     *
     * @return AssetUploadList
     */
    public function setAddEntryAllowed(bool $addEntryAllowed): self
    {
        $this->addEntryAllowed = $addEntryAllowed;

        return $this;
    }
}
