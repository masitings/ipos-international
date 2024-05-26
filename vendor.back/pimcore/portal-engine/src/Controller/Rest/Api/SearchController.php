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

namespace Pimcore\Bundle\PortalEngineBundle\Controller\Rest\Api;

use Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse;
use Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\ErrorHandler;
use Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\SearchHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/search", condition="request.attributes.get('isPortalEngineSite')")
 */
class SearchController extends AbstractRestApiController
{
    /**
     * @Route("/smart-suggest",
     *     name="pimcore_portalengine_rest_api_search_smart_suggest"
     * )
     */
    public function smartSuggestAction(Request $request, SearchHandler $searchHandler, ErrorHandler $errorHandler): JsonResponse
    {
        /** @var array $searchTerms */
        $searchTerms = is_array($request->query->get('q')) ? $request->query->get('q') : [];
        /** @var array $data */
        $data = $searchHandler->getSmartSuggestData($searchTerms);

        return new JsonResponse(
            [
                'success' => !$errorHandler->hasError(),
                'error' => $errorHandler->getErrorMessage(),
                'data' => $data
            ]
        );
    }
}
