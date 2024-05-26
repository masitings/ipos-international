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

use Pimcore\Bundle\PortalEngineBundle\Enum\Permission;
use Pimcore\Bundle\PortalEngineBundle\Service\Content\HeadTitleService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class StatisticExplorerController
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Controller
 */
class StatisticExplorerController extends AbstractSiteController
{
    /**
     * @Route("/{_portal_engine_prefix}statistic-explorer", condition="request.attributes.get('isPortalEngineSite')", name="pimcore_portalengine_statistic_explorer_overview")
     */
    public function overviewAction(Request $request, HeadTitleService $headTitleService, TranslatorInterface $translator)
    {
        $this->denyAccessUnlessGranted(Permission::STATISTIC_EXPLORER_ACCESS);
        $headTitleService->setTitle($translator->trans('portal-engine.content.title.statistic-explorer-overview'));

        return $this->renderTemplate(
            '@PimcorePortalEngine/statistic_explorer/overview.html.twig'
        );
    }
}
