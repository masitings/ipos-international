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

namespace Pimcore\Bundle\PortalEngineBundle\Event\Asset\Upload;

use Pimcore\Model\Asset;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class BasicAssetCreateEvent
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Event\Asset\Upload
 */
class BasicAssetCreateEvent extends Event
{
    /** @var Asset */
    protected $asset;
    /** @var string[] */
    protected $globalMessages = [];
    /** @var string|null */
    protected $assetListEntryMessage;
    /** @var bool */
    protected $cancelCurrentUpload = false;
    /** @var bool */
    protected $cancelWholeUpload = false;

    /**
     * BasicAssetCreateEvent constructor.
     *
     * @param Asset $asset
     */
    public function __construct(Asset $asset)
    {
        $this->asset = $asset;
    }

    /**
     * @return Asset
     */
    public function getAsset(): Asset
    {
        return $this->asset;
    }

    /**
     * @param string $message
     *
     * @return $this
     */
    public function addGlobalMessage(string $message)
    {
        $this->globalMessages[] = $message;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getGlobalMessages(): array
    {
        return $this->globalMessages;
    }

    /**
     * @param string|null $assetListEntryMessage
     *
     * @return $this
     */
    public function setAssetListEntryMessage(?string $assetListEntryMessage)
    {
        $this->assetListEntryMessage = $assetListEntryMessage;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAssetListEntryMessage(): ?string
    {
        return $this->assetListEntryMessage;
    }

    /**
     * @return bool
     */
    public function isCancelCurrentUpload(): bool
    {
        return $this->cancelCurrentUpload;
    }

    /**
     * @param bool $cancelCurrentUpload
     *
     * @return $this
     */
    public function setCancelCurrentUpload(bool $cancelCurrentUpload)
    {
        $this->cancelCurrentUpload = $cancelCurrentUpload;

        return $this;
    }

    /**
     * @return bool
     */
    public function isCancelWholeUpload(): bool
    {
        return $this->cancelWholeUpload;
    }

    /**
     * @param bool $cancelWholeUpload
     *
     * @return $this
     */
    public function setCancelWholeUpload(bool $cancelWholeUpload)
    {
        $this->cancelWholeUpload = $cancelWholeUpload;

        return $this;
    }
}
