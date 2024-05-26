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

class BundleStructuredData
{
    /**
     * @var string
     */
    private $downloadUniquid;

    /**
     * @var string
     */
    private $downloadFormat;

    /**
     * @var string|null
     */
    private $targetSubfolder;

    /**
     * BundleStructuredData constructor.
     *
     * @param string $downloadUniquid
     * @param string $downloadFormat
     * @param string $targetSubfolder
     */
    public function __construct(string $downloadUniquid, string $downloadFormat, ?string $targetSubfolder)
    {
        $this->downloadUniquid = $downloadUniquid;
        $this->downloadFormat = $downloadFormat;
        $this->targetSubfolder = $targetSubfolder;
    }

    /**
     * @return string
     */
    public function getDownloadUniquid(): string
    {
        return $this->downloadUniquid;
    }

    /**
     * @return string
     */
    public function getDownloadFormat(): string
    {
        return $this->downloadFormat;
    }

    /**
     * @return string|null
     */
    public function getTargetSubfolder(): ?string
    {
        return $this->targetSubfolder;
    }
}
