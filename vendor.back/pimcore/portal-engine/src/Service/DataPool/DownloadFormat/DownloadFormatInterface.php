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

namespace Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DownloadFormat;

use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataPoolConfigInterface;

interface DownloadFormatInterface
{
    /**
     * Returns true if the exporter supports the given data pool.
     *
     * @param DataPoolConfigInterface $dataPoolConfig
     *
     * @return bool
     */
    public function supports(DataPoolConfigInterface $dataPoolConfig): bool;

    /**
     * User friendly display name of the export format.
     *
     * @return string
     */
    public function getDisplayName(): string;

    /**
     * Adds the given element to the export file or data of the given download unique id.
     *
     * @param string $downloadUniqueId
     * @param $element
     *
     * @return mixed
     */
    public function add(string $downloadUniqueId, $element);

    /**
     * Produces the actual download file which contains all elements which where added via add().
     * The download file itself will be deleted as soon as the user downloaded it,
     * but remember to cleanup any additional files or data which might be produced by this download format.
     *
     * @param string $downloadUniqueId
     *
     * @return string
     */
    public function bundle(string $downloadUniqueId): string;

    /**
     * Filename
     *
     * @param string $downloadUniqueId
     *
     * @return string
     */
    public function getDownloadFilename(string $downloadUniqueId): string;
}
