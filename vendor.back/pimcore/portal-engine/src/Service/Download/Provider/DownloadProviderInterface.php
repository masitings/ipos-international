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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Download\Provider;

use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataPoolConfigInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadConfig;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadType;
use Pimcore\Model\Asset;

interface DownloadProviderInterface
{
    /**
     * @param DataPoolConfigInterface $config
     *
     * @return bool
     */
    public function canProvide(DataPoolConfigInterface $config): bool;

    /**
     * @param DataPoolConfigInterface $config
     * @param bool $checkPermissions
     *
     * @return DownloadType[]
     */
    public function provide(DataPoolConfigInterface $config, bool $checkPermissions = true): array;

    /**
     * @param DataPoolConfigInterface $dataPoolConfig
     * @param DownloadConfig $downloadConfig
     * @param mixed $source
     *
     * @return bool
     */
    public function canExtractSource(DataPoolConfigInterface $dataPoolConfig, DownloadConfig $downloadConfig, $source): bool;

    /**
     * @param DataPoolConfigInterface $dataPoolConfig
     * @param DownloadConfig $downloadConfig
     * @param mixed $source
     *
     * @return Asset|Asset[]
     */
    public function extractSource(DataPoolConfigInterface $dataPoolConfig, DownloadConfig $downloadConfig, $source);
}
