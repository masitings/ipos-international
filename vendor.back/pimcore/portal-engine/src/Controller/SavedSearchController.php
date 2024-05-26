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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class SavedSearchController
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Controller
 */
class SavedSearchController extends AbstractSiteController
{
    /**
     * @Route("/{_portal_engine_prefix}saved-search/list", condition="request.attributes.get('isPortalEngineSite')", name="pimcore_portalengine_saved_search_list")
     */
    public function listAction(Request $request, HeadTitleService $headTitleService, TranslatorInterface $translator): Response
    {
        $headTitleService->setTitle($translator->trans('portal-engine.content.title.saved-search-list'));

        return $this->renderTemplate(
            '@PimcorePortalEngine/saved_search/list.html.twig'
        );
    }
}
