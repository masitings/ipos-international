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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Security;

use Pimcore\Bundle\PortalEngineBundle\Model\DataObject\PortalUserInterface;
use Pimcore\Model\DataObject\PortalUser;
use Pimcore\Model\User;
use Pimcore\Tool;
use Pimcore\Tool\Session;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SecurityService
{
    protected $tokenStorage;

    /**
     * @var PortalUserInterface|null
     */
    protected $portalUser;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    public function __construct(TokenStorageInterface $tokenStorage, RequestStack $requestStack)
    {
        $this->tokenStorage = $tokenStorage;
        $this->requestStack = $requestStack;
    }

    /**
     * @return PortalUserInterface|null
     */
    public function getPortalUser(): ?PortalUserInterface
    {
        try {
            if ($this->portalUser === null) {
                $this->portalUser = $this->tokenStorage->getToken() ? $this->tokenStorage->getToken()->getUser() : null;

                if (!$this->portalUser instanceof PortalUserInterface) {
                    $this->portalUser = null;
                }
            }
        } catch (\Exception $e) {
            $this->portalUser = null;
        }

        if (empty($this->portalUser) && ($this->isAdminRestApiCall() || $this->isAdminPreviewCall())) {
            return (new PortalUser())
                ->setAdmin(true);
        }

        return $this->portalUser;
    }

    /**
     * @return User|null
     */
    public function getPimcoreUser()
    {
        /** @var User|mixed $pimcoreUser */
        $pimcoreUser = Session::get()->get('user');

        return $pimcoreUser instanceof User
            ? $pimcoreUser
            : null;
    }

    /**
     * Can be used to force a portal user (i.e. in cli context)
     *
     * @param PortalUserInterface|null $portalUser
     */
    public function setPortalUser(?PortalUserInterface $portalUser): void
    {
        $this->portalUser = $portalUser;
    }

    /**
     * @return int
     */
    public function getPimcoreUserId(): int
    {
        if (!$portalUser = $this->getPortalUser()) {
            return 0;
        }

        if (!$user = User::getById($portalUser->getPimcoreUser())) {
            return 0;
        }

        return $user->getId();
    }

    public function isAdminRestApiCall()
    {
        $pathInfo = $this->requestStack->getMasterRequest() ? $this->requestStack->getMasterRequest()->getPathInfo() : null;
        if (empty($pathInfo)) {
            return false;
        }
        if (strpos($pathInfo, '/_portal-engine/api/') === 0 || strpos($pathInfo, '/_portal-engine/stats/') === 0) {
            return $this->getPimcoreUser() instanceof User;
        }

        return false;
    }

    public function isAdminPreviewCall()
    {
        if (Tool::isFrontendRequestByAdmin()) {
            return $this->getPimcoreUser() instanceof User;
        }

        return false;
    }
}
