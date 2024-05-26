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

namespace Pimcore\Bundle\PortalEngineBundle\Service\DataObject;

use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataPoolConfigInterface;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\ElementDataPoolConfigResolver;
use Pimcore\Model\DataObject\AbstractObject;
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
     * @param AbstractObject $dataObject
     * @param DataPoolConfigInterface $dataPoolConfig
     *
     * @return string
     */
    public function extractUrl(AbstractObject $dataObject, ?DataPoolConfigInterface $dataPoolConfig = null)
    {
        $dataPoolConfig = $dataPoolConfig ?: $this->elementDataPoolConfigResolver->getDataPoolConfigForElement($dataObject);

        if (!$dataPoolConfig) {
            return null;
        }

        return $this->router->generate('pimcore_portalengine_data_object_detail', [
            'documentPath' => trim((string)$dataPoolConfig->getLanguageVariantOrDocument(), '/'),
            'id' => $dataObject->getId()
        ]);
    }
}
