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
use Pimcore\Bundle\PortalEngineBundle\Form\ChangePasswordForm;
use Pimcore\Bundle\PortalEngineBundle\Form\ChangeUserDataForm;
use Pimcore\Bundle\PortalEngineBundle\Form\UploadAvatarForm;
use Pimcore\Bundle\PortalEngineBundle\Model\View\Notification;
use Pimcore\Bundle\PortalEngineBundle\Service\Document\LanguageVariantService;
use Pimcore\Bundle\PortalEngineBundle\Service\Frontend\FrontendNotificationService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\Authentication\User\ChangePasswordService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\Authentication\User\PasswordChangeableService;
use Pimcore\Bundle\PortalEngineBundle\Service\User\AvatarService;
use Pimcore\Model\DataObject\PortalUser;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("", condition="request.attributes.get('isPortalEngineSite')")
 */
class UserController extends AbstractSiteController
{
    /**
     * @Route("/{_portal_engine_prefix}user/data",
     *     name="pimcore_portalengine_user_data"
     * )
     */
    public function userDataAction(Request $request, TranslatorInterface $translator, FormFactoryInterface $formFactory, FrontendNotificationService $frontendNotificationService, ChangePasswordService $changePasswordService, PasswordChangeableService $passwordChangeableService, LanguageVariantService $languageVariantService, AvatarService $avatarService)
    {
        /** @var PortalUser $portalUser */
        $portalUser = $this->getUser();
        /** @var FormInterface $changePasswordForm */
        $changePasswordForm = $formFactory->create(ChangePasswordForm::class);
        /** @var FormInterface $changeUserDataForm */
        $changeUserDataForm = $formFactory
            ->create(ChangeUserDataForm::class, [
                'email' => $portalUser->getEmail(),
                'firstname' => $portalUser->getFirstname(),
                'lastname' => $portalUser->getLastname(),
                'preferredLanguage' => $portalUser->getPreferredLanguage(),
            ]);
        /** @var FormInterface $uploadAvatarForm */
        $uploadAvatarForm = $formFactory->create(UploadAvatarForm::class);

        if ($request->isMethod(Request::METHOD_POST)) {
            $changePasswordForm->handleRequest($request);
            $changeUserDataForm->handleRequest($request);
            $uploadAvatarForm->handleRequest($request);

            try {
                if ($changePasswordForm->isSubmitted() && $changePasswordForm->isValid()) {
                    /** @var string $password */
                    $password = (string)$changePasswordForm->get('password')->getData();
                    if (empty($password)) {
                        throw new OutputErrorException($translator->trans('portal-engine.auth.password-not-set'));
                    }

                    /** @var string $passwordRepeat */
                    $passwordRepeat = (string)$changePasswordForm->get('passwordRepeat')->getData();
                    if ($password !== $passwordRepeat) {
                        throw new OutputErrorException($translator->trans('portal-engine.auth.password-and-password-repeat-are-not-identical'));
                    }

                    $changePasswordService
                        ->changePassword($portalUser, $password);
                }
                if ($changeUserDataForm->isSubmitted() && $changeUserDataForm->isValid()) {
                    /** @var string $firstname */
                    $firstname = (string)$changeUserDataForm->get('firstname')->getData();
                    /** @var string $lastname */
                    $lastname = (string)$changeUserDataForm->get('lastname')->getData();

                    $portalUser
                        ->setFirstname($firstname)
                        ->setLastname($lastname);

                    if ($changeUserDataForm->has('preferredLanguage')) {
                        $portalUser->setPreferredLanguage($changeUserDataForm->get('preferredLanguage')->getData());
                    }

                    $portalUser->save();
                }
                if ($uploadAvatarForm->isSubmitted() && $uploadAvatarForm->isValid()) {
                    /**
                     * @var UploadedFile $avatar
                     */
                    $avatar = $uploadAvatarForm->getData()['avatar'];
                    $avatarService->updateAvatar($avatar);
                } else {
                    $frontendNotificationService
                        ->addNotification($translator->trans('portal-engine.user.data-changed'), Notification::HTML_CLASS_SUCCESS);
                }
            } catch (\Exception $e) {
                if ($e instanceof OutputErrorException) {
                    $frontendNotificationService->addNotification($e->getMessage(), Notification::HTML_CLASS_DANGER);
                } else {
                    $frontendNotificationService->addNotification($translator->trans('portal-engine.general-error'), Notification::HTML_CLASS_DANGER);
                }
            }
        }

        $preferredLanguageChoices = $languageVariantService->getPreferredLanguageChoices();

        return $this->renderTemplate('@PimcorePortalEngine/user/data.html.twig', [
            'changePasswordForm' => $changePasswordForm->createView(),
            'changeUserDataForm' => $changeUserDataForm->createView(),
            'uploadAvatarForm' => $uploadAvatarForm->createView(),
            'notification' => $frontendNotificationService->getNotification(),
            'displayPreferredLanguages' => sizeof($preferredLanguageChoices) > 0,
            'displayChangePassword' => $passwordChangeableService->isPasswordChangeable(),
            'hasAvatar' => !empty($portalUser->getAvatar()),
        ]);
    }

    /**
     * @Route("/__portal-engine/user/image",
     *     name="pimcore_portalengine_user_image"
     * )
     */
    public function imageAction(AvatarService $avatarService)
    {
        $avatarStream = $avatarService->getAvatarStream();
        $headers['Content-Type'] = 'image/png';
        $headers['Content-Length'] = fstat($avatarStream)['size'];

        return new StreamedResponse(function () use ($avatarStream) {
            fpassthru($avatarStream);
        }, 200, $headers);
    }

    /**
     * @Route("/__portal-engine/user/delete-avatar",
     *     name="pimcore_portalengine_user_delete_avatar"
     * )
     *
     * @throws \Exception
     */
    public function deleteAvatarAction(AvatarService $avatarService)
    {
        $avatarService->deleteAvatar();

        return $this->redirectToRoute('pimcore_portalengine_user_data');
    }
}
