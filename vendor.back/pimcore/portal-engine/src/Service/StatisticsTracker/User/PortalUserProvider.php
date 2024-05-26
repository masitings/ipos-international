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

namespace Pimcore\Bundle\PortalEngineBundle\Service\StatisticsTracker\User;

use Pimcore\Bundle\PortalEngineBundle\Model\DataObject\PortalUserInterface;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\SecurityService;
use Pimcore\Bundle\StatisticsExplorerBundle\User\UserProviderInterface;
use Pimcore\Model\DataObject\PortalUser;

/**
 * Class PortalUserProvider
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\StatisticsTracker\User
 */
class PortalUserProvider implements UserProviderInterface
{
    /** @var SecurityService */
    protected $securityService;

    /**
     * PortalUserProvider constructor.
     *
     * @param SecurityService $securityService
     */
    public function __construct(SecurityService $securityService)
    {
        $this->securityService = $securityService;
    }

    /**
     * @return PortalUserInterface|PortalUser|null
     */
    protected function getUser(): ?PortalUser
    {
        return $this->securityService->getPortalUser();
    }

    /**
     * @return int|null
     */
    public function getCurrentUserId(): ?int
    {
        if ($user = $this->getUser()) {
            return $user->getId();
        }

        return null;
    }

    /**
     * @return array|null
     */
    public function getSharedWithCurrentUserCondition(): ?array
    {
        $condition = [];
        if ($user = $this->getUser()) {
            $condition[] = ['user', $user->getId()];
            foreach ($user->getGroups() as $group) {
                $condition[] = ['role', $group->getId()];
            }
        }

        return $condition;
    }

    /**
     * @return array
     */
    public function getOtherUsers(): array
    {
        $result = [];

        $userListing = new \Pimcore\Model\DataObject\PortalUser\Listing();
        if ($currentUserId = $this->getCurrentUserId()) {
            $userListing->setCondition('o_id != ?', [$currentUserId]);
        }

        foreach ($userListing->load() as $user) {
            $result['users'][] = [
                'value' => $user->getId(),
                'label' => trim($user->getFirstname() . ' ' . $user->getLastname())
            ];
        }

        $groupListing = new \Pimcore\Model\DataObject\PortalUserGroup\Listing();
        foreach ($groupListing->load() as $group) {
            $result['roles'][] = [
                'value' => $group->getId(),
                'label' => $group->getKey()
            ];
        }

        return $result;
    }
}
