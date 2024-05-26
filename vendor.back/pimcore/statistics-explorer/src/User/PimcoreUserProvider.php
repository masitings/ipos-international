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

namespace Pimcore\Bundle\StatisticsExplorerBundle\User;

use Pimcore\Bundle\AdminBundle\Security\User\TokenStorageUserResolver;
use Pimcore\Model\User;

class PimcoreUserProvider implements UserProviderInterface
{
    /**
     * @var TokenStorageUserResolver
     */
    protected $tokenStorageResolver;

    public function __construct(TokenStorageUserResolver $tokenStorageResolver)
    {
        $this->tokenStorageResolver = $tokenStorageResolver;
    }

    protected function getUser(): ?User
    {
        return $this->tokenStorageResolver->getUser();
    }

    public function getCurrentUserId(): ?int
    {
        if ($user = $this->getUser()) {
            return $user->getId();
        }

        return null;
    }

    public function getSharedWithCurrentUserCondition(): ?array
    {
        $condition = [];
        if ($user = $this->getUser()) {
            $condition[] = ['user', $user->getId()];
            $roles = $user->getRoles();
            foreach ($roles as $role) {
                $condition[] = ['role', $role->getId()];
            }
        }

        return $condition;
    }

    public function getOtherUsers(): array
    {
        $result = [];

        $userListing = new User\Listing();
        if ($currentUserId = $this->getCurrentUserId()) {
            $userListing->setCondition('id != ?', [$currentUserId]);
        }

        foreach ($userListing->load() as $user) {
            $result['users'][] = [
                'value' => $user->getId(),
                'label' => $user->getName()
            ];
        }

        $roleListing = new User\Role\Listing();
        foreach ($roleListing->load() as $role) {
            $result['roles'][] = [
                'value' => $role->getId(),
                'label' => $role->getName()
            ];
        }

        return $result;
    }
}
