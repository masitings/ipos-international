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

use Pimcore\Bundle\PortalEngineBundle\Service\Document\LanguageVariantService;
use Pimcore\Bundle\PortalEngineBundle\Service\PortalConfig\DefaultValuesService;
use Pimcore\Bundle\PortalEngineBundle\Service\PortalConfig\FrontendBuildService;
use Pimcore\Bundle\PortalEngineBundle\Service\PortalConfig\PortalConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\SecurityService;
use Pimcore\Http\Request\Resolver\DocumentResolver;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class PortalController extends AbstractSiteController
{
    public function pageAction(
        Request $request,
        DocumentResolver $documentResolver,
        DefaultValuesService $defaultConfigService,
        FrontendBuildService $frontendBuildService,
        PortalConfigService $portalConfigService,
        LanguageVariantService $languageVariantService,
        SecurityService $securityService
    ) {
        $defaultConfigService->setPortalPageDefaultConfig($documentResolver->getDocument($request));

        if (!$this->editmode && !$request->get('pimcore_preview') && $portalConfigService->getCurrentPortalConfig()->getEnableLanguageRedirect()) {
            $mapping = $languageVariantService->getPortalLanguageVariantsMapping();
            $languages = array_keys($mapping);
            $preferredLanguage = in_array($securityService->getPortalUser()->getPreferredLanguage(), $languages)
                ? $securityService->getPortalUser()->getPreferredLanguage()
                : $request->getPreferredLanguage($languages);

            return new RedirectResponse($mapping[$preferredLanguage]);
        }

        $customizedFrontendBuilds = [[null, '-']];
        foreach ($frontendBuildService->getCustomizedFrontendBuilds() as $build) {
            $customizedFrontendBuilds[] = [$build, $build];
        }

        return $this->renderTemplate('@PimcorePortalEngine/portal/page.html.twig', [
            'customizedFrontendBuilds' => $customizedFrontendBuilds
        ]);
    }

    public function contentAction()
    {
        return $this->renderTemplate('@PimcorePortalEngine/portal/content.html.twig');
    }
}
