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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Asset;

use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataPoolConfigInterface;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\ElementDataPoolConfigResolver;
use Pimcore\Model\Asset;
use Symfony\Component\Routing\RouterInterface;

class UrlExtractorService
{
    protected $router;
    protected $elementDataPoolConfigResolver;

    public function __construct(RouterInterface $router, ElementDataPoolConfigResolver $elementDataPoolConfigResolver)
    {
        $this->router = $router;
        $this->elementDataPoolConfigResolver = $elementDataPoolConfigResolver;
    }

    /**
     * @param Asset $asset
     * @param DataPoolConfigInterface|null $dataPoolConfig
     * @param array $params
     *
     * @return string
     */
    public function extractUrl(Asset $asset, ?DataPoolConfigInterface $dataPoolConfig = null, array $params = [])
    {
        $dataPoolConfig = $dataPoolConfig ?: $this->elementDataPoolConfigResolver->getDataPoolConfigForElement($asset);

        if (!$dataPoolConfig) {
            return null;
        }

        return $this->router->generate('pimcore_portalengine_asset_detail', array_merge([
            'documentPath' => trim((string)$dataPoolConfig->getLanguageVariantOrDocument(), '/'),
            'id' => $asset->getId()
        ], $params));
    }
}
