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

use Pimcore\Bundle\PortalEngineBundle\Service\Content\HeadTitleService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class DownloadCartController
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Controller
 */
class DownloadCartController extends AbstractSiteController
{
    /**
     * @Route("/{_portal_engine_prefix}download-cart", condition="request.attributes.get('isPortalEngineSite')", name="pimcore_portalengine_download_cart_detail")
     */
    public function detailAction(Request $request, HeadTitleService $headTitleService, TranslatorInterface $translator)
    {
        $headTitleService->setTitle($translator->trans('portal-engine.content.title.download-cart'));

        return $this->renderTemplate('@PimcorePortalEngine/download_cart/detail.html.twig');
    }
}
