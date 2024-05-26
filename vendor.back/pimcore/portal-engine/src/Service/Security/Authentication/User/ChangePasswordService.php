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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Security\Authentication\User;

use Pimcore\Model\DataObject\PortalUser;
use Pimcore\Model\User;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class ChangePasswordService
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\Security\Authentication\User
 */
class ChangePasswordService
{
    /**
     * @param PortalUser|UserInterface $portalUser
     * @param string $password
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function changePassword(PortalUser $portalUser, string $password)
    {
        try {
            if ($portalUser->getUsePimcoreUserPassword()) {

                /** @var User $pimcoreUser */
                $pimcoreUser = User::getById($portalUser->getPimcoreUser());
                if ($pimcoreUser) {
                    $pimcoreUser
                        ->setPassword($password)
                        ->save();
                }
            }

            $portalUser
                ->setPortalPassword($password)
                ->save();
        } catch (\Exception $e) {
            throw new \Exception('change password failed');
        }

        return $this;
    }
}
