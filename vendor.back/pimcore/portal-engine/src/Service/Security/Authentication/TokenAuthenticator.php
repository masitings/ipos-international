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

use Pimcore\Bundle\PortalEngineBundle\Service\Document\PrefixService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\Authentication\User\RecoverPasswordService;
use Pimcore\Model\DataObject\PortalUser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class TokenAuthenticator
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\Security\Authentication
 */
class TokenAuthenticator extends AbstractAuthenticator
{
    /**
     * @var RecoverPasswordService
     */
    protected $recoverPasswordService;

    /**
     * @var PrefixService
     */
    protected $prefixService;

    /**
     * @param RecoverPasswordService $recoverPasswordService
     * @required
     */
    public function setRecoverPasswordService(RecoverPasswordService $recoverPasswordService)
    {
        $this->recoverPasswordService = $recoverPasswordService;
    }

    /**
     * @param PrefixService $prefixService
     * @required
     */
    public function setPrefixService(PrefixService $prefixService): void
    {
        $this->prefixService = $prefixService;
    }

    /**
     * Does the authenticator support the given Request?
     *
     * If this returns false, the authenticator will be skipped.
     *
     * @return bool
     */
    public function supports(Request $request)
    {
        if (!$this->portalConfigService->isPortalEngineSite()) {
            return false;
        }
        if ('pimcore_portalengine_auth_login' !== $request->attributes->get('_route')) {
            return false;
        }

        return !empty($request->query->get('token'));
    }

    /**
     * Get the authentication credentials from the request and return them
     * as any type (e.g. an associate array).
     *
     * Whatever value you return here will be passed to getUser() and checkCredentials()
     *
     * For example, for a form login, you might:
     *
     *      return [
     *          'username' => $request->request->get('_username'),
     *          'password' => $request->request->get('_password'),
     *      ];
     *
     * Or for an API token that's on a header, you might use:
     *
     *      return ['api_key' => $request->headers->get('X-API-TOKEN')];
     *
     * @return mixed Any non-null value
     *
     * @throws \UnexpectedValueException If null is returned
     */
    public function getCredentials(Request $request)
    {
        return [
            'token' => $request->query->get('token')
        ];
    }

    /**
     * Return a UserInterface object based on the credentials.
     *
     * The *credentials* are the return value from getCredentials()
     *
     * You may throw an AuthenticationException if you wish. If you return
     * null, then a UsernameNotFoundException is thrown for you.
     *
     * @param mixed $credentials
     *
     * @return UserInterface|null
     *
     * @throws AuthenticationException
     *
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /** @var PortalUser|null $portalUser */
        $portalUser = $this->recoverPasswordService->getPortalUserByToken($credentials['token']);
        if (empty($portalUser)) {
            throw new AuthenticationException(sprintf('User with token %s not found.', $credentials['token']));
        }

        return $portalUser;
    }

    /**
     * Returns true if the credentials are valid.
     *
     * If false is returned, authentication will fail. You may also throw
     * an AuthenticationException if you wish to cause authentication to fail.
     *
     * The *credentials* are the return value from getCredentials()
     *
     * @param mixed $credentials
     *
     * @return bool
     *
     * @throws AuthenticationException
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return $user instanceof PortalUser;
    }

    /**
     * Called when authentication executed and was successful!
     *
     * This should return the Response sent back to the user, like a
     * RedirectResponse to the last page they visited.
     *
     * If you return null, the current request will continue, and the user
     * will be authenticated. This makes sense, for example, with an API.
     *
     * @param string $providerKey The provider (i.e. firewall) key
     *
     * @return Response|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $this->prefixService->setupRoutingPrefix();

        return new RedirectResponse($this->urlGenerator->generate('pimcore_portalengine_user_data'));
    }
}
