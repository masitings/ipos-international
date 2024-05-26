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

namespace Pimcore\Bundle\PortalEngineBundle\Controller\Admin;

use Pimcore\Bundle\AdminBundle\Controller\AdminController;
use Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse;
use Pimcore\Bundle\PortalEngineBundle\EventSubscriber\DocumentConfigSubscriber;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\PortalConfig;
use Pimcore\Bundle\PortalEngineBundle\Service\PortalConfig\FrontendBuildService;
use Pimcore\Bundle\PortalEngineBundle\Service\PortalConfig\PortalConfigService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/admin/frontend-build")
 */
class FrontendBuildController extends AdminController
{
    /**
     * @Route("/update-frontend-build",
     *     name="pimcore_portalengine_admin_update_frontend_build"
     * )
     */
    public function updateFrontendBuildAction(
        Request $request,
        PortalConfigService $portalConfigService,
        FrontendBuildService $frontendBuildService,
        DocumentConfigSubscriber $documentConfigSubscriber,
        TranslatorInterface $translator
    ) {
        $portalConfig = $this->getPortalConfigByRequest($portalConfigService, $request);

        if ($portalConfig->getCustomizedFrontendBuild()) {
            $errors = [$translator->trans('portal-engine.update-customized-build-not-possible', [], 'admin')];
        } else {
            $errors = $frontendBuildService->validatePortalConfigCssVariables($portalConfig);
        }

        if (empty($errors)) {
            $frontendBuildService->publishCustomizedBuild($portalConfig);
        }

        $documentConfigSubscriber->setUpdatePortalsJson(true);

        return new JsonResponse(
            [
                'success' => true,
                'errors' => $errors,
                'errorsHtml' => $this->renderView('@PimcorePortalEngine/admin/frontend_build/validation_errors.html.twig', ['errors' => $errors])
            ]
        );
    }

    /**
     * @Route("/is-portal",
     *     name="pimcore_portalengine_admin_is_portal"
     * )
     *
     * @param Request $request
     * @param PortalConfigService $portalConfigService
     *
     * @return JsonResponse
     */
    public function isPortalAction(Request $request, PortalConfigService $portalConfigService)
    {
        $portalConfig = $this->getPortalConfigByRequest($portalConfigService, $request);
        $isPortal = !empty($portalConfig);

        $responseData = [
            'success' => true,
            'isPortal' => $isPortal
        ];

        return new JsonResponse(
            $responseData
        );
    }

    protected function getPortalConfigByRequest(PortalConfigService $portalConfigService, Request $request): ?PortalConfig
    {
        if (!$portalId = $request->query->get('portalId')) {
            throw new BadRequestHttpException('portalId param required');
        }
        $portalConfig = $portalConfigService->getPortalConfigById($portalId);

        return $portalConfig;
    }
}
