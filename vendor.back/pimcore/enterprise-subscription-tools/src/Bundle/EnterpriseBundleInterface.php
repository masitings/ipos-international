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

namespace Pimcore\Bundle\EnterpriseSubscriptionToolsBundle\Bundle;

interface EnterpriseBundleInterface
{
    /**
     * @return string
     */
    public function getBundleLicenseId(): string;
}
