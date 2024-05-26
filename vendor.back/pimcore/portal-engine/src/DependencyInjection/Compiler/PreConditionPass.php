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
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Search\PreConditionService;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class PreConditionPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $taggedServices = $container->findTaggedServiceIds(CompilerPassTag::PRE_CONDITION_SERVICE_HANDLER);

        $preConditionService = $container->getDefinition(PreConditionService::class);

        if (sizeof($taggedServices)) {
            foreach ($taggedServices as $id => $tags) {
                $preConditionService->addMethodCall('addPreconditionServiceHandler', [$id, new Reference($id)]);
            }
        }
    }
}
