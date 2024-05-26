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

namespace Pimcore\Bundle\EnterpriseSubscriptionToolsBundle\Service;

use Pimcore\Bundle\EnterpriseSubscriptionToolsBundle\Bundle\EnterpriseBundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class EnterpriseSubscriptionStatusService
{
    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * @var string
     */
    protected $secret;

    /**
     * @var string
     */
    protected $token;

    /**
     * EnterpriseSubscriptionStatusService constructor.
     *
     * @param KernelInterface $kernel
     * @param string $secret
     * @param string $token
     */
    public function __construct(KernelInterface $kernel, string $secret, string $token)
    {
        $this->kernel = $kernel;
        $this->secret = $secret;
        $this->token = $token;
    }

    public function buildInstanceCode(): string
    {
        $bundles = $this->getEnterpriseBundles($this->kernel);
        $instanceCode = array_merge([$this->token], $bundles);

        return base64_encode(implode('|', $instanceCode));
    }

    protected function getEnterpriseBundles(KernelInterface $kernel): array
    {
        $bundles = [];
        foreach ($kernel->getBundles() as $bundle) {
            if ($bundle instanceof EnterpriseBundleInterface) {
                $bundles[] = $bundle->getBundleLicenseId();
            }
        }

        return $bundles;
    }

    /**
     * @return string
     */
    public function getInstanceId(): string
    {
        $instanceId = 'not-set';
        try {
            $instanceId = $this->secret;
            $instanceId = sha1(substr($instanceId, 3, -3));
        } catch (\Exception $e) {
            // nothing to do
        }

        return $instanceId;
    }
}
