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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Security\Traits;

use Pimcore\Bundle\PortalEngineBundle\Service\Security\SecurityService;

trait SecurityServiceAware
{
    /**
     * @var SecurityService
     */
    protected $securityService;

    /**
     * @param SecurityService $securityService
     * @required
     */
    public function setSecurityService(SecurityService $securityService): void
    {
        $this->securityService = $securityService;
    }
}
