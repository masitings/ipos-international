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

use Pimcore\Bundle\PortalEngineBundle\Service\Download\DownloadProviderService;
use Pimcore\Bundle\PortalEngineBundle\Service\Download\Provider\DataObjectFieldsProvider;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DownloadProviderPass implements CompilerPassInterface
{
    use TaggedAwareCompilerPass;

    public function process(ContainerBuilder $container)
    {
        $this->applyTaggedServices($container, 'pimcore.portal_engine.download_provider', DownloadProviderService::class, 'addDownloadProvider');
        $this->applyTaggedServices($container, 'pimcore.portal_engine.data_object_download_provider.field_extractor', DataObjectFieldsProvider::class, 'addFieldExtractor');
    }
}
