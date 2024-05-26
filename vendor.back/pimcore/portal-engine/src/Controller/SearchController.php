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

use Pimcore\Bundle\PortalEngineBundle\Enum\DependencyInjection\ContainerParameter;
use Pimcore\Bundle\PortalEngineBundle\Model\Rest\Api\SearchGroup;
use Pimcore\Bundle\PortalEngineBundle\Service\Content\HeadTitleService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\SearchHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class SearchController
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Controller
 */
class SearchController extends AbstractSiteController
{
    /**
     * @Route("/{_portal_engine_prefix}search", condition="request.attributes.get('isPortalEngineSite')", name="pimcore_portalengine_search")
     */
    public function searchAction(Request $request, SearchHandler $searchHandler, DataPoolConfigService $dataPoolConfigService, HeadTitleService $headTitleService, TranslatorInterface $translator): Response
    {
        /** @var array $searchTerms */
        $searchTerms = is_array($request->query->get('q')) ? $request->query->get('q') : [];
        /** @var SearchGroup[] $searchGroups */
        $searchGroups = $searchHandler->getFullTextData($searchTerms);
        /** @var int $activeDataPoolId */
        $activeDataPoolId = $request->query->get('activeDataPoolId');
        /** @var string|null $activeDataPoolType */
        $activeDataPoolType = null;

        //reset $activeDataPoolId var if id not exists
        if ($activeDataPoolId && !array_key_exists($activeDataPoolId, $searchGroups)) {
            $activeDataPoolId = null;
        }

        //set first found dataPool as default
        if (!$activeDataPoolId && !empty($searchGroups)) {
            $activeDataPoolId = reset($searchGroups)->getDataPoolConfigId();
        }

        //get activeDataPool type
        if ($activeDataPoolId && array_key_exists($activeDataPoolId, $searchGroups)) {
            $activeDataPoolType = $searchGroups[$activeDataPoolId]->getType();
        }

        if ($activeDataPoolId) {
            $dataPoolConfigService->setCurrentDataPoolConfigById($activeDataPoolId);
        }

        /** @var string $searchTermsAsString */
        $searchTermsAsString = '"' . implode('", "', $searchTerms) . '"';

        $headTitleService->setTitle($translator->trans('portal-engine.content.title.search-result') . ' ' . $searchTermsAsString);

        return $this->renderTemplate(
            '@PimcorePortalEngine/search/search.html.twig',
            [
                'searchTerms' => $searchTerms,
                'searchTermsAsString' => $searchTermsAsString,
                'searchGroups' => array_values($searchGroups),
                'activeDataPoolId' => $activeDataPoolId,
                'activeDataPoolType' => $activeDataPoolType,
                'selectAllMaxSize' => $this->getParameter(ContainerParameter::SELECT_ALL_MAX_SIZE)
            ]
        );
    }
}
