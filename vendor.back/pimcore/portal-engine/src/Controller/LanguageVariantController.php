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

namespace Pimcore\Bundle\PortalEngineBundle\Controller;

use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolService;
use Symfony\Component\HttpFoundation\Request;

class LanguageVariantController extends AbstractSiteController
{
    public function dataPoolLanguageVariantAction(Request $request, DataPoolConfigService $dataPoolConfigService, DataPoolService $dataPoolService)
    {
        if ($this->editmode) {
            return $this->renderTemplate('@PimcorePortalEngine/language_variant/data_pool_language_variant/editmode.html.twig');
        }

        if (!$dataPoolConfigService->getCurrentDataPoolConfig()) {
            return $this->renderTemplate('@PimcorePortalEngine/language_variant/data_pool_language_variant/invalid_config.html.twig');
        }

        $controllerClass = $dataPoolService->getCurrentDataPool()->getFrontendControllerClass();

        return $this->forward($controllerClass . '::listAction', [], $request->query->all());
    }
}
