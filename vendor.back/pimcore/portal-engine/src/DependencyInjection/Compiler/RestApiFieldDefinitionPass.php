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
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RestApiFieldDefinitionPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $this->buildDefinitionAdapters(
            $container,
            CompilerPassTag::REST_API_FIELD_DEFINITION,
            'pimcore.portal_engine.rest_api_field_definition_locator'
        );

        $this->buildDefinitionAdapters(
            $container,
            CompilerPassTag::REST_API_METADATA_DEFINITION,
            'pimcore.portal_engine.rest_api_metadata_definition_locator'
        );
    }

    protected function buildDefinitionAdapters(ContainerBuilder $container, string $tag, string $definition)
    {
        if (!$container->has($definition)) {
            return;
        }

        $taggedServices = $container->findTaggedServiceIds($tag);

        $arguments = [];
        if (sizeof($taggedServices)) {
            foreach ($taggedServices as $id => $tags) {
                foreach ($tags as $attributes) {
                    $arguments[$attributes['type']] = new Reference($id);
                }
            }
        }

        // load mappings for field definition adapters
        $serviceLocator = $container->getDefinition($definition);
        $serviceLocator->setArgument(0, $arguments);
    }
}
