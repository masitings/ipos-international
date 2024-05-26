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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Download\Generator;

use Pimcore\Bundle\PortalEngineBundle\Model\Download\Downloadable\DownloadableInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\Downloadable\GenerationResult;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadConfig;

interface DownloadGeneratorInterface
{
    /**
     * @param mixed $source
     * @param DownloadConfig $config
     *
     * @return bool
     */
    public function supports($source, DownloadConfig $config): bool;

    /**
     * @param mixed $source
     * @param DownloadConfig $config
     *
     * @return DownloadableInterface
     */
    public function createDownloadable($source, DownloadConfig $config): DownloadableInterface;

    /**
     * Generate download and return generation result with file path and file name
     *
     * @return GenerationResult|null
     */
    public function generate(DownloadableInterface $downloadable): ?GenerationResult;
}
