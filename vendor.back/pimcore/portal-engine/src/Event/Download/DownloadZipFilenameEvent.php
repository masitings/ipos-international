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

namespace Pimcore\Bundle\PortalEngineBundle\Event\Download;

use Pimcore\Bundle\PortalEngineBundle\Entity\BatchTask;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Fires before the final download zip will be sent as download to the user.
 * Can be used to define a custom file name of the zip file.
 */
class DownloadZipFilenameEvent extends Event
{
    /**
     * @var string
     */
    private $filename;

    /**
     * @var BatchTask|null
     */
    private $batchTask;

    /**
     * DownloadZipFilenameEvent constructor.
     *
     * @param string $filename
     * @param BatchTask|null $batchTask
     */
    public function __construct(string $filename, BatchTask $batchTask = null)
    {
        $this->filename = $filename;
        $this->batchTask = $batchTask;
    }

    /**
     * @return BatchTask|null
     */
    public function getBatchTask(): ?BatchTask
    {
        return $this->batchTask;
    }

    public function isSingleDownload(): bool
    {
        return empty($this->batchTask);
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     *
     * @return DownloadZipFilenameEvent
     */
    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }
}
