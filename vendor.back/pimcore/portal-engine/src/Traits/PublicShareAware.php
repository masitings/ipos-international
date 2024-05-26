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

namespace Pimcore\Bundle\PortalEngineBundle\Traits;

use Pimcore\Bundle\PortalEngineBundle\Entity\PublicShare;
use Pimcore\Bundle\PortalEngineBundle\Service\PublicShare\PublicShareService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\SecurityService;
use Symfony\Component\HttpFoundation\Request;

/**
 * Trait PublicShareAware
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Traits
 */
trait PublicShareAware
{
    /** @var PublicShareService */
    protected $publicShareService;
    /** @var SecurityService */
    protected $securityService;

    /** @var PublicShare */
    protected $publicShare;

    /**
     * @param Request $request
     *
     * @return $this
     */
    public function setupPublicShareByRequest(Request $request)
    {
        $this->publicShare = $this->publicShareService->validateByHash($request->query->get('publicShareHash'));

        return $this;
    }

    /**
     * @param PublicShareService $publicShareService
     * @required
     */
    public function setPublicShareService(PublicShareService $publicShareService): void
    {
        $this->publicShareService = $publicShareService;
    }

    /**
     * @param SecurityService $securityService
     * @required
     */
    public function setSecurityService(SecurityService $securityService): void
    {
        $this->securityService = $securityService;
    }
}
