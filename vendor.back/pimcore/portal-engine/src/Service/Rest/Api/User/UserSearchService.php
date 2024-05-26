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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\User;

use Pimcore\Bundle\PortalEngineBundle\Model\DataObject\PortalUserGroupInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\DataObject\PortalUserInterface;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\SecurityService;
use Pimcore\Model\DataObject\PortalUser;
use Pimcore\Model\DataObject\PortalUser\Listing;
use Pimcore\Model\DataObject\PortalUserGroup;

/**
 * Class UserSearchService
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\User
 */
class UserSearchService
{
    /** @var SecurityService */
    protected $securityService;

    /**
     * UserSearchService constructor.
     *
     * @param SecurityService $securityService
     */
    public function __construct(SecurityService $securityService)
    {
        $this->securityService = $securityService;
    }

    /**
     * @param string $searchTerm
     * @param int|null $excludedId
     *
     * @return PortalUser[]
     */
    public function getPortalUsersBySearchTerm(string $searchTerm, int $excludedId = null)
    {
        if ($excludedId === null) {
            $excludedId = $this->securityService->getPortalUser()->getId();
        }

        $searchTerm = strtolower($searchTerm);

        return (new Listing())
            ->setCondition('oo_id != :id AND (lower(o_key) LIKE :key OR lower(email) LIKE :email OR lower(firstname) LIKE :firstname OR lower(lastname) LIKE :lastname)', [
                'id' => $excludedId,
                'key' => $searchTerm . '%',
                'email' => $searchTerm . '%',
                'firstname' => $searchTerm . '%',
                'lastname' => $searchTerm . '%',
            ])
            ->load();
    }

    /**
     * @param string $searchTerm
     *
     * @return \Pimcore\Model\DataObject\PortalUserGroup[]
     *
     * @throws \Exception
     */
    public function getPortalUserGroupsBySearchTerm(string $searchTerm)
    {
        $searchTerm = strtolower($searchTerm);

        return (new \Pimcore\Model\DataObject\PortalUserGroup\Listing())
            ->setCondition('lower(o_key) LIKE :key', [
                'key' => $searchTerm . '%',
            ])
            ->load();
    }

    /**
     * @param PortalUserInterface|PortalUser $portalUser
     *
     * @return array
     */
    public function hydratePortalUser($portalUser)
    {
        return [
            'id' => $portalUser->getId(),
            'name' => $portalUser->getFirstname() . ' ' . $portalUser->getLastname(),
            'type' => 'user'
        ];
    }

    /**
     * @param PortalUserGroupInterface|PortaluserGroup $portalUserGroup
     *
     * @return array
     */
    public function hydratePortalUserGroup($portalUserGroup)
    {
        return [
            'id' => $portalUserGroup->getId(),
            'name' => $portalUserGroup->getKey(),
            'type' => 'group'
        ];
    }
}
