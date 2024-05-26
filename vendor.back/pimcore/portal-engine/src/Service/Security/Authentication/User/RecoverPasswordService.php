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

use Carbon\Carbon;
use Defuse\Crypto\Crypto;
use Pimcore\Bundle\PortalEngineBundle\Event\Auth\RecoverPasswordEmailTemplateEvent;
use Pimcore\Bundle\PortalEngineBundle\Service\PortalConfig\PortalConfigService;
use Pimcore\Model\DataObject\PortalUser;
use Pimcore\Tool;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Class RecoverPasswordService
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\Security\Authentication\User
 */
class RecoverPasswordService
{
    /** @var UrlGeneratorInterface */
    protected $urlGenerator;
    /** @var Environment $templating */
    protected $templating;
    /** @var TranslatorInterface */
    protected $translator;
    /** @var EventDispatcherInterface */
    protected $eventDispatcher;
    /** @var PortalConfigService $portalConfigService */
    protected $portalConfigService;

    /**
     * RecoverPasswordService constructor.
     *
     * @param UrlGeneratorInterface $urlGenerator
     * @param Environment $templating
     * @param TranslatorInterface $translator
     * @param EventDispatcherInterface $eventDispatcher
     * @param PortalConfigService $portalConfigService
     */
    public function __construct(UrlGeneratorInterface $urlGenerator, Environment $templating, TranslatorInterface $translator, EventDispatcherInterface $eventDispatcher, PortalConfigService $portalConfigService)
    {
        $this->urlGenerator = $urlGenerator;
        $this->templating = $templating;
        $this->translator = $translator;
        $this->eventDispatcher = $eventDispatcher;
        $this->portalConfigService = $portalConfigService;
    }

    /**
     * @param PortalUser $portalUser
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function recoverPassword(PortalUser $portalUser)
    {
        try {
            /** @var string $passwordRecoverUrl */
            $passwordRecoverUrl = $this->urlGenerator->generate('pimcore_portalengine_auth_login', ['token' => $this->getTokenByPortalUser($portalUser)], UrlGeneratorInterface::ABSOLUTE_URL);
            /** @var string|null $userName */
            $userName = ($portalUser->getFirstname() || $portalUser->getLastname())
                ? ' ' . trim($portalUser->getFirstname() . ' ' . $portalUser->getLastname())
                : null;
            $portalName = $this->portalConfigService->getPortalName();
            /** @var string $htmlBody */
            $htmlBody = $this->templating->render('@PimcorePortalEngine/email/password-forgotten-email.html.twig', [
                'userName' => $userName,
                'passwordRecoverUrl' => $passwordRecoverUrl,
                'portalName' => $portalName
            ]);

            /** @var RecoverPasswordEmailTemplateEvent $event */
            $event = new RecoverPasswordEmailTemplateEvent($htmlBody, $userName, $passwordRecoverUrl);
            $this->eventDispatcher->dispatch($event);

            (Tool::getMail([$portalUser->getEmail()], $this->translator->trans('portal-engine.email.password-forgotten.email-subject', ['%name%' => $portalName])))
                ->setIgnoreDebugMode(true)
                ->setBodyHtml($event->getHtmlBody())
                ->send();
        } catch (\Exception $e) {
            throw new \Exception('recover password failed');
        }

        return $this;
    }

    /**
     * @param PortalUser $portalUser
     *
     * @return string
     */
    public function getTokenByPortalUser(PortalUser $portalUser): string
    {
        return $this->generateToken($portalUser->getEmail());
    }

    /**
     * @param string $token
     *
     * @return PortalUser|null
     */
    public function getPortalUserByToken(string $token)
    {
        /** @var PortalUser|null $portalUser */
        $portalUser = null;

        try {
            /** @var array $decrypted */
            $decrypted = $this->tokenDecrypt($token);
            list($timestamp, $userEmail) = $decrypted;

            if (!Carbon::createFromTimestamp($timestamp)->isBetween(Carbon::now(), Carbon::now()->subHours(24))) {
                throw new \Exception('timestamp is older than 24 hours or in future');
            }

            $portalUser = PortalUser::getByEmail($userEmail)->current();
        } catch (\Exception $e) {
            //nothing to do
        }

        return $portalUser;
    }

    /**
     * @param $username
     *
     * @return string
     *
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     */
    private static function generateToken($username)
    {
        $secret = \Pimcore::getContainer()->getParameter('secret');

        $data = time() - 1 . '|' . $username;
        $token = Crypto::encryptWithPassword($data, $secret);

        return $token;
    }

    /**
     * @param string $token
     *
     * @return array
     */
    private function tokenDecrypt($token)
    {
        $secret = \Pimcore::getContainer()->getParameter('secret');
        $decrypted = Crypto::decryptWithPassword($token, $secret);

        return explode('|', $decrypted);
    }
}
