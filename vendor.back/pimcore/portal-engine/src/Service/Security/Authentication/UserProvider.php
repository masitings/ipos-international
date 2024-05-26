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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Security\Authentication;

use Pimcore\Bundle\PortalEngineBundle\Model\DataObject\PortalUserInterface;
use Pimcore\Bundle\PortalEngineBundle\Service\PublicShare\PublicShareService;
use Pimcore\Model\DataObject\PortalUser;
use Pimcore\Model\Factory;
use Pimcore\Model\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    /**
     * @var Factory
     */
    protected $modelFactory;

    /**
     * @var PublicShareService
     */
    protected $publicShareService;
    /**
     * @var string[]
     */
    protected $userLoginFields;

    /**
     * UserProvider constructor.
     *
     * @param Factory $modelFactory
     * @param PublicShareService $publicShareService
     * @param array $fields
     */
    public function __construct(Factory $modelFactory, PublicShareService $publicShareService, array $fields)
    {
        $this->modelFactory = $modelFactory;
        $this->publicShareService = $publicShareService;
        $this->userLoginFields = $fields;
    }

    /**
     * @param string $username
     *
     * @return UserInterface|null
     */
    public function loadUserByUsername($username)
    {
        foreach ($this->userLoginFields as $field) {
            $list = PortalUser::getList();
            $list->setCondition($field.' = ?', $username);
            $list->setLimit(1);
            if ($list->current()) {
                return $list->current();
            }
        }

        return null;
    }

    public function refreshUser(UserInterface $user)
    {
        return PortalUser::getById($user->getId());
    }

    public function supportsClass($class)
    {
        $userClassName = $this->modelFactory->getClassNameFor(PortalUser::class);

        return in_array($class, [$userClassName, PortalUser::class]);
    }

    /**
     * @param string $userId
     * @param bool $force
     *
     * @return PortalUserInterface|null
     */
    public function getById(string $userId, $force = false): ?PortalUserInterface
    {
        if (!is_numeric($userId) && !empty($userId)) {
            return $this->publicShareService->createPublicShareUserInstance($userId);
        }

        return PortalUser::getById($userId, $force);
    }

    /**
     * @param User $pimcoreUser
     *
     * @return PortalUserInterface|null
     */
    public function getPortalUserForPimcoreUser(User $pimcoreUser): ?PortalUserInterface
    {
        $list = PortalUser::getList();
        $list->setCondition('pimcoreUser = ?', $pimcoreUser->getId());
        $list->setLimit(1);

        return $list->current() ?: null;
    }
}
