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

namespace Pimcore\Bundle\PortalEngineBundle\EventSubscriber;

use Pimcore\Bundle\PortalEngineBundle\Exception\PublicShareExpiredException;
use Pimcore\Bundle\PortalEngineBundle\Service\Document\PrefixService;
use Pimcore\Bundle\PortalEngineBundle\Service\PortalConfig\PortalConfigService;
use Pimcore\Http\Request\Resolver\DocumentResolver;
use Pimcore\Http\Request\Resolver\EditmodeResolver;
use Pimcore\Localization\LocaleServiceInterface;
use Pimcore\Model\Document\Service;
use Pimcore\Model\Site;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Class IndexUpdateListener
 *
 * @package Pimcore\Bundle\PortalEngineBundle\EventListener
 */
class ResponseExceptionSubscriber implements EventSubscriberInterface
{
    /**
     * @var PortalConfigService
     */
    protected $portalConfigService;

    /**
     * @var Environment
     */
    protected $twig;

    /**
     * @var DocumentResolver
     */
    protected $documentResolver;

    /**
     * @var EditmodeResolver
     */
    protected $editmodeResolver;

    /**
     * @var PrefixService
     */
    protected $prefixService;

    /**
     * @var Service
     */
    protected $documentService;

    /**
     * @var LocaleServiceInterface
     */
    protected $localeService;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * ResponseExceptionSubscriber constructor.
     *
     * @param PortalConfigService $portalConfigService
     * @param Environment $twig
     * @param DocumentResolver $documentResolver
     * @param EditmodeResolver $editmodeResolver
     * @param PrefixService $prefixService
     * @param Service $documentService
     */
    public function __construct(
        PortalConfigService $portalConfigService,
        Environment $twig,
        DocumentResolver $documentResolver,
        EditmodeResolver $editmodeResolver,
        PrefixService $prefixService,
        Service $documentService,
        LocaleServiceInterface $localeService,
        TranslatorInterface $translator
    ) {
        $this->portalConfigService = $portalConfigService;
        $this->twig = $twig;
        $this->documentResolver = $documentResolver;
        $this->editmodeResolver = $editmodeResolver;
        $this->prefixService = $prefixService;
        $this->documentService = $documentService;
        $this->localeService = $localeService;
        $this->translator = $translator;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', -5],
        ];
    }

    /**
     * @param ExceptionEvent $exceptionEvent
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function onKernelException(ExceptionEvent $exceptionEvent)
    {
        if ($this->portalConfigService->isPortalEngineSite()) {
            $throwable = $exceptionEvent->getThrowable();
            if ($throwable instanceof AccessDeniedHttpException) {
                $this->setErrorPageResponse(
                    $exceptionEvent,
                    '@PimcorePortalEngine/error_page/access_denied.html.twig',
                    true
                );
            } elseif ($throwable instanceof PublicShareExpiredException) {
                $this->setErrorPageResponse(
                    $exceptionEvent,
                    '@PimcorePortalEngine/error_page/public_share_expired.html.twig',
                    true,
                    [
                        'publicShare' => $throwable->getPublicShare()
                    ]
                );
            } elseif ($throwable instanceof NotFoundHttpException) {
                $this->setErrorPageResponse(
                    $exceptionEvent,
                    '@PimcorePortalEngine/error_page/404.html.twig',
                    true
                );
            } else {
                $this->setErrorPageResponse(
                    $exceptionEvent,
                    '@PimcorePortalEngine/error_page/exception.html.twig',
                    false
                );
            }
        }
    }

    /**
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    protected function setErrorPageResponse(ExceptionEvent $exceptionEvent, string $template, bool $displayInDebugMode, array $additionalViewParams = [])
    {
        if (!$displayInDebugMode && \Pimcore::inDebugMode()) {
            return;
        }

        $this->prefixService->setupRoutingPrefix();

        if (!$document = $this->documentResolver->getDocument($exceptionEvent->getRequest())) {
            $site = Site::getCurrentSite();
            $document = $this->documentService->getNearestDocumentByPath($site->getRootDocument()->getRealFullPath() . $exceptionEvent->getRequest()->getPathInfo());
            if ($lang = $document->getProperty('language')) {
                $this->localeService->setLocale($lang);
                $this->translator->setLocale($lang);
            }
            $this->documentResolver->setDocument($exceptionEvent->getRequest(), $document);
        }

        $exceptionEvent->setResponse(

             new Response(
                $this->twig->render($template, array_merge([
                    'document' => $document,
                    'editmode' => $this->editmodeResolver->isEditmode($exceptionEvent->getRequest()),
                    'inDebugMode' => \Pimcore::inDebugMode(),
                    'debugInfo' => $exceptionEvent->getThrowable()->getMessage() . '<hr>' . $exceptionEvent->getThrowable()->getTraceAsString()
                ], $additionalViewParams))
            )
        );
    }
}
