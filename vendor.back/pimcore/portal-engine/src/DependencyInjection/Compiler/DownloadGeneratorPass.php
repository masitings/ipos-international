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

namespace Pimcore\Bundle\PortalEngineBundle\DependencyInjection\Compiler;

use Pimcore\Bundle\PortalEngineBundle\Service\Download\DownloadService;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DownloadGeneratorPass implements CompilerPassInterface
{
    use TaggedAwareCompilerPass;

    public function process(ContainerBuilder $container)
    {
        $this->applyTaggedServices($container, 'pimcore.portal_engine.download_generator', DownloadService::class, 'addDownloadGenerator');
    }
}
