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

namespace Pimcore\Bundle\StatisticsExplorerBundle\Service;

use Pimcore\Bundle\StatisticsExplorerBundle\User\UserProviderInterface;

class UserProviderLocatorService
{
    /**
     * @var UserProviderInterface[]
     */
    protected $userProviderMap;

    /**
     * @param UserProviderInterface[] $userProviderMap
     */
    public function __construct(array $userProviderMap)
    {
        $this->userProviderMap = $userProviderMap;
    }

    /**
     * @param string $context
     *
     * @return UserProviderInterface
     *
     * @throws \Exception
     */
    public function getUserProvider(string $context): UserProviderInterface
    {
        if (!isset($this->userProviderMap[$context])) {
            throw new \Exception("Invalid user context '$context'");
        }

        return $this->userProviderMap[$context];
    }
}
