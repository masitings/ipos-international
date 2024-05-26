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

use Pimcore\Bundle\PortalEngineBundle\Enum\DependencyInjection\CompilerPassTag;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DownloadFormatHandler;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class DownloadFormatPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $taggedServices = $container->findTaggedServiceIds(CompilerPassTag::DOWNLOAD_FORMAT);

        $serviceHandler = $container->getDefinition(DownloadFormatHandler::class);

        if (sizeof($taggedServices)) {
            foreach ($taggedServices as $id => $tags) {
                $serviceHandler->addMethodCall('addDownloadFormatService', [$id, new Reference($id)]);
            }
        }
    }
}
