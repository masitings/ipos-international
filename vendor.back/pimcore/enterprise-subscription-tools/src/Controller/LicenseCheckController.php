<?php

/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Enterprise License (PEL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PEL
 */

namespace Pimcore\Bundle\EnterpriseSubscriptionToolsBundle\Controller;

use Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse;
use Pimcore\Bundle\EnterpriseSubscriptionToolsBundle\Service\EnterpriseSubscriptionStatusService;
use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

class LicenseCheckController extends FrontendController
{
    /**
     * @Route("/admin/enterprise-subscription-tools/instance-info")
     */
    public function instanceInfoAction(Request $request, KernelInterface $kernel, EnterpriseSubscriptionStatusService $service)
    {
        $environment = $kernel->getEnvironment();

        return new JsonResponse([
            'success' => true,
            'instanceId' => $service->getInstanceId(),
            'environment' => $environment,
            'instanceCode' => $service->buildInstanceCode()
        ]);
    }
}
