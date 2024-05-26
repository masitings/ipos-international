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

namespace Pimcore\Bundle\PortalEngineBundle\Controller;

use Pimcore\Bundle\PortalEngineBundle\Exception\OutputErrorException;
use Pimcore\Bundle\PortalEngineBundle\Form\LoginForm;
use Pimcore\Bundle\PortalEngineBundle\Form\RecoverPasswordForm;
use Pimcore\Bundle\PortalEngineBundle\Model\View\Notification;
use Pimcore\Bundle\PortalEngineBundle\Service\Content\HeadTitleService;
use Pimcore\Bundle\PortalEngineBundle\Service\Frontend\FrontendNotificationService;
use Pimcore\Bundle\PortalEngineBundle\Service\PortalConfig\PortalConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\Authentication\User\PasswordChangeableService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\Authentication\User\RecoverPasswordService;
use Pimcore\Model\DataObject\PortalUser;
use Pimcore\Model\DataObject\PortalUser\Listing;
use Pimcore\Tool;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/auth", condition="request.attributes.get('isPortalEngineSite')")
 */
class AuthController extends AbstractSiteController
{
    /**
     * @Route("/login",
     *     name="pimcore_portalengine_auth_login"
     * )
     */
    public function loginAction(Request $request, FormFactoryInterface $formFactory, HeadTitleService $headTitleService, TranslatorInterface $translator, PortalConfigService $portalConfigService, PasswordChangeableService $passwordChangeableService)
    {
        /** @var string $locale */
        $locale = $request->getPreferredLanguage(Tool::getValidLanguages());
        $translator->setLocale($locale);
        $request->setLocale($locale);

        $portalName = $portalConfigService->getPortalName();
        $headTitleService->setTitle($translator->trans('portal-engine.content.title.auth-login', ['%name%' => $portalName]));

        $loginForm = $formFactory->create(LoginForm::class);

        return $this->renderTemplate('@PimcorePortalEngine/auth/login.html.twig', [
            'form' => $loginForm->createView(),
            'loginFailed' => (bool)$request->query->get('loginFailed'),
            'portalName' => $portalName,
            'showRecoverPassword' => $passwordChangeableService->isPasswordChangeable()
        ]);
    }

    /**
     * @Route("/logout",
     *     name="pimcore_portalengine_auth_logout"
     * )
     */
    public function logoutAction(Request $request)
    {
    }

    /**
     * @Route("/recover-password",
     *     name="pimcore_portalengine_auth_recover_password"
     * )
     */
    public function recoverPasswordAction(Request $request, TranslatorInterface $translator, FormFactoryInterface $formFactory, FrontendNotificationService $frontendNotificationService, RecoverPasswordService $recoverPasswordService, PasswordChangeableService $passwordChangeableService, HeadTitleService $headTitleService)
    {
        if (!$passwordChangeableService->isPasswordChangeable()) {
            throw new NotFoundHttpException('password not changeable');
        }

        /** @var string $locale */
        $locale = $request->getPreferredLanguage(Tool::getValidLanguages());
        $translator->setLocale($locale);
        $request->setLocale($locale);

        $headTitleService->setTitle($translator->trans('portal-engine.content.title.auth-recover-password'));

        /** @var FormInterface $recoverPasswordForm */
        $recoverPasswordForm = $formFactory
            ->create(RecoverPasswordForm::class)
            ->handleRequest($request);

        if ($request->isMethod(Request::METHOD_POST) && $recoverPasswordForm->isSubmitted() && $recoverPasswordForm->isValid()) {
            try {
                /** @var string $userIdentifier */
                $userIdentifier = (string)$recoverPasswordForm->get('userIdentifier')->getData();
                if (empty($userIdentifier)) {
                    throw new OutputErrorException($translator->trans('portal-engine.auth.user-not-found'));
                }

                /** @var PortalUser|null $portalUser */
                $portalUser = (new Listing())
                    ->addConditionParam('email = ?', $userIdentifier)
                    ->setLimit(1)
                    ->current();

                if (!$portalUser) {
                    $portalUser = (new Listing())
                        ->addConditionParam('externalUserId = ?', $userIdentifier)
                        ->setLimit(1)
                        ->current();
                }

                if (!$portalUser) {
                    throw new OutputErrorException($translator->trans('portal-engine.auth.user-not-found'));
                }

                $recoverPasswordService
                    ->recoverPassword($portalUser);

                $frontendNotificationService
                    ->addNotification($translator->trans('portal-engine.auth.password-recover-email'), Notification::HTML_CLASS_SUCCESS);
            } catch (\Exception $e) {
                if ($e instanceof OutputErrorException) {
                    $frontendNotificationService->addNotification($e->getMessage(), Notification::HTML_CLASS_DANGER);
                }
            }
        }

        return $this->renderTemplate('@PimcorePortalEngine/auth/recover_password.html.twig', [
            'recoverPasswordForm' => $recoverPasswordForm->createView(),
            'notification' => $frontendNotificationService->getNotification()
        ]);
    }
}
