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

use Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse;
use Pimcore\Bundle\PortalEngineBundle\Service\PortalConfig\PortalConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\PermissionService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

/**
 * Class AbstractAuthenticator
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\Security\Authentication
 */
abstract class AbstractAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;

    /**
     * @var PortalConfigService
     */
    protected $portalConfigService;

    /**
     * @var PermissionService
     */
    protected $permissionService;

    /**
     * AbstractAuthenticator constructor.
     *
     * @param UrlGeneratorInterface $urlGenerator
     * @param PortalConfigService $portalConfigService
     * @param PermissionService $permissionService
     */
    public function __construct(UrlGeneratorInterface $urlGenerator, PortalConfigService $portalConfigService, PermissionService $permissionService)
    {
        $this->urlGenerator = $urlGenerator;
        $this->portalConfigService = $portalConfigService;
        $this->permissionService = $permissionService;
    }

    /**
     * Returns a response that directs the user to authenticate.
     *
     * This is called when an anonymous request accesses a resource that
     * requires authentication. The job of this method is to return some
     * response that "helps" the user start into the authentication process.
     *
     * Examples:
     *
     * - For a form login, you might redirect to the login page
     *
     *     return new RedirectResponse('/login');
     *
     * - For an API token authentication system, you return a 401 response
     *
     *     return new Response('Auth header required', 401);
     *
     * @return Response
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $url = $this->urlGenerator->generate('pimcore_portalengine_auth_login');

        if ($this->isRestApiPath($request->getPathInfo())) {
            return new JsonResponse([
                'redirectUrl' => $url
            ]);
        }

        return new RedirectResponse($url);
    }

    /**
     * Called when authentication executed, but failed (e.g. wrong username password).
     *
     * This should return the Response sent back to the user, like a
     * RedirectResponse to the login page or a 401 response.
     *
     * If you return null, the request will continue, but the user will
     * not be authenticated. This is probably not what you want to do.
     *
     * @return Response|null
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $url = $this->urlGenerator->generate('pimcore_portalengine_auth_login', ['loginFailed' => true]);

        return new RedirectResponse($url);
    }

    /**
     * Does this method support remember me cookies?
     *
     * Remember me cookie will be set if *all* of the following are met:
     *  A) This method returns true
     *  B) The remember_me key under your firewall is configured
     *  C) The "remember me" functionality is activated. This is usually
     *      done by having a _remember_me checkbox in your form, but
     *      can be configured by the "always_remember_me" and "remember_me_parameter"
     *      parameters under the "remember_me" firewall key
     *  D) The onAuthenticationSuccess method returns a Response object
     *
     * @return bool
     */
    public function supportsRememberMe()
    {
        return true;
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    protected function isRestApiPath(string $path): bool
    {
        return strpos($path, '/_portal-engine/api') !== false;
    }
}
