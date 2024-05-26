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

use Pimcore\Bundle\PortalEngineBundle\Enum\Permission;
use Pimcore\Bundle\PortalEngineBundle\Event\Auth\LoginCheckPasswordEvent;
use Pimcore\Bundle\PortalEngineBundle\Event\Auth\LoginGetUserEvent;
use Pimcore\Bundle\PortalEngineBundle\Form\LoginForm;
use Pimcore\Bundle\PortalEngineBundle\Model\DataObject\PortalUserInterface;
use Pimcore\Bundle\PortalEngineBundle\Service\StatisticsTracker\Elasticsearch\PortalUserLoginTracker;
use Pimcore\Model\Site;
use Pimcore\Model\User;
use Pimcore\Tool\Authentication;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * Class GuardAuthenticator
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\Security\Authentication
 */
class GuardAuthenticator extends AbstractAuthenticator
{
    use TargetPathTrait;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var PortalUserLoginTracker
     */
    protected $portalUserLoginTracker;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param FormFactoryInterface $formFactory
     * @required
     */
    public function setFormFactory(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @param PortalUserLoginTracker $portalUserLoginTracker
     * @required
     */
    public function setPortalUserLoginTracker(PortalUserLoginTracker $portalUserLoginTracker)
    {
        $this->portalUserLoginTracker = $portalUserLoginTracker;
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

        $loginForm = $this->formFactory->create(LoginForm::class);
        $loginForm->handleRequest($request);

        return $loginForm->isSubmitted() && $loginForm->isValid();
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
        $loginForm = $this->formFactory->create(LoginForm::class);
        $loginForm->handleRequest($request);

        return $loginForm->getData();
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
        $event = new LoginGetUserEvent($credentials['username'], $credentials['password']);
        $this->eventDispatcher->dispatch($event);
        if ($event->getPortalUserResolved()) {
            $user = $event->getPortalUser();
        } else {
            $user = $userProvider->loadUserByUsername($credentials['username']);
        }

        if (empty($user)) {
            throw new AuthenticationException(sprintf('User with email %s not found.', $credentials['username']));
        }

        return $user;
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
        if (!$user instanceof PortalUserInterface) {
            throw new AuthenticationException('Invalid user');
        }

        $event = new LoginCheckPasswordEvent($user, $credentials['password']);
        $this->eventDispatcher->dispatch($event);

        if (is_bool($event->getLoginValid())) {
            $success = $event->getLoginValid();
        } elseif ($user->getUsePimcoreUserPassword() && $user->getPimcoreUser()) {
            $pimcoreUser = User::getById($user->getPimcoreUser());

            $success = Authentication::isValidUser($pimcoreUser) && Authentication::verifyPassword($pimcoreUser, $credentials['password']);
        } else {
            $success = $user->getClass()->getFieldDefinition('portalPassword')->verifyPassword($credentials['password'], $user);
        }

        if ($success) {
            $portalId = Site::getCurrentSite()->getRootId();
            $success = $this->permissionService->isAllowed($user, Permission::PORTAL_ACCESS . Permission::PERMISSION_DELIMITER . $portalId);
        }

        if (!$success) {
            throw new AuthenticationException('Password wrong');
        }

        return $success;
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
     *
     * @throws \Exception
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $this->portalUserLoginTracker->trackEvent(['user' => $token->getUser()]);

        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            if (!$this->isRestApiPath($targetPath)) {
                return new RedirectResponse($targetPath);
            }
        }

        return new RedirectResponse('/');
    }

    /**
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return GuardAuthenticator
     * @required
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): self
    {
        $this->eventDispatcher = $eventDispatcher;

        return $this;
    }
}
