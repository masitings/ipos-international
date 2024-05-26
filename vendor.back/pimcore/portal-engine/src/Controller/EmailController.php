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

use Pimcore\Bundle\PortalEngineBundle\Service\PortalConfig\PortalConfigService;
use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class EmailController
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Controller
 */
class EmailController extends FrontendController
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function passwordForgottenEmailAction(Request $request, PortalConfigService $portalConfigService)
    {
        return $this->renderTemplate('@PimcorePortalEngine/email/password-forgotten-email.html.twig', [
            'userName' => $this->editmode ? ' Dummy User' : $request->get('userName'),
            'passwordRecoverUrl' => $this->editmode ? 'https://pimcore.com' : $request->get('passwordRecoverUrl'),
            'portalName' => $portalConfigService->getPortalName()
        ]);
    }
}
