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

namespace Pimcore\Bundle\EnterpriseSubscriptionToolsBundle\Maintenance;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use Pimcore\Bundle\EnterpriseSubscriptionToolsBundle\Service\EnterpriseSubscriptionStatusService;
use Pimcore\Config;
use Pimcore\Maintenance\TaskInterface;
use Pimcore\Model\Tool\TmpStore;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class CheckSubscriptionStatusTask implements TaskInterface
{
    /**
     * @var EnterpriseSubscriptionStatusService
     */
    protected $subscriptionStatusService;

    /**
     * @var ClientInterface
     */
    protected $httpClient;

    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * CheckSubscriptionStatusTask constructor.
     *
     * @param EnterpriseSubscriptionStatusService $subscriptionStatusService
     * @param ClientInterface $httpClient
     * @param KernelInterface $kernel
     * @param Config $config
     * @param LoggerInterface $logger
     */
    public function __construct(EnterpriseSubscriptionStatusService $subscriptionStatusService, ClientInterface $httpClient, KernelInterface $kernel, Config $config, LoggerInterface $logger)
    {
        $this->subscriptionStatusService = $subscriptionStatusService;
        $this->httpClient = $httpClient;
        $this->kernel = $kernel;
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * Execute the Task
     */
    public function execute()
    {
        $lockId = 'EnterpriseSubscription::CheckSubscriptionStatusTask';

        $locked = TmpStore::get($lockId);

        if (!$locked && date('H') <= 4) {
            // execution should be only sometime between 0:00 and 4:59 -> less load expected
            $this->logger->debug('Checking Pimcore Subscription Status');

            $request = new Request('POST', 'https://license.pimcore.com/pimcore-license/check');
            $response = $this->httpClient->send($request, [
                'form_params' => [
                    'instanceId' => $this->subscriptionStatusService->getInstanceId(),
                    'instanceCode' => $this->subscriptionStatusService->buildInstanceCode(),
                    'environment' => $this->kernel->getEnvironment(),
                    'main_domain' => $this->config['general']['domain']
                ]
            ]);
            TmpStore::set($lockId, true, null, 86400);
        } else {
            $this->logger->debug('Skip Checking Pimcore Subscription Status, was done within the last 24 hours');
        }
    }
}
